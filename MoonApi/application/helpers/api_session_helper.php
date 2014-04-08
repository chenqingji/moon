<?php

/**
 * session helper 用于集中管理session，避免session散乱串了数据
 * @author jm
 */
class SessionHelper {

        const MOON_USER_INFO = 'moon_user_info';

        /**
         * 启用session
         * @param type $sessionId
         * @return type
         */
        public static function start($sessionId = '') {
                if (!empty($sessionId)) {
                        session_id($sessionId);
                }
                session_start();
                return session_id();
        }

        /**
         * 写session关闭
         */
        public static function writeClose() {
                session_write_close();
        }

        /**
         * 设置企业用户session
         */
        public static function setUserInfo($userInfo) {
                $_SESSION[self::MOON_USER_INFO] = $userInfo;
        }

        /**
         * 获取企业用户session
         * @return object |null
         */
        public static function getUserInfo() {
                if (isset($_SESSION[self::MOON_USER_INFO])) {
                        return $_SESSION[self::MOON_USER_INFO];
                }
                return null;
        }

        /**
         * 删除企业用户session
         */
        public static function removeUserInfo() {
                unset($_SESSION[self::MOON_USER_INFO]);
        }

        /**
         * free all session variables currently registered
         */
        public static function unsetAllSession() {
                session_unset();
        }

}

?>
