<?php
/**
 * Wrappers around yt-dlp and ffmpeg.
 * All functions check binary availability before executing.
 */

function media_tools_available(): array {
    return [
        'ytdlp'  => YTDLP_ENABLED,
        'ffmpeg' => FFMPEG_ENABLED,
    ];
}

/**
 * Run a shell command safely and capture output.
 */
function run_binary(string $binary, array $args, int $timeout = 300): array {
    if (!is_file($binary)) {
        return ['ok' => false, 'error' => 'Binary not found: ' . basename($binary), 'output' => ''];
    }

    $cmd = escapeshellarg($binary);
    foreach ($args as $a) {
        $cmd .= ' ' . escapeshellarg((string)$a);
    }
    // Redirect stderr to stdout
    $cmd .= ' 2>&1';

    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $process = proc_open($cmd, $descriptors, $pipes);
    if (!is_resource($process)) {
        return ['ok' => false, 'error' => 'Could not start process', 'output' => ''];
    }
    fclose($pipes[0]);
    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);

    $output = '';
    $start = time();
    while (true) {
        $status = proc_get_status($process);
        $output .= stream_get_contents($pipes[1]);
        $output .= stream_get_contents($pipes[2]);
        if (!$status['running']) break;
        if (time() - $start > $timeout) {
            proc_terminate($process, 9);
            fclose($pipes[1]); fclose($pipes[2]);
            proc_close($process);
            return ['ok' => false, 'error' => 'Timeout after ' . $timeout . 's', 'output' => $output];
        }
        usleep(100000);
    }
    fclose($pipes[1]); fclose($pipes[2]);
    $exitCode = proc_close($process);

    return [
        'ok'     => $exitCode === 0,
        'error'  => $exitCode === 0 ? null : 'Exit code ' . $exitCode,
        'output' => $output,
    ];
}

/**
 * Download media from a URL using yt-dlp.
 * @param string $url Source URL
 * @param string $outDir Destination directory
 * @param string $format 'audio' or 'video'
 * @param string $quality For video: 'best','720','480','360'
 * @return array {ok, path, filename, error}
 */
function ytdlp_download(string $url, string $outDir, string $format = 'video', string $quality = 'best'): array {
    if (!YTDLP_ENABLED) {
        return ['ok' => false, 'error' => 'yt-dlp not installed on this server'];
    }
    if (!is_dir($outDir)) @mkdir($outDir, 0755, true);

    $template = $outDir . '/%(id)s.%(ext)s';

    $args = [
        '--no-playlist',
        '--no-warnings',
        '--restrict-filenames',
        '-o', $template,
        '--print', 'after_move:filepath',
    ];

    if ($format === 'audio') {
        // Best audio, extract to mp3 if ffmpeg available, else keep original
        if (FFMPEG_ENABLED) {
            $args[] = '-x';
            $args[] = '--audio-format'; $args[] = 'mp3';
            $args[] = '--audio-quality'; $args[] = '0';
            $args[] = '--ffmpeg-location'; $args[] = FFMPEG_BIN;
        } else {
            $args[] = '-f'; $args[] = 'bestaudio[ext=m4a]/bestaudio';
        }
    } else {
        // Video
        $q = $quality === 'best' ? 'bestvideo[ext=mp4]+bestaudio[ext=m4a]/best[ext=mp4]/best'
            : "bestvideo[height<={$quality}][ext=mp4]+bestaudio[ext=m4a]/best[height<={$quality}]/best";
        $args[] = '-f'; $args[] = $q;
        $args[] = '--merge-output-format'; $args[] = 'mp4';
        if (FFMPEG_ENABLED) {
            $args[] = '--ffmpeg-location'; $args[] = FFMPEG_BIN;
        }
    }

    $args[] = $url;

    $result = run_binary(YTDLP_BIN, $args, 600);
    if (!$result['ok']) {
        return ['ok' => false, 'error' => 'Download failed: ' . ($result['error'] ?? 'unknown'), 'output' => $result['output']];
    }

    // yt-dlp prints final path on the last line
    $lines = array_filter(array_map('trim', explode("\n", $result['output'])));
    $finalPath = null;
    foreach (array_reverse($lines) as $line) {
        if (is_file($line)) { $finalPath = $line; break; }
    }
    if (!$finalPath) {
        // Fallback: scan directory for newest file
        $files = glob($outDir . '/*');
        if ($files) {
            usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
            $finalPath = $files[0];
        }
    }
    if (!$finalPath || !is_file($finalPath)) {
        return ['ok' => false, 'error' => 'Downloaded file not found', 'output' => $result['output']];
    }

    return [
        'ok'       => true,
        'path'     => $finalPath,
        'filename' => basename($finalPath),
        'size'     => filesize($finalPath),
        'output'   => $result['output'],
    ];
}

