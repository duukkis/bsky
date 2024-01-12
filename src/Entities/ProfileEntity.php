<?php

namespace Duukkis\Bsky\Entities;

use Duukkis\Bsky\Bsky;
use Duukkis\Bsky\helpers\Mapper;
use Duukkis\Bsky\Models\Model;
use Duukkis\Bsky\Models\Profile;

class ProfileEntity
{
    public function __construct(private Bsky $bsky)
    {
    }

    public function get(array $params): Model|Profile
    {
        return Mapper::mapJsonObjectToClass(
            $this->bsky->get("https://bsky.social/xrpc/app.bsky.actor.getProfile", $params),
            new Profile()
        );
    }

}