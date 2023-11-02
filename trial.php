<?php
include ('./vendor/autoload.php');

use Duukkis\Bsky\Bsky;
use Duukkis\Bsky\Models\Feed;

$bsky = new Bsky("duukkis.bsky.social", trim(file_get_contents("pass")));
/** @var Feed $feed */
$feed = $bsky->feed()->getAuthorFeed([]);

print_r($feed);

