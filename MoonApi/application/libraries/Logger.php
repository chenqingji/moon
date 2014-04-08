<?php

/**
 *  日志   操作日志/跟踪日志/错误日志
 *
 * @author jm
 */
class Logger extends CI_Log {
    /**
     * api日志相对目录
     * @var type 
     */

    const API_LOG_DIR = 'api';

    /**
     * 日志物理路径
     * @var string 
     */
    private static $_ci_log_path = '/data/logs/MoonApi/';

    /**
     * 日志物理路径
     * @var string 
     */
    private static $_api_log_path = '';

    /**
     * 开启按月分割日志 月目录
     * @var type 
     */
    private static $_createMonthDir = true;

    /**
     * log message -- general errorlog, need new logger
     * @param string $message
     * @param string $level
     * @param boolean $php_error
     * @return mixd
     */
    public function error_log($message, $level = 'error', $php_error = FALSE) {
        $this->_log_path = config_item("log_path") . ((self::$_createMonthDir) ? date('Y-m', time()) . "/" : "");
        $this->write_log($level, $message, $php_error);
    }

    /**
     * 设置api日志目录路径 日志目录分级
     */
    private static function setApiLogPath() {
        $ciPath = config_item('log_path');
        if (!$ciPath) {
            $ciPath = self::$_ci_log_path;
        }
        $apiLogPath = $ciPath . self::API_LOG_DIR . "/";
        $apiLogPath = $apiLogPath . ((self::$_createMonthDir) ? date('Y-m', time()) . "/" : "");
        if (!file_exists($apiLogPath)) {
            mkdir($apiLogPath, 0755, TRUE);
        }
        self::$_api_log_path = $apiLogPath;
    }

    /**
     * memcache日志 when cache what
     * @param string $message 日志信息
     */
    public static function cacheLog($message) {
        self::apiLog($message, "cache");
    }

    /**
     * 记录通过linux后台直接调用php执行相关脚本日志
     * @param type $message 日志信息
     */
    public static function commandLog($message) {
        self::apiLog($message, "command");
    }

    /**
     * php平台、ci框架、系统级别的错误或异常日志
     * @param string $message 错误信息
     */
    public static function errorLog($message) {
        self::apiLog($message, "error");
    }

    /**
     * 记录API日志  日志路径等及日志记录  默认记录正常http访问api日志
     * @param string $message 日志信息
     * @param string $type 日志类型
     */
    public static function apiLog($message, $type = "api") {
        $message = date("Y-m-d H:i:s") . "\n" . $message . "\n\n";
        self::setApiLogPath();
        $filename = self::$_api_log_path . $type . "_log_" . date('Y-m-d');
        self::log($filename, $message);
    }

    /**
     * 写日志
     * 
     * @param $filename string 文件名
     * @param $message string 日志信息
     * @param $mod string 打开模式
     * @throws Exception
     */
    public static function log($filename, $message, $mod = 'a') {
        if (config_item('log_threshold') == 0) {
            return;
        }
        $handle = @fopen($filename, $mod);
        //in linux root id is 0
        if (!fileowner($filename)) {
            chmod($filename, 0777);
        }
        if (!$handle) {
            // 尝试自动创建日志目录
            $parentFolder = dirname($filename);
            if (!file_exists($parentFolder)) {
                mkdir($parentFolder, 0777);
            }
            $handle = @fopen($filename, $mod);
            if (!$handle) {
                throw new Exception("创建日志文件失败!");
            }
        }
        if (FALSE === @fwrite($handle, $message)) {
            throw new Exception('日志文件写入失败');
        }
        fclose($handle);
    }

}

?>
