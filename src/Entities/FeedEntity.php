<?php

namespace Duukkis\Bsky\Entities;

use Duukkis\Bsky\Bsky;
use Duukkis\Bsky\helpers\Mapper;
use Duukkis\Bsky\Models\Feed;
use Duukkis\Bsky\Models\Model;

class FeedEntity
{
    public function __construct(private Bsky $bsky)
    {
    }

    public function getAuthorFeed(array $params): Model|Feed
    {
        $useParams = array_merge([
            "actor" => $this->bsky->getDid(),
            "limit" => 10,
        ], $params);
        return Mapper::mapJsonObjectToClass(
            $this->bsky->get("https://bsky.social/xrpc/app.bsky.feed.getAuthorFeed", $useParams),
            new Feed()
        );
    }
}