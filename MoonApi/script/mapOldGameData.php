<?php

/**
 * 用于迁移旧的开发者帐号及其旗下游戏数据
 */
/**
 * in normail mode can insert new record into db.table else echo the insert sql 
 */
define('NORMAIL_MODE', FALSE);
define('PUNCHBOX_UCENTER_V5_OLD', 'pay_old');
define('PUNCHBOX_OAUTH_API_OLD', 'ouath_old');
define('PUNCHBOX_UCENTER_V5', 'punchbox_ucenter_v5');
define('PUNCHBOX_OAUTH_API', 'punchbox_oauth_api');


$entUserArray = array(
    "zh@qq.com",
    "zy@qq.com",
    "jm-cqj@163.com",
    "mac@chukong-inc.com",
    "win@chukong-inc.com",
    "379531702@qq.com",
    "dengkai.chang@chukong-inc.com",
    "liangshiyuan_china@126.com",
    "yanli.huang@chukong-inc.com"
);

$conn = getDbConnection();

foreach ($entUserArray as $oneUser) {
        //pay uc_ent_user
        $sqlQueryUser = "select * from " . PUNCHBOX_UCENTER_V5_OLD . ".uc_ent_user where user='" . $oneUser . "'";
        $user = getOneLine($sqlQueryUser);
        if ($user) {
                echo "###user:" . $user->user . ":start\n";
                newInsert($user, PUNCHBOX_UCENTER_V5, "uc_ent_user");

                //pay uc_ent_group
                echo "##group:" . $user->group_id . "\n";
                $sqlQueryGroup = "select * from " . PUNCHBOX_UCENTER_V5_OLD . ".uc_ent_group where group_id=" . $user->group_id;
                $group = getOneLine($sqlQueryGroup);
                if ($group) {
                        newInsert($group, PUNCHBOX_UCENTER_V5, "uc_ent_group");
                }

                //pay uc_ent_corporation
                echo "##corporation:" . $user->corporation_id . "\n";
                $sqlQueryCorporation = "select * from " . PUNCHBOX_UCENTER_V5_OLD . ".uc_ent_corporation where id=" . $user->corporation_id;
                $corporation = getOneLine($sqlQueryCorporation);
                if ($corporation) {
                        newInsert($corporation, PUNCHBOX_UCENTER_V5, "uc_ent_corporation");
                }

                //pay uc_game_list
                echo "##pay:games:start\n";
                $sqlQueryGames = "select * from " . PUNCHBOX_UCENTER_V5_OLD . ".uc_game_list where corporation_id=" . $user->corporation_id;
                $games = getMoreLines($sqlQueryGames);
                foreach ($games as $game) {
                        newInsert($game, PUNCHBOX_UCENTER_V5, "uc_game_list");

                        //pay uc_app
                        echo "#pay:app:start\n";
                        $sqlQueryUcApp = "select * from " . PUNCHBOX_UCENTER_V5_OLD . ".uc_app where game_id=" . $game->game_id;
                        $ucApps = getMoreLines($sqlQueryUcApp);
                        foreach ($ucApps as $oneUcApp) {
                                newInsert($oneUcApp, PUNCHBOX_UCENTER_V5, "uc_app");
                        }
                        echo "#pay:app:end\n";
                }
                echo "##pay:games:end\n";


                //ouath game_list
                echo "##oauth:games:start\n";
                $sqlQueryGames2 = "select * from " . PUNCHBOX_OAUTH_API_OLD . ".game_list where corporation_id=" . $user->corporation_id;
                $games2 = getMoreLines($sqlQueryGames2);
                foreach ($games2 as $game2) {
                        newInsert($game2, PUNCHBOX_OAUTH_API, "game_list");

                        //oaut app_list                        
                        echo "#oauth:app:start\n";
                        $sqlQueryApp = "select * from " . PUNCHBOX_OAUTH_API_OLD . ".app_list where game_id=" . $game->game_id;
                        $apps = getMoreLines($sqlQueryApp);
                        foreach ($apps as $oneApp) {
                                newInsert($oneApp, PUNCHBOX_OAUTH_API, "app_list");
                        }
                        echo "#oauth:app:end\n";
                }
                echo "##oauth:games:end\n";

                //pay uc_tpl_channel
                echo "##pay:channel:start\n";
                $sqlQueryChannel = "select * from " . PUNCHBOX_UCENTER_V5_OLD . ".uc_tpl_channel where corporation_id=" . $user->corporation_id;
                $channels = getMoreLines($sqlQueryChannel);
                foreach ($channels as $channel) {
                        newInsert($channel, PUNCHBOX_UCENTER_V5, "uc_tpl_channel");

                        //pay uc_tpl_channel_sdk
                        echo "#pay:channel:sdk:start\n";
                        $sqlQueryChannelSdk = "select * from " . PUNCHBOX_UCENTER_V5_OLD . ".uc_tpl_channel_sdk where idChannel=" . $channel->idChannel;
                        $channelSdks = getMoreLines($sqlQueryChannelSdk);
                        foreach ($channelSdks as $channelSdk) {
                                newInsert($channelSdk, PUNCHBOX_UCENTER_V5, "uc_tpl_channel_sdk");
                        }
                        echo "#pay:channel:sdk:end\n";

                        //pay uc_tpl_user_sdk_param
                        echo "#pay:user:param:start\n";
                        $sqlQueryParam = "select * from " . PUNCHBOX_UCENTER_V5_OLD . ".uc_tpl_user_sdk_param where idChannel=" . $channel->idChannel;
                        $params = getMoreLines($sqlQueryParam);
                        foreach ($params as $param) {
                                newInsert($param, PUNCHBOX_UCENTER_V5, "uc_tpl_user_sdk_param");
                        }
                        echo "#pay:user:param:end\n";
                }
                echo "##pay:channel:end\n";
        }
        echo "###user:" . $user->user . ":end\n";
}

function getMoreLines($sql) {
        $arr = array();
        $res = mysql_query($sql) or die(mysql_error());
        while ($line = mysql_fetch_object($res)) {
                $arr[] = $line;
        }
        return $arr;
}

function getOneLine($sql) {
        $res = mysql_query($sql) or die(mysql_error());
        if ($res) {
                return mysql_fetch_object($res);
        }
        return null;
}

function newInsert($data, $dbname, $table) {
        $jsonString = json_encode($data);
        $data = json_decode($jsonString, true);
        $keys = array_keys($data);
        $values = array_values($data);

        $keyString = "(" . implode(',', $keys) . ")";
        $valueString = "('" . implode("','", $values) . "')";

        $sqlEdit = "insert into " . $dbname . "." . $table . $keyString . " values" . $valueString;
        echo $sqlEdit . "\n";
        if (NORMAIL_MODE) {
                mysql_query($sqlEdit) or die(mysql_error());
        }
}

function getDbConnection() {
        $host = '10.10.10.89';
        $user = 'ucenter';
        $pwd = 'Agcg$?iQbN';
        $conn = mysql_connect($host, $user, $pwd);

        mysql_query("SET NAMES 'UTF8'");
//        mysql_query("SET CHARACTER SET UTF8");
//        mysql_query("SET CHARACTER_SET_RESULTS=UTF8'");

        return $conn;
}

?>