<?php

namespace Duukkis\Bsky\Models;

class Author extends Model
{
    public string $did;
    public string $handle;
    public string $displayName;
    public string $avatar;
}