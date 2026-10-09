<?php
function json_response($data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
function json_input(): array {
    $raw = file_get_contents('php://input');
    if (!$raw) return $_POST;
    $j = json_decode($raw, true);
    return is_array($j) ? $j : $_POST;
}
function format_duration(int $s): string {
    return sprintf('%d:%02d', intdiv($s, 60), $s % 60);
}
function random_filename(string $ext): string {
    return bin2hex(random_bytes(16)) . '.' . strtolower($ext);
}
function asset(string $path): string {
    return BASE_URL . '/assets/' . ltrim($path, '/') . '?v=' . APP_VERSION;
}
function url(string $path = ''): string {
    return BASE_URL . '/' . ltrim($path, '/');
}
function audit_log(string $action, ?string $targetType = null, ?int $targetId = null, array $meta = []): void {
    try {
        db()->prepare("INSERT INTO audit_log (actor_id, action, target_type, target_id, meta, ip) VALUES (?,?,?,?,?,?)")
            ->execute([
                current_user()['id'] ?? null, $action, $targetType, $targetId,
                $meta ? json_encode($meta) : null,
                $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
    } catch (Throwable $e) {}
}
function platformBadge(string $source): string {
    $labels = ['upload'=>'Upload','youtube'=>'YouTube','spotify'=>'Spotify','soundcloud'=>'SoundCloud','vimeo'=>'Vimeo','apple'=>'Apple Music','deezer'=>'Deezer','bandcamp'=>'Bandcamp','direct'=>'Direct'];
    $label = $labels[$source] ?? ucfirst($source);
    return '<span class="source-badge source-' . htmlspecialchars($source, ENT_QUOTES) . '">' . htmlspecialchars($label) . '</span>';
}
function cover_url(?string $path): ?string {
    if (!$path) return null;
    return str_starts_with($path, 'http') ? $path : url($path);
}

function track_payload(array $r, array $likedIds = []): array {
    $streamUrl = null;
    $downloadUrl = null;
    $embedUrl = null;

    $source = $r['source'];
    $id = (int)$r['id'];

    // Only expose stream_url if there's an actual local file
    $hasLocal = !empty($r['file_path']) && is_file(ROOT_PATH . '/' . ltrim($r['file_path'], '/'));
    if (!$hasLocal && !empty($r['cached_path']) && is_file(ROOT_PATH . '/' . ltrim($r['cached_path'], '/'))) {
        $hasLocal = true;
    }

    if ($hasLocal) {
        $streamUrl = url('api/tracks/stream.php?id=' . $id);
    } else if (in_array($source, ['upload','direct'], true)) {
        $streamUrl = url('api/tracks/stream.php?id=' . $id);
    } else {
        // External — rely on embed
        $embedUrl = $r['source_url'];
    }

    $downloadUrl = url('api/media/download.php?id=' . $id);

    return [
        'id'           => $id,
        'title'        => $r['title'],
        'artist'       => $r['artist'],
        'album'        => $r['album'] ?? null,
        'genre'        => $r['genre'],
        'duration'     => (int)$r['duration'],
        'cover_path'   => cover_url($r['cover_path']),
        'stream_url'   => $streamUrl,
        'download_url' => $downloadUrl,
        'embed_url'    => $embedUrl,
        'source'       => $source,
        'source_url'   => $r['source_url'],
        'external_id'  => $r['external_id'],
        'embed_type'   => $r['embed_type'],
        'plays'        => (int)$r['plays'],
        'likes'        => (int)$r['likes'],
        'liked'        => in_array($id, array_map('intval', $likedIds), true),
        'created_at'   => $r['created_at'],
        'mood'         => $r['mood'] ?? null,
        'has_local'    => $hasLocal,
        'user_id'      => (int)$r['user_id'],
    ];
}