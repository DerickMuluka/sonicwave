<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf'] ?? ''))) json_response(['error' => 'Invalid session token'], 403);

if (!isset($_FILES['audio']) || $_FILES['audio']['error'] !== UPLOAD_ERR_OK) json_response(['error' => 'Audio file is required'], 422);
$audio = $_FILES['audio'];
if ($audio['size'] > MAX_AUDIO_SIZE) json_response(['error' => 'File exceeds 40MB limit'], 413);

$ext = strtolower(pathinfo($audio['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ALLOWED_AUDIO, true)) json_response(['error' => 'Unsupported format. Allowed: ' . implode(', ', ALLOWED_AUDIO)], 415);

// Relaxed MIME sniff — many valid MP3s report as application/octet-stream
$finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : null;
if ($finfo) {
    $mime = finfo_file($finfo, $audio['tmp_name']);
    finfo_close($finfo);
    $okMimes = [
        'audio/mpeg','audio/mp3','audio/wav','audio/x-wav','audio/wave','audio/ogg','audio/mp4','audio/m4a','audio/x-m4a',
        'audio/flac','audio/x-flac','audio/aac','audio/webm','audio/opus',
        'application/octet-stream','application/mp4','video/mp4','video/webm','binary/octet-stream'
    ];
    if (!in_array($mime, $okMimes, true)) {
        // Accept if extension is allowed AND mime is vaguely media
        if (!preg_match('#^(audio|video|application)#i', $mime)) {
            json_response(['error' => 'File content does not match an audio file (' . $mime . ')'], 415);
        }
    }
}

if (!is_dir(AUDIO_PATH)) @mkdir(AUDIO_PATH, 0755, true);
$fname = random_filename($ext);
if (!move_uploaded_file($audio['tmp_name'], AUDIO_PATH . '/' . $fname)) json_response(['error' => 'Could not save file'], 500);

$coverRel = null;
if (!empty($_FILES['cover']['name']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
    $c = $_FILES['cover'];
    $cext = strtolower(pathinfo($c['name'], PATHINFO_EXTENSION));
    if ($c['size'] <= MAX_COVER_SIZE && in_array($cext, ALLOWED_IMAGE, true)) {
        if (!is_dir(COVER_PATH)) @mkdir(COVER_PATH, 0755, true);
        $cname = random_filename($cext);
        if (move_uploaded_file($c['tmp_name'], COVER_PATH . '/' . $cname)) $coverRel = 'uploads/covers/' . $cname;
    }
}

$title = clean_str($_POST['title'] ?? pathinfo($audio['name'], PATHINFO_FILENAME), 200);
$artist = clean_str($_POST['artist'] ?? 'Unknown Artist', 200);
$album = clean_str($_POST['album'] ?? '', 200);
$genre = clean_str($_POST['genre'] ?? 'Unknown', 80);
$duration = max(0, (int)($_POST['duration'] ?? 0));

$mood = ai_detect_mood($title . ' ' . $artist . ' ' . $genre);
$desc = ai_describe_track($title, $artist, $genre);

$pdo = db();
$stmt = $pdo->prepare("INSERT INTO tracks (user_id, title, artist, album, genre, duration, file_path, cover_path, source, embed_type, mood, description, is_approved)
                       VALUES (?,?,?,?,?,?,?,?,'upload','audio',?,?,1)");
$stmt->execute([
    current_user()['id'], $title, $artist, $album ?: null, $genre,
    $duration, 'uploads/audio/' . $fname, $coverRel, $mood, $desc
]);
$id = (int)$pdo->lastInsertId();
audit_log('track.upload', 'track', $id, ['title' => $title]);
json_response(['ok' => true, 'id' => $id]);