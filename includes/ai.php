<?php
/**
 * SonicWave AI engine — mood-aware track scoring.
 * Ensures each mood produces a DISTINCT queue.
 */

function ai_detect_mood(string $text): string {
    $map = [
        'energetic' => ['workout','gym','pump','dance','party','edm','club','remix','trap','bass','drop','hype','rap','drill','phonk','hardstyle','techno'],
        'chill'     => ['chill','lofi','lo-fi','relax','calm','study','sleep','ambient','acoustic','soft','mellow','easy','smooth','jazz','coffee'],
        'happy'     => ['happy','sunshine','joy','smile','feel good','upbeat','pop','summer','dance pop','cheerful','sunny','groove','funk'],
        'sad'       => ['sad','cry','lonely','heartbreak','melancholy','blues','tears','missing','gone','lost','rain','slow','piano','emotional'],
        'focus'     => ['focus','study','concentrate','work','instrumental','classical','piano','beats to','lofi hip hop','ambient','deep'],
        'romantic'  => ['love','romance','date','heart','valentine','wedding','kiss','baby','darling','soul','slow jam'],
    ];
    $low = mb_strtolower($text);
    $scores = array_fill_keys(array_keys($map), 0);
    foreach ($map as $mood => $keys) {
        foreach ($keys as $k) {
            if (str_contains($low, $k)) $scores[$mood] += 2;
        }
    }
    arsort($scores);
    $top = array_key_first($scores);
    return $scores[$top] > 0 ? $top : 'neutral';
}

function ai_describe_track(string $title, string $artist, string $genre): string {
    $t = [
        "A fresh take on %s from %s — polished production and memorable hooks.",
        "%s brings their signature sound to %s, crisp and unmistakably modern.",
        "An immersive %s track by %s — perfect for repeat listening.",
        "%s at their best: %s delivers a track that stays with you.",
    ];
    return sprintf($t[array_rand($t)], strtolower($genre), $artist);
}

/**
 * Compute a numeric score for how well a track fits a specific mood.
 * Higher = better match. Negative values are possible for hard mismatches.
 */
function ai_score_track_for_mood(array $track, string $mood): int {
    $score = 0;

    $title  = mb_strtolower(($track['title'] ?? '') . ' ' . ($track['artist'] ?? ''));
    $genre  = $track['genre'] ?? '';
    $stored = $track['mood'] ?? '';

    // ── 1. Stored mood (from AI tagger) — strongest signal
    if ($stored === $mood) $score += 100;
    elseif ($stored && $stored !== 'neutral' && $stored !== $mood) $score -= 40; // penalize wrong explicit mood

    // ── 2. Genre affinity per mood — strong signal
    $genreMap = [
        'energetic' => ['Hip-Hop','Electronic','Rock','Dance','Pop','Rap'],
        'chill'     => ['Lo-Fi','Jazz','Classical','R&B','Ambient','Reggae'],
        'happy'     => ['Pop','Afrobeat','Reggae','Country','Dance','Funk'],
        'sad'       => ['R&B','Soul','Blues','Classical','Folk'],
        'focus'     => ['Classical','Lo-Fi','Ambient','Jazz','Electronic'],
        'romantic'  => ['R&B','Soul','Jazz','Pop','Classical'],
    ];
    $dislikeMap = [
        'energetic' => ['Classical','Ambient'],
        'chill'     => ['Hip-Hop','Electronic','Rap'],
        'happy'     => ['Blues','Metal'],
        'sad'       => ['Dance','Electronic'],
        'focus'     => ['Rap','Metal'],
        'romantic'  => ['Rap','Metal'],
    ];
    if ($genre && in_array($genre, $genreMap[$mood] ?? [], true)) $score += 50;
    if ($genre && in_array($genre, $dislikeMap[$mood] ?? [], true)) $score -= 30;

    // ── 3. Keyword match on title/artist
    $keywords = [
        'energetic' => ['remix','beat','bass','drop','fire','lit','hype','turbo'],
        'chill'     => ['lofi','chill','relax','smooth','slow','sleep','coffee'],
        'happy'     => ['sun','shine','good','happy','love','smile','groove'],
        'sad'       => ['rain','tears','goodbye','alone','miss','sorry'],
        'focus'     => ['study','focus','deep','beat','instrumental'],
        'romantic'  => ['love','baby','darling','kiss','heart'],
    ];
    foreach ($keywords[$mood] ?? [] as $kw) {
        if (str_contains($title, $kw)) $score += 15;
    }

    // ── 4. Popularity small boost
    $score += min(15, (int)($track['plays'] ?? 0) / 20);
    $score += min(15, (int)($track['likes'] ?? 0));

    // ── 5. Small random jitter so queues vary per session
    $score += random_int(-5, 8);

    return $score;
}

