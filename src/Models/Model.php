<?php

namespace Duukkis\Bsky\Models;

use Duukkis\Bsky\helpers\Mapper;

abstract class Model
{
    public array $useKey = [];

    public array $mapArrayToObjects = [];

    public static function build(\stdClass $item, Model $model): self
    {
        return Mapper::map($item, $model);
    }
}