/**
 * Convert a media file to another format using ffmpeg.
 * @param string $inputPath Absolute path to source file
 * @param string $targetFormat 'mp3','m4a','wav','ogg','aac','mp4','webm','gif'
 * @return array {ok, path, error, output}
 */
function ffmpeg_convert(string $inputPath, string $targetFormat): array {
    if (!FFMPEG_ENABLED) {
        return ['ok' => false, 'error' => 'FFmpeg not installed on this server'];
    }
    if (!is_file($inputPath)) {
        return ['ok' => false, 'error' => 'Source file not found'];
    }

    if (!is_dir(CACHE_PATH)) @mkdir(CACHE_PATH, 0755, true);

    $targetFormat = strtolower($targetFormat);
    $allowed = ['mp3','m4a','wav','ogg','aac','flac','mp4','webm'];
    if (!in_array($targetFormat, $allowed, true)) {
        return ['ok' => false, 'error' => 'Unsupported target format'];
    }

    $outName = pathinfo($inputPath, PATHINFO_FILENAME) . '_' . bin2hex(random_bytes(4)) . '.' . $targetFormat;
    $outPath = CACHE_PATH . '/' . $outName;

    // Determine if target is audio or video
    $audioFormats = ['mp3','m4a','wav','ogg','aac','flac'];
    $isAudio = in_array($targetFormat, $audioFormats, true);

    $args = [
        '-i', $inputPath,
        '-y',
        '-hide_banner',
        '-loglevel', 'error',
    ];

    if ($isAudio) {
        // Strip video, encode audio
        $args[] = '-vn';
        if ($targetFormat === 'mp3') {
            $args[] = '-c:a'; $args[] = 'libmp3lame'; $args[] = '-b:a'; $args[] = '192k';
        } elseif ($targetFormat === 'm4a') {
            $args[] = '-c:a'; $args[] = 'aac'; $args[] = '-b:a'; $args[] = '192k';
        } elseif ($targetFormat === 'wav') {
            $args[] = '-c:a'; $args[] = 'pcm_s16le';
        } elseif ($targetFormat === 'ogg') {
            $args[] = '-c:a'; $args[] = 'libvorbis'; $args[] = '-q:a'; $args[] = '5';
        } elseif ($targetFormat === 'aac') {
            $args[] = '-c:a'; $args[] = 'aac'; $args[] = '-b:a'; $args[] = '192k';
        } elseif ($targetFormat === 'flac') {
            $args[] = '-c:a'; $args[] = 'flac';
        }
    } else {
        // Video container conversion
        if ($targetFormat === 'mp4') {
            $args[] = '-c:v'; $args[] = 'libx264'; $args[] = '-preset'; $args[] = 'fast'; $args[] = '-crf'; $args[] = '23';
            $args[] = '-c:a'; $args[] = 'aac'; $args[] = '-b:a'; $args[] = '160k';
            $args[] = '-movflags'; $args[] = '+faststart';
        } elseif ($targetFormat === 'webm') {
            $args[] = '-c:v'; $args[] = 'libvpx-vp9'; $args[] = '-crf'; $args[] = '30'; $args[] = '-b:v'; $args[] = '0';
            $args[] = '-c:a'; $args[] = 'libopus'; $args[] = '-b:a'; $args[] = '128k';
        }
    }

    $args[] = $outPath;

    $result = run_binary(FFMPEG_BIN, $args, 900);
    if (!$result['ok'] || !is_file($outPath)) {
        return ['ok' => false, 'error' => 'Conversion failed: ' . ($result['error'] ?? 'unknown'), 'output' => $result['output']];
    }

    return [
        'ok'     => true,
        'path'   => $outPath,
        'format' => $targetFormat,
        'size'   => filesize($outPath),
    ];
}

/**
 * Probe a media file to get duration/format info.
 */
function ffprobe_info(string $inputPath): array {
    if (!FFMPEG_ENABLED || !is_file($inputPath)) return [];
    $result = run_binary(FFPROBE_BIN, [
        '-v', 'error',
        '-show_entries', 'format=duration,bit_rate,format_name',
        '-of', 'json',
        $inputPath,
    ], 30);
    if (!$result['ok']) return [];
    $j = json_decode($result['output'], true);
    return $j['format'] ?? [];
}