function ai_build_dj_queue(int $userId, string $mood, int $limit = 20): array {
    $pdo = db();

    $stmt = $pdo->prepare("
        SELECT id, title, artist, genre, mood, plays, likes
        FROM tracks
        WHERE is_approved = 1
        LIMIT 500
    ");
    $stmt->execute();
    $candidates = $stmt->fetchAll();

    if (!$candidates) return [];

    // Score every track
    foreach ($candidates as &$c) {
        $c['_score'] = ai_score_track_for_mood($c, $mood);
    }
    unset($c);

    // Boost user's liked tracks that ALSO fit the mood
    try {
        $stmt = $pdo->prepare("SELECT track_id FROM likes WHERE user_id = ?");
        $stmt->execute([$userId]);
        $liked = array_column($stmt->fetchAll(), 'track_id');
        $likedSet = array_flip($liked);
        foreach ($candidates as &$c) {
            if (isset($likedSet[$c['id']]) && $c['_score'] > 0) $c['_score'] += 20;
        }
        unset($c);
    } catch (Throwable $e) {}

    // Sort descending
    usort($candidates, fn($a, $b) => $b['_score'] <=> $a['_score']);

    // Take top N — but only if their score is above a threshold
    $top = [];
    foreach ($candidates as $c) {
        if (count($top) >= $limit) break;
        // Only include tracks with at least marginally positive score
        // (mood-tagged or genre-matched or liked)
        if ($c['_score'] > -20) $top[] = $c['id'];
    }

    // If we couldn't fill the queue with good matches, top up with random others
    if (count($top) < $limit) {
        $exclude = implode(',', array_map('intval', $top)) ?: '0';
        $extra = $limit - count($top);
        $stmt = $pdo->query("SELECT id FROM tracks WHERE is_approved = 1 AND id NOT IN ($exclude) ORDER BY RAND() LIMIT $extra");
        $top = array_merge($top, array_column($stmt->fetchAll(), 'id'));
    }

    return $top;
}

function ai_recommend_for_user(int $userId, int $limit = 12): array {
    $pdo = db();
    $stmt = $pdo->prepare("
        SELECT t.genre, t.artist, COUNT(*) AS score
        FROM play_history ph
        JOIN tracks t ON t.id = ph.track_id
        WHERE ph.user_id = ?
        GROUP BY t.genre, t.artist
        ORDER BY score DESC
        LIMIT 30
    ");
    $stmt->execute([$userId]);
    $prefs = $stmt->fetchAll();

    if (!$prefs) {
        $stmt = $pdo->prepare("SELECT id FROM tracks WHERE is_approved = 1 ORDER BY RAND() LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return array_column($stmt->fetchAll(), 'id');
    }

    $genres  = array_values(array_unique(array_filter(array_column($prefs, 'genre'))));
    $artists = array_values(array_unique(array_filter(array_column($prefs, 'artist'))));

    $conds = []; $args = [];
    if ($genres)  { $conds[] = "genre IN (" . implode(',', array_fill(0, count($genres), '?')) . ")";  $args = array_merge($args, $genres); }
    if ($artists) { $conds[] = "artist IN (" . implode(',', array_fill(0, count($artists), '?')) . ")"; $args = array_merge($args, $artists); }

    $sql = "SELECT id FROM tracks WHERE is_approved = 1 AND (" . implode(' OR ', $conds) . ") ORDER BY (plays * 0.3 + likes * 0.7) DESC LIMIT ?";
    $args[] = $limit;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($args);
    $ids = array_column($stmt->fetchAll(), 'id');

    if (count($ids) < $limit) {
        $extra = $limit - count($ids);
        $notIn = $ids ? " AND id NOT IN (" . implode(',', array_map('intval', $ids)) . ")" : "";
        $stmt = $pdo->query("SELECT id FROM tracks WHERE is_approved = 1$notIn ORDER BY RAND() LIMIT $extra");
        $ids = array_merge($ids, array_column($stmt->fetchAll(), 'id'));
    }
    return $ids;
}