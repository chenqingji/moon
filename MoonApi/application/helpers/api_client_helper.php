<?php

/**
 * 客户端帮助
 * @author jm
 */
class ClientHelper {

    /**
     * 获取请求客户端真是ip
     * @return string
     */
    public static function getRemoteRealIp() {
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else
            $ip = "Unknow";
        return $ip;
    }

    /**
     * 通过ip获得城市名称 依赖于第三方api
     * @param string $ip 默认空由服务器自动获取
     * @return object
     */
    public static function getRemoteCity($ip = '') {
        $returnObject = null;
        if (empty($ip)) {
            $ip = self::getRemoteRealIp();
        }
        //可采用 api_request_helper.php提供的模拟请求方式 @todo
        $getCityByIpUrl = "http://int.dpool.sina.com.cn/iplookup/iplookup.php" . "?format=js&ip=" . $ip;

        $customStream = stream_context_create(array(
            'http' => array(
                'timeout' => 30 //set a 5 seconds to down the request
            )
                )
        );
        $returnString = file_get_contents($getCityByIpUrl, false, $customStream);
        $returnJson = strstr($returnString, '=');
        $returnJson = trim($returnJson, " =;");
        $returnObject = json_decode($returnJson);
        /**
         * $rtnJson format :
         * {"ret":1,"start":"121.204.128.0","end":"121.204.255.255","country":"\u4e2d\u56fd","province":"\u798f\u5efa","city":"\u53a6\u95e8","district":"","isp":"\u7535\u4fe1","type":"","desc":""}
         */
        return $returnObject;
    }

    /**
     * 获得服务器ip
     * @return type
     */
    public static function getServerIp() {
        $delimiter = ':';
        $serverAddr = $_SERVER['SERVER_ADDR'];
        if (strpos($serverAddr, $delimiter)) {
            list($host, $port) = explode($delimiter, $serverAddr, 2);
        } else {
            $host = $serverAddr;
        }
        return $host;
    }

    /**
     * 获得服务器
     * @return type
     */
    public static function getServerHost() {
        if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) {
            return $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']) {
            return $_SERVER['SERVER_NAME'];
        } else {
            return self::getServerIp();
        }
    }

    /**
     * 自定义配置获取文件服务器iphost
     * @param type $toUpload 是否上传，上传与下载不同端口
     * @return string
     */
    public static function getFileServerHost($toUpload = false) {
        $localServerAddr = self::getServerIp();
        $port = 8084;
        $matchArray = array(
            '117.121.57.91',
            '117.121.57.162',
            '192.168.2.211',
        );

        if (in_array($localServerAddr, $matchArray)) {
            if($toUpload){
                return "localhost:".$port;
            }
            return $localServerAddr . ":" . $port;
//        } elseif ($localServerAddr == '221.122.115.234') {
//            return '221.122.115.233:' . $port;    //临时使用ip访问
        } else {
            return 'app.download.anzhuoshangdian.com';
        }
    }

}

?>
