<?php

/**
 * 跟踪记录执行时间，及请求相关参数记录
 *
 * @author 
 */
class TrackHelper {

    /**
     * List of all benchmark markers and when they were added
     * @var array
     */
    private static $marker = array();

    /**
     * 是否开启跟踪
     * @var type 
     */
    private static $_on = true;

    /**
     * Set a benchmark marker
     *
     * Multiple calls to this function can be made so that several
     * execution points can be timed
     *
     * @access	public
     * @param	string	$name	name of the marker
     * @param	string	$value  value of the name
     * @return	void
     */
    public static function mark($name, $value = null) {
        if (self::$_on && config_item('track')) {
            if (!is_null($value)) {
                self::$marker[$name] = $value;
            } else {
                self::$marker[$name] = microtime();
            }
        } else {
            return;
        }
    }

    /**
     * Calculates the time difference between two marked points.
     *
     * If the first parameter is empty this function instead returns the
     * {elapsed_time} pseudo-variable. This permits the full system
     * execution time to be shown in a template. The output class will
     * swap the real value for this variable.
     *
     * @access	public
     * @param	string	a particular marked point
     * @param	string	a particular marked point
     * @param	integer	the number of decimal places
     * @return	mixed
     */
    public static function elapsedTime($point1 = '', $point2 = '', $decimals = 4) {
        if (self::$_on && config_item('track')) {
            if ($point1 == '' || !isset(self::$marker[$point1])) {
                return '{elapsed_time}';
            } else {
                $point1Time = self::$marker[$point1];
            }
            $point2Time = self::getPointTime($point2);
            list($sm, $ss) = explode(' ', $point1Time);
            list($em, $es) = explode(' ', $point2Time);

            return number_format(($em + $es) - ($sm + $ss), $decimals);
        } else {
            return;
        }
    }

    /**
     * 记录请求相关参数及时间 
     * @param string $point1 第一个监测点由mark设置
     * @param string $point2 第二个监测点由mark设置
     * @param int $code   请求结束时返回的状态值，一般200为成功
     */
    public static function trackLog($point1 = '', $point2 = '', $code = 10000, $data = '') {
        if (self::$_on && config_item('track')) {
            $user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
            if ($user && isset($user->imei)) {
                $userToken = "\tIMEI:" . $user->imei;
            } elseif ($user && isset($user->mobile)) {
                $userToken = "\tMOBILE:" . $user->mobile;
            } else {
                $userToken = '';
            }

            $message = "\tSTATUS:" . $code . (($code == 200) ? "\t-SUCCESS-" : "\t-WARNNING-" ) . "\n";
            $elapsedTime = self::elapsedTime($point1, $point2, 4);

            $markMessage = '';
            foreach (self::$marker as $key => $value) {
                $markMessage .= "\n\t" . $key . ":" . $value;
            }

            $returnData = mb_substr(print_r($data, true), 0, 128);
            if (strcmp($returnData, '') != 0) {
                $returnData .= "...";
            }

            if (isset($_SERVER["REQUEST_URI"]) && isset($_SERVER["REQUEST_METHOD"])) {
                $message .= $userToken
                        . "\n\tUSER_ANGENT:" . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '')
                        . "\n\tMODE:" . $_SERVER["REQUEST_METHOD"]
                        . "\n\tURI:" . $_SERVER["REQUEST_URI"]
                        . $markMessage
                        . "\n\tREMOTE:" . $_SERVER["REMOTE_ADDR"]
                        . "\n\tREQUEST:" . print_r($_REQUEST, true)
                        . "\n\tRESPONSE:" . $returnData
                        . "\n\tELAPSEDTIME:" . $elapsedTime
                ;
                Logger::apiLog($message);
            } else {
                $message .= "\tMODE:Command Line"
                        . "\n\tURI:" . $_SERVER["SCRIPT_FILENAME"]
                        . $markMessage
                        . "\n\tREMOTE:Localhost"
                        . "\n\tREQUEST:" . print_r($_SERVER["argv"], true)
                        . "\n\tRESPONSE:" . print_r($returnData, true)
                        . "\n\tELAPSEDTIME:" . $elapsedTime
                ;
                Logger::commandLog($message);
            }
//            if ($code == CodeHelper::CODE_SYS_EXCEPTION || $code == CodeHelper::CODE_PAGE_NOT_FOUND) {
//                Logger::errorLog($message);
//            } else {
//                Logger::apiLog($message);
//            }
        } else {
            return;
        }
    }

    /**
     * get all mark
     * @return string
     */
    public static function getAllMarks() {
        return self::$marker;
    }

    /**
     * get mark and time
     * @param type $mark
     * @return type
     */
    public static function getMark($mark, $onlyValue = true) {
        if (isset(self::$marker[$mark])) {
            if ($onlyValue) {
                return self::$marker[$mark];
            } else {
                return $mark . "=>" . self::$marker[$mark];
            }
        }
        return 'No Mark ' . $mark;
    }

    /**
     * 获得指定基准点
     * @param type $point
     * @param type $last
     * @return type
     */
    private static function getPointTime($point, $last = true) {
        if (isset(self::$marker[$point])) {
            $pointTime = self::$marker[$point];
        } else {
            if ($last) {
                $pointTime = self::$marker[$point] = microtime();
            }
        }
        return $pointTime;
    }

}

?>
