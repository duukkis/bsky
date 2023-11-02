<?php

namespace Duukkis\Bsky\Models;

use Carbon\Carbon;

class Record extends Model
{
    public string $text;
    public string $types;
    public array $langs;
    public Carbon $createdAt;
}