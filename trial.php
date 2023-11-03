<?php
include ('./vendor/autoload.php');

use Duukkis\Bsky\Bsky;
use Duukkis\Bsky\Models\Feed;
use Duukkis\Bsky\Models\Notification;

$bsky = new Bsky("duukkis.bsky.social", trim(file_get_contents("pass")));

$bsky->repo()->createRecord("New library fun", ["fi"]);
// print_r($feed);
die();

/** @var Notification $feed */
$feed = $bsky->notification()->listNotifications(["limit" => 10]);

/** @var Feed $feed */
$feed = $bsky->feed()->getAuthorFeed([]);


