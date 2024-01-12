<?php

namespace Duukkis\Bsky\Models;

use Carbon\Carbon;

class Notification extends Model
{
    public string $uri;
    public string $cid;
    public Author $author;

    // reasons being like. follow, repost, reply, mention
    public string $reason;

    // this can be anything
    public mixed $record;
    public bool $isRead;
    public Carbon $indexedAt;

}