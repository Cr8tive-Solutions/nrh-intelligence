<?php

if (! function_exists('hid')) {
    function hid(int|string $id): string
    {
        return app('hashids')->encode((int) $id);
    }
}

if (! function_exists('hdecode')) {
    function hdecode(string $hash): int
    {
        $decoded = app('hashids')->decode($hash);
        abort_if(empty($decoded), 404);

        return $decoded[0];
    }
}

if (! function_exists('initials')) {
    /**
     * Avatar initials for a person or company: the first letter of the first
     * two words ("Anwar Saad" → "AS"), or the first two letters of a single
     * word ("Kadboard" → "KA").
     */
    function initials(?string $name, string $fallback = '?'): string
    {
        $words = preg_split('/\s+/u', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY);

        if ($words === []) {
            return $fallback;
        }

        $letters = count($words) > 1
            ? mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1)
            : mb_substr($words[0], 0, 2);

        return mb_strtoupper($letters);
    }
}
