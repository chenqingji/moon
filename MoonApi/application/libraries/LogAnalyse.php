<?php

/**
 * 对api的log分析
 */
class LogAnalyse {
    /**
     * 关键词CiPath
     */

    const KEYWORD_CI_PATH = 'CiPath';
    /**
     * 关键词STATUS
     */
    const KEYWORD_STATUS = 'STATUS';
    /**
     * 关键词REMOTE
     */
    const KEYWORD_REMOTE = 'REMOTE';
    /**
     * 关键词ELAPSEDTIME
     */
    const KEYWORD_ELAPSEDTIME = 'ELAPSEDTIME';
    /**
     * api日志目录
     */
    const API_LOG_DIR = "/var/log/ci/api/";
    /**
     * api日志文件名前缀
     */
    const API_LOG_FILENAME_PREFIX = 'api_log_';
    /**
     * 临时文件生成目录
     */
    const SED_LOG_DIR = '/tmp/sedlog/';
    /**
     * 临时文件名前缀
     */
    const SED_LOG_FILENAME_PREFIX = 'sed_api_log_';

    /**
     * cipath 对应描述
     * @var array 
     */
    private static $_ciPathArray = array(
        '/client/sub_shop_setting' => '游戏商店开关（被动）',
        '/client/sub_pkg_update' => '游戏更新（被动）',
        '/client/sub_newest_get' => '助手更新（被动）',
        '/entrance/index' => '助手注册登录（被动）',
        '/feedback/index' => '反馈（主动）',
        '/game/sub_whitelist' => '所有白名单（被动）',
        '/game/sub_comments_list' => '游戏吐槽列表（主动）',
        '/game/sub_main_recommend' => '主推游戏（被动）',
        '/game/sub_whitelist_custom' => '指定游戏白名单（被动）',
        '/game/sub_whitelist_recommend' => '指定游戏推荐白名单（被动）',
        '/game/sub_comments_add' => '游戏吐槽（主动）',
        '/game/sub_screenshot_add' => '截图分享（主动）',
        '/tips/sub_chapter' => '攻略章列表（主动）',
        '/tips/sub_node' => '攻略节列表（主动）',
        '/tips/sub_get' => '攻略内容（主动）',
        '/tips/sub_comments_newest' => '攻略最新吐槽（主动）',
        '/tips/sub_comments_add' => '攻略吐槽（主动）',
        '/tips/sub_search' => '攻略搜索（主动）',
        '/tool/sub_request_statistics' => 'api请求统计（主动）',
        '/server/sub_isenabled' => '助手地区开关（被动）',
    );

    /**
     * 通过sed工具读取含有关键词的行
     * @param type $keyword
     * @param type $time 要求计算关键词频率的时间--用于确定要计算的文件
     * @return array array('time'=>1234567667,'sedFile'=>'/tmp/sedlog/xxxx')
     */
    public static function cutOutApiLogBySed($keyword = '', $time = 0) {
        if (empty($time)) {
            $time = time();
        }
        $dateYm = date("Y-m", $time);
        $dateYmd = date("Y-m-d", $time);
//        $keyword = $keyword ? $keyword : self::KEYWORD_CI_PATH;
        if (strcmp($keyword, '') == 0) {
            echo "请输入日志截取关键词比如" . self::KEYWORD_CI_PATH . "\t" . self::KEYWORD_ELAPSEDTIME . "\t" . self::KEYWORD_REMOTE . "\t" . self::KEYWORD_STATUS . "\n";
            exit(0);
            return;
        }

        $logFilePath = self::API_LOG_DIR . $dateYm . "/" . self::API_LOG_FILENAME_PREFIX . $dateYmd;
        if (!file_exists(self::SED_LOG_DIR)) {
            mkdir(self::SED_LOG_DIR);
        }
        $sedApiLogFilePath = self::SED_LOG_DIR . self::SED_LOG_FILENAME_PREFIX . $dateYmd;
        if (!file_exists($logFilePath)) {
            return null;
        }
        //读cache 没有的话生成临时文件 供collectKeywordLogMoreDay计算并写入cache
        $cache = Cache::get($sedApiLogFilePath);
        if (!$cache) {
            if (!file_exists($sedApiLogFilePath)) {
                $command = "sed -n '/" . $keyword . "/'p '" . $logFilePath . "' >> " . $sedApiLogFilePath;
                $outputString = shell_exec($command);
            }
        }
        return array('time' => $time, 'sedFile' => $sedApiLogFilePath);
    }

