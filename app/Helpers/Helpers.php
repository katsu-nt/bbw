<?php

if (!function_exists('isPremiumContent')) {
    function isPremiumContent(array $processedKeywords): bool
    {
        foreach ($processedKeywords as $keyword) {
            if (strtolower($keyword) === 'premium') {
                return true;
            }
        }
        return false;
    }
}

function convertPublishedTime($dateString)
{
    // Extract timestamp from format: /Date(1749616141000)/
    preg_match('/\/Date\((\d+)\)\//', $dateString, $matches);
    $timestamp = isset($matches[1]) ? (int)$matches[1] / 1000 : null;

    if (!$timestamp) return 'Không rõ thời gian';

    $diffInSeconds = time() - $timestamp;

    if ($diffInSeconds < 0) return 'Trong tương lai';

    $days = floor($diffInSeconds / 86400);
    $hours = floor(($diffInSeconds % 86400) / 3600);
    $minutes = floor(($diffInSeconds % 3600) / 60);

    if ($days > 0) return "$days ngày";
    if ($hours > 0) return "$hours giờ";
    return "$minutes phút";
}

function isTimeGreaterThanHours(string $dateString, int $hours): bool
{
    // Extract timestamp from format: /Date(1749616141000)/
    preg_match('/\/Date\((\d+)\)\//', $dateString, $matches);
    $timestamp = isset($matches[1]) ? (int)$matches[1] / 1000 : null;

    if (!$timestamp) return false;

    $now = time();
    $thresholdSeconds = $hours * 3600;

    return ($now - $timestamp) > $thresholdSeconds;
}
