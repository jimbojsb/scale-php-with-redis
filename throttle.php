<?php
$threshold = 100;
if ($_GET['mysql']) {
    $p = new PDO('mysql:host=localhost;dbname=redistest');
    $ip = sprintf('%u', ip2long($_SERVER['REMOTE_ADDR']));
    $stmt = $p->prepare("SELECT numvisits FROM ip_logs WHERE ip = ?");
    $num = $stmt->execute(array($ip));
    if ($num) {
        $num = $stmt->fetchColumn();
    }
    if ($num < $threshold) {
        echo 'ok';
        // blow away the query cache !!!
        $stmt = $p->prepare("INSERT INTO ip_logs VALUES (?, ?) ON DUPLICATE KEY UPDATE numvisits=numvisits + 1");
        $stmt->execute(array($ip, 1));
    } else {
        echo 'bad';
    }
} else {
    require_once 'predis_0.7.0-dev.phar';
    $p = new Predis\Client();
    $ip = $_SERVER['REMOTE_ADDR'];
    if ($p->get($ip) < $threshold) {
        echo 'ok';
        $p->incr($ip);
    } else {
        echo 'bad';
    }
}
