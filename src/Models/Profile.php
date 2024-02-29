<?php

namespace Duukkis\Bsky\Models;

use Carbon\Carbon;

class Profile extends Model
{
    public string $did;
    public string $handle;
    public string $displayName;
    public string $description;
    public string $avatar;
    public int $followsCount;
    public int $followersCount;
    public int $postsCount;
    public array $labels;

}