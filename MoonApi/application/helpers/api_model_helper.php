<?php

/**
 * Description of api_modelService_helper
 *
 * @author 
 */
class ModelHelper {

        private static $_modelArray = array();

        /**
         * get model
         * @param string $modelClassName  model classname
         * @return API_Model
         */
        public static function getModel($modelClassName) {
                $modelClassName .= "_model";
                if (!array_key_exists($modelClassName, self::$_modelArray)) {
                        $loader = new CI_Loader(); //todo待整改
                        if ($modelClassName) {
                                $loader->model("$modelClassName");
                                $controller = get_instance();
                                $model = $controller->$modelClassName;
                                self::$_modelArray[$modelClassName] = $model;
                                return $model;
                        }
                } else {
                        return self::$_modelArray[$modelClassName];
                }
        }

        /**
         * moon_user
         * @return Moon_User_Model
         */
        public static function getMoonUserModel() {
                return self::getModel("moon_user");
        }

        /**
         * moon_goods
         * @return Moon_Goods_Model
         */
        public static function getMoonGoodsModel() {
                return self::getModel('moon_goods');
        }

        /**
         * moon_user_goods
         * @return Moon_User_Goods_Model
         */
        public static function getMoonUserGoodsModel() {
                return self::getModel("moon_user_goods");
        }

        /**
         * moon_login_info model
         * @return Moon_Login_Info_Model
         */
        public static function getMoonLoginInfoModel() {
                return self::getModel('moon_login_info');
        }

        /**
         * moon_pay_notify model
         * @return Moon_Pay_Notify_Model
         */
        public static function getMoonPayNotifyModel() {
                return self::getModel('moon_pay_notify');
        }

}

?>
