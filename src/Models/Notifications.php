<?php

namespace Duukkis\Bsky\Models;

class Notifications extends Model
{
    public array $notifications = [];
    public string $cursor;
    public array $mapArrayToObjects = [
        "notifications" => Notification::class,
    ];

    public array $useKey = ["feed" => "post"];
}