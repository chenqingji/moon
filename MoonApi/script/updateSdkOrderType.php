<?php
/**
 * 用于更新校正idSDK与orderType对应的数据
 */

$idSDKOrderTypeMap = array(
    "21"=>71,
    "20"=>72,
    "19"=>73,
    "18"=>74,
    "17"=>67,
    "16"=>75,
    "15"=>94,
    "14"=>66,
    "13"=>85,
    "12"=>87,
    "11"=>93,
    "10"=>86,
    "9"=>92,
    "8"=>76,
    "7"=>88,
    "6"=>98,
    "5"=>99,
    "4"=>95,
    "3"=>78,
    "2"=>97,
    "1"=>83
);

$conn = getDbConnection();
//updte uc_tpl_sdk idSDK=>orderType updated_time
//update uc_app idSDK=>orderType update_time
foreach($idSDKOrderTypeMap as $idSDK=>$orderType){
        $sqlOne = "update punchbox_ucenter_v5.uc_tpl_sdk set orderType=".$orderType." where idSDK=".$idSDK;
        mysql_query($sqlOne) or die(mysql_error());
        $sqlTwo = "update punchbox_ucenter_v5.uc_app set order_type=".$orderType." where idSDK=".$idSDK;
        mysql_query($sqlTwo) or die(mysql_error());
}

function getDbConnection() {
        $host = 'writedb.uc.punchbox.ads';
        $user = 'ucenter';
        $pwd = 'Agcg$?iQbN';
        $conn = mysql_connect($host, $user, $pwd);
        return $conn;
}



?>