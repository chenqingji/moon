<?php

/**
 * 用于批量修改uc_tpl_channel_sdk所有
 */
$conn = getDbConnection();
$sql = "select * from punchbox_ucenter_v5.uc_tpl_channel_sdk";
$res = mysql_query($sql);
while ($line = mysql_fetch_array($res)) {
        $idChannel = $line['idChannel'];
        $idSDK = $line['idSDK'];

        if (checkOauth($line) && checkPay($line)) {
                $type = 40;
        } elseif (checkOauth($line)) {
                $type = 32;
        } elseif (checkPay($line)) {
                $type = 8;
        } else {
                $type = 0;
        }
        $typeArray = getChannelSdkTypes($type);
        $updateString = "";
        foreach ($typeArray as $key => $value) {
                $updateString .= "," . $key . "=" . $value;
        }
        $sqlUpdate = "update punchbox_ucenter_v5.uc_tpl_channel_sdk set type=" . $type . $updateString . " where idChannel=" . $idChannel . " and idSDK=" . $idSDK;
        echo $sqlUpdate."\n";
        mysql_query($sqlUpdate) or die(mysql_error());
        echo "#done#\n";
}

function checkOauth($line) {
        $sqlOauth = "select * from punchbox_oauth_api.app_list where idChannel=" . $line['idChannel'] . " and idSDK=" . $line['idSDK'];
        $res = mysql_query($sqlOauth) or die(mysql_error());
        $count = mysql_num_rows($res);
        if ($count) {
                return true;
        }
        return false;
}

function checkPay($line) {
        $sqlPay = "select * from punchbox_ucenter_v5.uc_app where idChannel=" . $line['idChannel'] . " and idSDK=" . $line['idSDK'];
        $res = mysql_query($sqlPay) or die(mysql_error());
        $count = mysql_num_rows($res);
        if ($count) {
                return true;
        }
        return false;
}

function getChannelSdkTypes($type) {
        $typeBin = str_pad(decbin($type), 6, 0, STR_PAD_LEFT);
        $types = array();
        $types['userType'] = substr($typeBin, -6, 1);
        $types['adsType'] = substr($typeBin, -5, 1);
        $types['payType'] = substr($typeBin, -4, 1);
        $types['socialType'] = substr($typeBin, -3, 1);
        $types['shareType'] = substr($typeBin, -2, 1);
        $types['statisticsType'] = substr($typeBin, -1, 1);
        return $types;
}

function getDbConnection() {
        $host = 'writedb.uc.punchbox.ads';
        $user = 'ucenter';
        $pwd = 'Agcg$?iQbN';
        $conn = mysql_connect($host, $user, $pwd);
        return $conn;
}

?>