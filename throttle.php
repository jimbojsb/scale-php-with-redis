<?php
require_once 'predis_0.7.0-dev.phar';
$threshold = 100;
$p = new Predis\Client();
$ip = $_SERVER['REMOTE_ADDR'];
if ($p->get($ip) < $threshold) {
    echo 'ok';
    $p->incr($ip);
} else {
    echo 'bad';
}
