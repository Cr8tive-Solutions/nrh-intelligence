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