    /**
     * 通过sed循环查询关键词并生成各自结果文件
     * @param type $keyword
     * @param type $startTime
     * @param type $endTime
     * @return array array(0=>array('time'=>1234567667,'sedFile'=>'/tmp/sedlog/xxxx')...)
     */
    public static function cutOutApiLogBySedMoreDay($keyword, $startTime = 0, $endTime = 0) {
        if (!isset($startTime) || empty($startTime)) {
            $startTime = time();
        }
        if (!isset($endTime) || empty($endTime)) {
            $endTime = time();
        }

        $maxSpanTime = 30 * 24 * 3600;
        $spanTime = ($endTime - $startTime);
        $spanTime = ($spanTime > $maxSpanTime) ? $maxSpanTime : ($spanTime < 0 ? 0 : $spanTime);
        $oneDayTime = 24 * 3600;
        $howManyDay = round($spanTime / $oneDayTime);

        $allArray = array();
        for ($day = 0; $day <= $howManyDay; $day++) {
            $tmpArray = self::cutOutApiLogBySed($keyword, $startTime + 24 * 3600 * $day);
            if ($tmpArray) {
                $allArray[] = $tmpArray;
            }
        }
        return $allArray;
    }

    /**
     * 计算关键词出现的频率
     * @param type $keyword 关键词
     * @param type $startTime 开始时间
     * @param type $endTime 结束时间
     * @return array
     */
    public static function collectKeywordLogMoreDay($keyword = '', $startTime = 0, $endTime = 0) {
        $allArray = self::cutOutApiLogBySedMoreDay($keyword, $startTime, $endTime);
        $countDays = count($allArray);
        $realStartTime = $allArray[0]['time'];
        $realEndTime = $allArray[$countDays - 1]['time'];
        $countArray = array();

        foreach ($allArray as $key => $oneDay) {
            $sedApiLogFilePath = $oneDay['sedFile'];
            //有cache读cache 没有的话读文件并写入cache最后删除临时文件
            $cache = Cache::get($sedApiLogFilePath);
            if (!$cache) {
                if (!file_exists($sedApiLogFilePath)) {
                    continue;
                }
                $handle = fopen($sedApiLogFilePath, 'r');
                $oneCountArray = array();
                while (!feof($handle)) {
                    $line = trim(fgets($handle));
                    if (strpos($line, ':') !== false) {
                        list($key, $value) = explode(":", $line, 2);
                    } else {
                        continue;
                    }
                    $value = trim($value);
                    if (strpos($value, ' ')) {
                        $value = substr(0, strpos($value, ' '));
                    }
                    $oneCountArray[$value] = isset($oneCountArray[$value]) ? ($oneCountArray[$value] + 1) : 1;
                }
                if (strcmp($sedApiLogFilePath, self::SED_LOG_DIR . self::SED_LOG_FILENAME_PREFIX . date("Y-m-d", time())) != 0) {
                    Cache::set($sedApiLogFilePath, serialize($oneCountArray));
                }
                fclose($handle);
                echo 'a';
                @unlink($sedApiLogFilePath);
            } else {
                echo 'b';
                $oneCountArray = unserialize($cache);
            }
            $countArray = self::mergeAllCount($countArray, $oneCountArray);
        }
        arsort($countArray); //排序
        $countArray['startTime'] = $realStartTime;
        $countArray['endTime'] = $realEndTime;
        return $countArray;
    }

    /**
     * 合并计算每天各种请求数
     * @param type $countArray
     * @param type $oneCountArray
     * @return array
     */
    public static function mergeAllCount($countArray, $oneCountArray) {
        $length = count($oneCountArray);
        foreach ($oneCountArray as $key => $value) {
            $countArray[$key] = isset($countArray[$key]) ? ($countArray[$key] + $value) : $value;
        }
        return $countArray;
    }

    /**
     * 增加统计中的方法名描述
     * @param type $countArray
     * @return array
     */
    public static function cipathMap($countArray) {
        $returnArray = array();
        foreach ($countArray as $key => $one) {
            if (isset(self::$_ciPathArray[$key])) {
                $returnArray[$key] = array(self::$_ciPathArray[$key], $one);
            } else {
                $returnArray[$key] = array($key, $one);
            }
        }
        return $returnArray;
    }

}

?>
