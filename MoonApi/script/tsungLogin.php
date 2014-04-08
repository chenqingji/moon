<?php

/**
 * 模拟渠道服务器响应开发者服务器登录验证接口
 */

$rand = rand(1000, 999);
$fi = $rand % 8;
if ($fi) {
        //sleep 0.2 second
        usleep(200000);
        echo json_encode(array("status" => 0, "data" => array("uid" => 123321, "name" => "imabc")));
        exit;
} else {
        usleep(300000);
        echo json_encode(array("status" => 1111, "data" => "未知错误，在渠道服务器验证失败;"));
        exit;
}
?>
