<?php
define('APP_NAME', 'SonicWave');
define('APP_VERSION', '3.1.0');

/**
 * BASE_URL — hardcode per environment.
 * XAMPP:         'http://localhost/sonicwave'
 * InfinityFree:  'http://yoursite.rf.gd'
 */
define('BASE_URL', 'http://localhost/sonicwave');

define('MAX_AUDIO_SIZE', 40 * 1024 * 1024);
define('MAX_COVER_SIZE', 5 * 1024 * 1024);
define('ALLOWED_AUDIO', ['mp3','wav','ogg','m4a','flac','aac','webm','opus']);
define('ALLOWED_IMAGE', ['jpg','jpeg','png','webp','gif']);

define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('AUDIO_PATH', UPLOAD_PATH . '/audio');
define('COVER_PATH', UPLOAD_PATH . '/covers');
define('VIDEO_PATH', UPLOAD_PATH . '/video');   // downloaded video
define('CACHE_PATH', UPLOAD_PATH . '/cache');   // converted files

// External binaries
define('BIN_PATH',    ROOT_PATH . '/bin');
define('YTDLP_BIN',   BIN_PATH . '/yt-dlp.exe');   // Change to /yt-dlp on Linux
define('FFMPEG_BIN',  BIN_PATH . '/ffmpeg.exe');   // Change to /ffmpeg on Linux
define('FFPROBE_BIN', BIN_PATH . '/ffprobe.exe');  // Change to /ffprobe on Linux

// Feature flags (auto-detected at runtime)
define('YTDLP_ENABLED',   is_file(YTDLP_BIN));
define('FFMPEG_ENABLED',  is_file(FFMPEG_BIN));