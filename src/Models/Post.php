<?php

namespace Duukkis\Bsky\Models;

use Carbon\Carbon;

class Post extends Model
{
    public string $uri;
    public string $cid;
    public Author $author;
    public Record $record;
    public int $replyCount;
    public int $repostCount;
    public int $likeCount;
    public Carbon $indexedAt;
}