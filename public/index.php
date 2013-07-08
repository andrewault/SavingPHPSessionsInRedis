<?php
require '../vendor/autoload.php';

$db = new Predis\Client();
$prefix = 'PHPSESSID:';
$sessHandler = new RedisSessionHandler($db, $prefix);
$sessHandler->ttl = ini_get('session.gc_maxlifetime');

session_set_save_handler(
    array($sessHandler, 'open'),
    array($sessHandler, 'close'),
    array($sessHandler, 'read'),
    array($sessHandler, 'write'),
    array($sessHandler, 'destroy'),
    array($sessHandler, 'gc')
);
session_start();

$views = ($_SESSION['views']) ?: 0;
$_SESSION['views'] = $views + 1;
session_write_close();

$sessId = session_id();
echo '<p>$_SESSION["views"]: ' . $_SESSION['views'];
echo '<p>Redis key: ' . $prefix . $sessId;
echo '<p>Value from Redis: ' . $db->get($prefix . $sessId);
echo '<p>TTL: ' . $db->ttl($prefix . $sessId);

