<?php

/**
 * 系统验证工具集
 * @author 
 */
class ValidatorHelper {
    /**
     * 短信验证码有效时间
     */

    const SMS_AUTHCODE_SAVE_TIME = 1800;

    /**
     * 短信验证码cachekey分隔符号
     */
    const SMS_AUTHCODE_KEY_DELIMITER = "-";

    /**
     * 短信验证码 cachekey 默认前缀
     */
    const SMS_AUTHCODE_DEFAULT_PREFIX_KEY = "smsauthcode";

    /**
     * 检查号码是否合法
     * @param type $phoneNumber
     * @return boolean
     */
    public static function isPhoneNumber($phoneNumber) {
        $reg = "/^1[358][0-9]{9}$/";
        return preg_match($reg, $phoneNumber);
    }

    /**
     * 验证邮箱是否合法
     * @param type $address
     * @return boolean
     */
    public static function isEmail($address) {
        return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $address)) ? FALSE : TRUE;
    }

    /**
     * 随机生成纯数字验证码 短信验证码使用，页面验证码需要使用验证码组件
     * @param type $length 验证码长度
     * @param type $prefixKey 短信验证码cachekey前缀
     * @return null|string null mean get sms authcode failed
     */
    public static function getSmsAuthcode($length = 6, $prefixKey = null) {
        $prefixKey = is_null($prefixKey) ? self::SMS_AUTHCODE_DEFAULT_PREFIX_KEY : $prefixKey;
        $authcode = '';
        for ($i = 0; $i < $length; $i++) {
            $authcode .= rand(0, 9);
        }
        $key = $prefixKey . self::SMS_AUTHCODE_KEY_DELIMITER . $authcode;
        if (Cache::set($key, $authcode, self::SMS_AUTHCODE_SAVE_TIME, false)) {
            return $authcode;
        } else {
            return null;
        }
    }

    /**
     * 短信验证码验证
     * @param type $prefixKey 短信验证码cachekey前缀
     * @param type $authcode 短信验证码
     * @return boolean
     */
    public static function checkSmsAuthcode($authcode, $prefixKey = null) {
        $prefixKey = is_null($prefixKey) ? self::SMS_AUTHCODE_DEFAULT_PREFIX_KEY : $prefixKey;
        $key = $prefixKey . self::SMS_AUTHCODE_KEY_DELIMITER . $authcode;
        $cacheAuthcode = Cache::get($key);
        if ($cacheAuthcode && strcmp($authcode, $cacheAuthcode) == 0) {
            Cache::delete($key);
            return true;
        } else {
            return false;
        }
    }

}

?>
