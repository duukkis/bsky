<?php

namespace Duukkis\Bsky\Models;

class Feed extends Model
{
    public array $feed = [];
    public string $cursor;
    public array $mapArrayToObjects = [
        "feed" => Post::class,
    ];

    public bool 
}