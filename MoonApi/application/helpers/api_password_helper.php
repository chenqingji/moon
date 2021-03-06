<?php

/**
 * 验证密码是否为弱密码
 */
class PasswordHelper {

    /**
     *
     * @var array 特殊弱密码数组
     */
    public static $weakPwdArr = array('abc123', '123abc', 'abc+123', 'test123', 'temp123', 'mypc123', 'admin123');

    /**
     *
     * @var string 用于配合验证弱密码的正则表达式
     */
    public static $weakReg = '/^([A-Z]+|[a-z]+|[0-9]+|[`~\!@#\$%\^&\*\(\)_\+\-=\{\}\|\[\]\\\:";\'<>\?\,\.\/]+)$/';

    /**
     *
     * @var string 配合验证密码键盘按键连续程度的字符串
     */
    public static $checkString = "abcdefghijklmnopqrstuvwxyz zyxwvutsrqponmlkjihgfedcba 1234567890 qwertyuiop asdfghjkl; zxcvbnm,./ 1qaz 2wsx 3edc 4rfv 5tgb 6yhn 7ujm 8ik, 9ol. 0p;/ /;p0 .lo9 ,ki8 mju7 nhy6 bgt5 vfr4 cde3 xsw2 zaq1 /.,mnbvcxz ;lkjhgfdsa poiuytrewq 0987654321";

    /**
     * 验证是否是弱密码
     * @param type $password 密码
     * @param type $anotherInput 不和密码一致的其它输入
     * @return boolean
     */
    public static function isWeakPassword($password, $anotherInput) {
        if (preg_match(self::$weakReg, $password)
                || $password === $anotherInput
                || in_array($password, self::$weakPwdArr)
                || self::checkDifWordNum($password)
                || self::checkKeybord($password)
                || stripos($password, $anotherInput) !== false) {
            return true;
        }
        return false;
    }

    /**
     * 密码中不同字母的个数 不小于4个
     * @param type $pwd
     * @return boolean
     */
    private static function checkDifWordNum($pwd) {
        $pwArray = str_split($pwd);
        $count = 1;
        $temString = $pwArray [0];
        for ($i = 0; $i < count($pwArray); $i++) {
            if (strpos($temString, $pwArray [$i]) !== false) {
                continue;
            } else {
                $count = $count + 1;
                $temString = $temString . $pwArray [$i];
            }
        }
        if ($count < 4) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 密码中键盘连续的字符串出现比例验证
     * 
     * @param $pwd string
     */
    private static function checkKeybord($pwd) {
        $pwArray = str_split($pwd);
        $lowLength = ceil((count($pwArray)) * 45 / 100);
        $checkString = self::$checkString;
        $subStr = "";
        $flag = false;
        $newPassword = self::replayShiftWord(strtolower($pwd));
        for ($i = 0; $i <= (strlen($newPassword) - $lowLength); $i++) {
            $subStr = substr($newPassword, $i, $lowLength);
            if (stripos($checkString, $subStr) !== false) {
                $flag = true;
                break;
            }
        }
        return $flag;
    }

    /**
     * 确认经过shift转义的字符在键盘上的位置
     * 
     * @param $pwd string
     * @return mixed
     */
    private static function replayShiftWord($pwd) {
        $pwd = str_replace('!', '1', $pwd);
        $pwd = str_replace('@', '2', $pwd);
        $pwd = str_replace('#', '3', $pwd);
        $pwd = str_replace('$', '4', $pwd);
        $pwd = str_replace('%', '5', $pwd);
        $pwd = str_replace('^', '6', $pwd);
        $pwd = str_replace('&', '7', $pwd);
        $pwd = str_replace('*', '8', $pwd);
        $pwd = str_replace('(', '9', $pwd);
        $pwd = str_replace(')', '0', $pwd);
        $pwd = str_replace('_', '-', $pwd);
        $pwd = str_replace('+', '=', $pwd);
        $pwd = str_replace('|', '\\', $pwd);
        $pwd = str_replace('<', ',', $pwd);
        $pwd = str_replace('>', '\.', $pwd);
        $pwd = str_replace('?', '/', $pwd);
        $pwd = str_replace(':', ';', $pwd);
        return $pwd;
    }

}