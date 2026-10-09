<?php
function clean_str(?string $s, int $max = 255): string {
    $s = trim((string)$s);
    $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $s);
    return mb_substr($s, 0, $max);
}
function clean_email(?string $s): string {
    return filter_var(trim((string)$s), FILTER_SANITIZE_EMAIL);
}
function e(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}