<?php

namespace Duukkis\Bsky\Entities;

use Duukkis\Bsky\Bsky;
use Duukkis\Bsky\helpers\Mapper;
use Duukkis\Bsky\Models\Model;
use Duukkis\Bsky\Models\Profile;
use Duukkis\Bsky\Models\ProfileRecord;

class ProfileEntity
{
    public function __construct(private Bsky $bsky)
    {
    }

    public function get(string $actor): Model|Profile
    {
        $params = ["actor" => $actor];
        return Mapper::mapJsonObjectToClass(
            $this->bsky->get("https://bsky.social/xrpc/app.bsky.actor.getProfile", $params),
            new Profile()
        );
    }

    // The avatar is a stupid array of existing image at the moment as dont have an endpoint where to get that
    public function updateProfile(
        Profile $profile,
        array $avatar = [],
        string  $newDisplayName = null,
        string  $newDescription = null,
    ): void {
        $params = [];
        $params["repo"] = $profile->did;
        $params["collection"] = 'app.bsky.actor.profile';
        $params["rkey"] = 'self';
        $params["record"] = [
            '$type' => "app.bsky.actor.profile",
            "avatar" => $avatar,
            "description" => $newDescription ?? $profile->description,
            "displayName" => $newDisplayName ?? $profile->displayName,
        ];
/*        if ($file !== null) {
            $repoEntity = new RepoEntity($this->bsky);
            $blob = $repoEntity->uploadFile($file);
            $params["avatar"] = $blob->blob;
        }*/
        $this->bsky->post("https://bsky.social/xrpc/com.atproto.repo.putRecord", $params);
    }
}