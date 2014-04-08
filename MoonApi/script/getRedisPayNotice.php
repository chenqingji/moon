<?php

/*
 * 简单查看所有通知失败的订单详细信息 通知地址 通知信息 下一次通知时间
 */

$redis = new Redis();
$redis->connect("127.0.0.1", 6379);
$res = $redis->zrevrange("paynotifyset_v5", 0, -1);
foreach ($res as $one) {
        $one = json_decode(base64_decode($one), true);
        print_r($one);
}
?>
