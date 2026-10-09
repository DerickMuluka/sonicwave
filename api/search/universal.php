<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();

$q = clean_str($_GET['q'] ?? '', 120);
if (strlen($q) < 2) json_response(['results' => [], 'local' => [], 'platform' => null]);

$pdo = db();
$uid = current_user()['id'];

$like = '%' . $q . '%';
$stmt = $pdo->prepare("
    SELECT t.*, (SELECT COUNT(*) FROM likes l WHERE l.user_id = ? AND l.track_id = t.id) AS liked
    FROM tracks t
    WHERE t.is_approved = 1
      AND (t.title LIKE ? OR t.artist LIKE ? OR t.album LIKE ? OR t.description LIKE ?)
    ORDER BY
      CASE WHEN t.title LIKE ? THEN 1
           WHEN t.artist LIKE ? THEN 2
           ELSE 3 END,
      t.plays DESC
    LIMIT 50
");
$stmt->execute([$uid, $like, $like, $like, $like, $like, $like]);
$localRows = $stmt->fetchAll();

$local = [];
foreach ($localRows as $r) {
    $p = track_payload($r);
    $p['liked'] = (bool)$r['liked'];
    $local[] = $p;
}

$platformMatch = null;
$urlCandidate = $q;
if (!preg_match('#^https?://#', $urlCandidate)) {
    if (preg_match('#^(?:www\.)?(youtube\.com|youtu\.be|vimeo\.com|soundcloud\.com|spotify\.com|deezer\.com|music\.apple\.com|.+\.bandcamp\.com)#i', $urlCandidate)) {
        $urlCandidate = 'https://' . $urlCandidate;
    }
}
if (preg_match('#^https?://#', $urlCandidate)) {
    $platformMatch = [
        'url' => $urlCandidate,
        'message' => 'This looks like a media URL. Click to import it.',
    ];
}

json_response([
    'query'    => $q,
    'local'    => $local,
    'count'    => count($local),
    'platform' => $platformMatch,
]);