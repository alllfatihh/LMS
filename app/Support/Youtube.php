<?php

namespace App\Support;

final class Youtube
{
    private const ID_PATTERN = '[a-zA-Z0-9_-]{11}';

    /**
     * URL embed untuk iframe.
     */
    public static function toEmbedUrl(?string $url): ?string
    {
        $id = self::extractVideoId($url);

        return $id !== null ? 'https://www.youtube.com/embed/' . $id : null;
    }

    public static function toWatchUrl(?string $url): ?string
    {
        $id = self::extractVideoId($url);

        return $id !== null ? 'https://www.youtube.com/watch?v=' . $id : null;
    }

    public static function extractVideoId(?string $url): ?string
    {
        $clean = self::sanitizeIncomingUrl((string) $url);

        return self::extractVideoIdFromCleaned($clean, 0);
    }

    private static function sanitizeIncomingUrl(string $raw): string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return '';
        }

        $raw = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $raw = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $raw) ?? $raw;

        // Ambil URL pertama jika disisipi teks / markdown: ... https://youtu.be/xx ...
        if (preg_match('#\bhttps?://[^\s<>"\'\\]]+#i', $raw, $m)) {
            $raw = $m[0];
        }

        return rtrim(trim($raw), '.,);\'\"');
    }

    private static function extractVideoIdFromCleaned(string $clean, int $depth): ?string
    {
        if ($clean === '' || $depth > 3) {
            return null;
        }

        $normalized = $clean;
        if (! preg_match('#^[a-z][a-z0-9+.-]*://#i', $normalized)) {
            $normalized = 'https://' . $normalized;
        }

        $parts = parse_url($normalized);
        if ($parts === false) {
            return self::extractFromRawString($clean);
        }

        $host = $parts['host'] ?? '';
        if ($host !== '' && ! self::isYoutubeHost(strtolower($host))) {
            // Jika ada host tapi bukan YouTube, pastikan ini bukan cuma ID yang disalahpahami sebagai host
            // PENTING: Gunakan $host asli (bukan strtolower) karena ID YouTube case-sensitive
            if (preg_match('/^' . self::ID_PATTERN . '$/', $host)) {
                return $host;
            }

            return null;
        }

        if (! empty($parts['query'])) {
            parse_str($parts['query'], $query);

            // Tautan perantara YouTube: ?u=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3D...
            if (isset($query['u']) && is_string($query['u']) && $query['u'] !== '') {
                $inner  = urldecode($query['u']);
                $nested = self::extractVideoIdFromCleaned(self::sanitizeIncomingUrl($inner), $depth + 1);
                if ($nested !== null) {
                    return $nested;
                }
            }

            // Parameter standar v=
            if (isset($query['v']) && is_string($query['v']) && $query['v'] !== '') {
                $fromV = self::normalizeElevenCharId($query['v']);
                if ($fromV !== null) {
                    return $fromV;
                }
            }
        }

        $path = $parts['path'] ?? '';

        // /embed/, /e/, /v/, /shorts/, /live/
        $pathPatterns = [
            '%/(?:embed|e|v|shorts|live)/(' . self::ID_PATTERN . ')(?:/|[?#]|$)%i',
            '%^/(' . self::ID_PATTERN . ')(?:/|[?#]|$)%i',
        ];
        foreach ($pathPatterns as $pattern) {
            if (preg_match($pattern, $path, $m)) {
                return $m[1];
            }
        }

        // youtu.be short links
        if ($host === 'youtu.be' || str_ends_with($host, '.youtu.be')) {
            if (preg_match('%^/(' . self::ID_PATTERN . ')(?:/|[?#]|$)%i', $path, $m)) {
                return $m[1];
            }
        }

        return self::extractFromRawString($clean);
    }

    private static function extractFromRawString(string $url): ?string
    {
        // 1️⃣ Standard ?v=ID pattern (works for watch, mobile, all variants)
        if (preg_match('%[?&]v=(' . self::ID_PATTERN . ')(?:[&#/]|$)%i', $url, $m)) {
            return $m[1];
        }

        // 2️⃣ Shortened youtu.be/ID
        if (preg_match('%youtu\.be/(' . self::ID_PATTERN . ')(?:[?#/]|$)%i', $url, $m)) {
            return $m[1];
        }

        // 3️⃣ Path-based: /embed/, /shorts/, /live/, /v/
        if (preg_match('%/(?:embed|shorts|live|v)/(' . self::ID_PATTERN . ')(?:[?#/]|$)%i', $url, $m)) {
            return $m[1];
        }

        // 4️⃣ Fallback — ambil 11-karakter ID yang muncul di string manapun
        if (preg_match('%(?<![a-zA-Z0-9_-])(' . self::ID_PATTERN . ')(?![a-zA-Z0-9_-])%', $url, $m)) {
            return $m[1];
        }

        return null;
    }

    private static function isYoutubeHost(string $host): bool
    {
        return str_ends_with($host, 'youtube.com')
            || str_ends_with($host, 'youtube-nocookie.com')
            || $host === 'youtu.be'
            || str_ends_with($host, '.youtu.be')
            || (bool) preg_match('/^youtube\.co\.[a-z]{2,3}$/', $host);
    }

    private static function normalizeElevenCharId(string $value): ?string
    {
        $value = trim($value);
        if (preg_match('/^(' . self::ID_PATTERN . ')/', $value, $m)) {
            return $m[1];
        }

        return null;
    }
}
