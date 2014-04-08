<?php

/**
 * define参考uc_tpl_sdk表的idSDK
 */
define('ID_SDK_ND91',    1);
define('ID_SDK_QH360',   2);
define('ID_SDK_XIAOMI',  3);
define('ID_SDK_WDJ',     4);
define('ID_SDK_DUOKU',   5);
define('ID_SDK_UC',      6);
define('ID_SDK_OPPO',    7);
define('ID_SDK_DOWNJOY', 8);
define('ID_SDK_APPCHINA',9);
define('ID_SDK_GFAN',   10);
define('ID_SDK_LENOVO', 11);
define('ID_SDK_COCO',   12);
define('ID_SDK_ANZHI',  13);
define('ID_SDK_MUMAYI', 14);
define('ID_SDK_HUAWEI', 15);
define('ID_SDK_3G',     16);
define('ID_SDK_OPERA',  17);
define('ID_SDK_IOS_PP', 18);
define('ID_SDK_IOS_KUAIYONG', 19);
define('ID_SDK_IOS_TP',  20);
define('ID_SDK_IOS_ND91',21);
define('ID_SDK_IOS_COCO',22);

return array(
    ID_SDK_ND91 => array(
        'app_id' => 'Nd91AppId',
        'app_key' => 'Nd91AppKey',
        'sign_key' => 'Nd91AppKey',
        'pay_expand1' => 'Nd91AppId',
    ),
    ID_SDK_QH360 => array(
        'app_id' => 'QHOPENSDK_APPID',
        'app_key' => 'QHOPENSDK_APPKEY',
        'app_secret' => 'QHOPENSDK_APPSECRET',
        'sign_key' => 'QHOPENSDK_APPSECRET',
    ),
    ID_SDK_XIAOMI => array(
        'app_id' => 'XiaoMiAppId',
        'app_key' => 'XiaoMiAppKey',
        'sign_key' => 'XiaoMiAppKey',
        'pay_expand1' => 'XiaoMiAppId',
    ),
    ID_SDK_WDJ => array(
        'app_key' => 'WDJAppKeyID',
        'app_secret' => 'WDJAppSecret',
    //wdj采用公钥的方式签名
    ),
    ID_SDK_DUOKU => array(
        'app_id' => 'DKAppId',
        'app_key' => 'DKAppKey',
        'app_secret' => 'DKAppSecret',
        'sign_key' => 'DKAppSecret',
        'pay_expand1' => 'DKAppId',
        'pay_expand2' => 'DKAppKey',
    ),
    ID_SDK_UC => array(
        'cp_id' => 'UCCpID',
        'app_key' => 'UCApiKey',
        'sign_key' => 'UCApiKey',
        'oauth_expand1' => 'UCGameID',
        'oauth_expand2' => 'UCServerID',
        'pay_expand1' => 'UCCpID',
    ),
    ID_SDK_OPPO => array(
        'app_id' => 'OPPOGameId',
        'app_key' => 'OPPOAppKey',
        'app_secret' => 'OPPOAppSecret',
    //oppo采用公钥的方式签名
    ),
    ID_SDK_DOWNJOY => array(
        'app_id' => 'DJAppId',
        'app_key' => 'DJAppKey',
        'sign_key' => 'DJPaymentKey',
    ),
    ID_SDK_APPCHINA => array(
        'app_id' => 'APPCHINA_ACCOUNT_APPID',
        'app_key' => 'APPCHINA_ACCOUNT_APPKEY',
        'sign_key' => 'AppchinaAppKey',
    ),
    ID_SDK_GFAN => array(
        'cp_id' => 'gfan_cpid',
        'sign_key' => 'gfan_pay_appkey',
    ),
    ID_SDK_LENOVO => array(
        'app_id' => 'LenovoAppId',
        'sign_key' => 'LenovoAppKey',
    ),
    ID_SDK_COCO => array(
        'app_id' => 'coco_aid',
        'app_secret' => 'coco_appSecret',
        'sign_key' => 'coco_appSecret',
    ),
    ID_SDK_ANZHI => array(
        'app_key' => 'AnzhiAppKey',
        'app_secret' => 'AnzhiAppSecret',
        'sign_key' => 'AnzhiAppSecret',
    ),
    ID_SDK_MUMAYI => array(
        'sign_key' => 'MuMaYiAppKey',
    ),
    ID_SDK_HUAWEI => array(
        'app_id' => 'HuaWeiClientId',
        'app_secret' => 'HuaWeiAppSecret',
        // 华为采用公钥签名
    ),
    ID_SDK_3G => array(
        'cp_id' => 'JiuBang3GCpId',
        'oauth_expand1' => 'JiuBang3GGameId',
        'sign_key' => 'JiuBang3GMd5Key',
    ),
    ID_SDK_OPERA => array(
        'app_id' => 'OuPengAppId',
        'sign_key' => 'OuPengAppKey',
    ),
    ID_SDK_IOS_PP => array(
        'app_id' => 'iOSPPAppId',
        'app_key' => 'iOSPPAppKey',
    ),
    ID_SDK_IOS_KUAIYONG => array(
        'app_id' => 'iOSKYID',
        'app_key' => 'iOSKYappkey',
        'sign_key' => 'iOSKYPayRsaPubKey',
        'app_secret' => 'iOSKYSignSecret',
    ),
    ID_SDK_IOS_TP => array(
        'app_id' => 'iOSTPAppid',
        'app_key' => 'iOSTPAppkey',
        'sign_key' => 'iOSTPAppkey',
    ),
    ID_SDK_IOS_ND91 => array(
        'app_id' => 'iOSND91AppId',
        'app_key' => 'iOSND91AppKey',
        'pay_expand1' => 'iOSND91AppId',
        'sign_key' => 'iOSND91AppKey',
    ),
    ID_SDK_IOS_COCO => array(
        'app_id' => 'iOSCocoappId',
        'app_secret' => 'iOSCocoappSecret',
    ),
);
?>
