<?php
include ('./vendor/autoload.php');

use Duukkis\Bsky\Bsky;
use Duukkis\Bsky\Models\Feed;
use Duukkis\Bsky\Models\Notification;

$bsky = new Bsky("duukkis.bsky.social", trim(file_get_contents("pass")));
/** @var Notification $feed */
$feed = $bsky->notification()->listNotifications(["limit" => 10]);

// $feed = $bsky->feed()->getAuthorFeed([]);

print_r($feed);

