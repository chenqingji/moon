<?php

/*
 * ci框架php 命令行执行php脚本
 * php -f script.php [控制器目录] 控制类名 [方法名]     注:[]表示可有可无
 * eg:  php -f script.php game game status
 */

/**
 * 需要检测是否有至少2个参数($argv[0]为脚本文件名参数) 否则报错退出
 */
if (count($argv) < 3) {
    $note1 = "CommandLine: Missing parameters.";
    $note2 = "eg:";
    $note3 = "php -f script.php [控制器目录] 控制类名 [方法名]";
    echo $note1 . "\n" . $note2 . "\n" . $note3 . "\n";
    exit;
}
set_time_limit(0);
require_once '/usr/local/nginx/html/GameBoxServer/gamebox/api2/application/index.php';
?>
