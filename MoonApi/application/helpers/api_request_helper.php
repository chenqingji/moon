<?php

/**
 * 模拟请求  @todo 借鉴统一从yii版本pluginx项目中的模拟请求组件
 *
 * @author jm
 */
class RequestHelper {

    /**
     * 模拟get请求
     * @param string $url 请求url- 不带参数
     * @param array $post 请求参数
     * @param string $queryStringCharset 请求参数编码
     * @return string|null
     */
    public static function get($url, $post = array(), $queryStringCharset = '') {
        if ($url) {
            $queryString = '';
            while (list($k, $v) = each($data)) {
                $post .= ($k) . "=" . ($v) . "&";
            }
            $queryString = substr($queryString, 0, -1);
            if ($queryStringCharset) {
                $url = self::SMS_URL . "?" . iconv('', $queryStringCharset, $queryString);
            }
            $customStream = stream_context_create(
                    array(
                        'http' => array(
                            'timeout' => 30 //set a 5 seconds to down the request
                        )
                    )
            );
            return file_get_contents($url, false, $customStream);
        } else {
            return '-error: url is null';
        }
    }

    /**
     * 模拟post请求
     * @param string $url 请求url
     * @param array $post 请求参数
     * @param string $queryStringCharset  请求参数编码
     * @return string
     */
    public static function post($url, $post = array(), $queryStringCharset = '') {
        if ($url) {
            $row = parse_url($url);
            $host = $row['host'];
            $port = $row['port'] ? $row['port'] : 80;
            $path = $row['path'];
            $queryString = '';
            while (list($k, $v) = each($post)) {
                $queryString .= ($k) . "=" . ($v) . "&";
            }
            $queryString = substr($queryString, 0, -1);
            if ($queryStringCharset) {
                $queryString = iconv('', $queryStringCharset, $queryString);
            }
            $len = strlen($queryString);
            $fp = @fsockopen($host, $port, $errno, $errstr, 30);
            if (!$fp) {
                return "$errstr ($errno)\n";
            } else {
                $receive = '';
                $out = "POST $path HTTP/1.1\n";
                $out .= "Host: $host\n";
                $out .= "Content-type: application/x-www-form-urlencoded\n";
                $out .= "Connection: Close\n";
                $out .= "Content-Length: $len\n";
                $out .= "\n";
                $out .= $queryString . "\n";
                fwrite($fp, $out);
                while (!feof($fp)) {
                    $receive .= fgets($fp);
                }
                fclose($fp);
//                print_r($out);
//                echo "\n";
//                print_r($receive);
//                exit;
                $receive = explode("\r\n\r\n", $receive);
                unset($receive[0]);
                return implode("", $receive);
            }
        } else {
            return '-error: url is null';
        }
    }

    /**
     * 通过curl方式提交请求
     * @param type $data 提交请求参数 如果是上传文件 'file1'='@/data/xxx.txt' 注意@后跟物理路径
     * @param type $url 请求url
     * @return array if errno is 0 means success and error is ''
     */
    public static function curlPost($data, $url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 curl");
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);        
        curl_setopt($curl, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_0);        
        $result = curl_exec($curl);
        $error = curl_error($curl);
        $errno = curl_errno($curl);
        curl_close($curl);
        return array('errno' => $errno, 'error' => $error, 'result' => $result);
    }

}

?>
