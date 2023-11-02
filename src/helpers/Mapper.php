<?php

namespace Duukkis\Bsky\helpers;

use Carbon\Carbon;
use Duukkis\Bsky\Models\Author;
use Duukkis\Bsky\Models\Model;
use Duukkis\Bsky\Models\Post;
use Duukkis\Bsky\Models\Record;
use InvalidArgumentException;

class Mapper
{
    public static function mapJsonObjectToClass(mixed $item, Model $model): Model
    {
        return $model::build($item, $model);
    }

    public static function mapJsonObjectToClassArray(array $items, Model $model): array
    {
        $result = [];
        foreach ($items as $i => $item) {
            $entity = $model::build($item, clone $model);
            array_push($result, $entity);
        }
        return $result;
    }

    public static function map(\stdClass $json, Model $obj): Model
    {
        foreach ($json as $key => $val) {
            if (property_exists($obj, $key)) {
                $obj = static::mapItem($obj, $key, $val, isset($obj->useKey[$key]) ? $obj->useKey[$key] : null);
            }
        }
        return $obj;
    }

    private static function mapItem(Model $obj, $key, $val, $useArrayKey): Model
    {
        // json contains object that is not mapped
        if (!property_exists($obj, $key)) {
            return $obj;
        }
        $rp = new \ReflectionProperty($obj, $key);
        $type = $rp->getType()->getName();
        switch ($type) {
            case "int":
                $obj->$key = (int) $val;
                break;
            case "string":
                $obj->$key = $val;
                break;
            case "bool":
                $obj->$key = (bool) $val;
                break;
            case "Carbon\Carbon":
                $obj->$key = Carbon::parse($val);
                break;
            case "array":
                if (isset($obj->mapArrayToObjects[$key])) {
                    foreach ($val as $j => $sub) {
                        if ($useArrayKey !== null) {
                            $sub = $sub->$useArrayKey;
                        }
                        /** @var Model $subitem */
                        $subitem = new $obj->mapArrayToObjects[$key]();
                        foreach ($sub as $subkey => $subval) {
                            $subitem = self::mapItem($subitem, $subkey, $subval, null);
                        }
                        array_push($obj->$key, $subitem);
                    }
                } else { // no type defined
                    $arr = [];
                    foreach ($val as $j => $sub) {
                        $arr[$j] = $sub;
                    }
                    $obj->$key = $arr;
                }
                break;
            default:
                if ($val == null) {
                    $obj->$key = $val;
                    return $obj;
                }
                switch ($type) {
                    case "Duukkis\Bsky\Models\Author":
                        $obj->$key = Author::build($val, new Author());
                        break;
                    case "Duukkis\Bsky\Models\Post":
                        $obj->$key = Post::build($val, new Post());
                        break;
                    case "Duukkis\Bsky\Models\Record":
                        $obj->$key = Record::build($val, new Record());
                        break;
                    default:
                        throw new InvalidArgumentException($type . " is not mapped");
                }
                break;
        }
        return $obj;
    }
}
