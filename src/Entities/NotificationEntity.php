<?php

namespace Duukkis\Bsky\Entities;

use Duukkis\Bsky\Bsky;
use Duukkis\Bsky\helpers\Mapper;
use Duukkis\Bsky\Models\Feed;
use Duukkis\Bsky\Models\Model;
use Duukkis\Bsky\Models\Notifications;

class NotificationEntity
{
    public function __construct(private Bsky $bsky)
    {
    }

    public function listNotifications(array $params): Model|Feed
    {
        $useParams = array_merge([
            "limit" => 20,
        ], $params);
        return Mapper::mapJsonObjectToClass(
            $this->bsky->get("https://bsky.social/xrpc/app.bsky.notification.listNotifications", $useParams),
            new Notifications()
        );
    }
}