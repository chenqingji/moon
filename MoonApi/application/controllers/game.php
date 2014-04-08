<?php

/**
 * game
 * @author
 */
class game extends API_Controller {

        /**
         * anysdk统一登录地址
         * @var string
         */
        private $_loginCheckUrl = 'http://oauth.anysdk.com/api/User/LoginOauth/';

        /**
         * user
         * @var object
         */
        private $_user = null;

        /**
         * 测试
         */
        public function sub_test() {
                $this->responseSuccess('user test succeed');
        }

        /**
         * 登录api 含验证 - 游戏客户端
         * 由游戏客户端发起验证，经过游戏登录验证api，经过anysdk统一登陆验证，到渠道服务器上验证并返回验证信息
         */
        public function sub_login_verify() {
                $this->loginLog(json_encode($_REQUEST));
                //http请求中所有请求参数数组
                $params = $_REQUEST;

                //检测必要参数
                if (!$this->parametersIsset($params)) {
                        echo 'parameter not complete';
                        exit;
                }

                //模拟http请求
                $http = new HttpHelper();
                //这里建议使用post方式提交请求，避免客户端提交的参数再次被urlencode导致部分渠道token带有特殊符号验证失败
                $result = $http->post($this->_loginCheckUrl, $params);
                $this->loginLog($http->getUrl());
                $this->loginLog($result);

                $decodeResult = json_decode($result, true);
                if ($decodeResult['status'] == 'ok') {
                        $channelNumber = $decodeResult['common']['channel'];
                        $uid = $decodeResult['common']['uid'];
                        $user = $this->isUserExists($channelNumber, $uid);
                        if (empty($user)) {
                                $userId = $this->addUser($channelNumber, $uid);
                                $this->initUserGoods($userId);
                        } else {
                                $userId = $user->id;
                        }
                        $sessionId = $this->userSession($userId);
                        $decodeResult['ext'] = $sessionId;
                        $result = json_encode($decodeResult);
                }
                //$result如： {"status":"ok","data":{--渠道服务器返回的信息--},"common":{"channel":"000007","user_sdk":"nd91","uid":"用户标识"},"ext":""}
                echo $result;
        }

        /**
         * check needed parameters isset 
         * 检查必须的参数 channel uapi_key：渠道提供给应用的app_id或app_key（标识应用的id） uapi_secret：渠道提供给应用的app_key或app_secret（支付签名使用的密钥）
         * @param type $params
         * @return boolean
         */
        private function parametersIsset($params) {
                if (!(isset($params['channel']) && isset($params['uapi_key']) && isset($params['uapi_secret']))) {
                        return false;
                }
                return TRUE;
        }

        /**
         * 初始化用户物品及数值
         */
        private function initUserGoods($userId) {
                $allGoods = ModelHelper::getMoonGoodsModel()->getAllGoods();
                foreach ($allGoods as $one) {
                        $default = $one->default_value;
                        //@todo del all goods by userId;
                        $data = array(
                            "user_id" => $userId,
                            "goods_id" => $one->id,
                            "available" => $default,
                            "total" => $default,
                        );
                        ModelHelper::getMoonUserGoodsModel()->add($data);
                }
        }

        /**
         * 添加新道具
         * name money category desc
         * http://moon.anysdk.local/game/goods/new/?name=flightLife&money=50000&desc=user%20has%20some%20life%20to%20play&default=3
         * http://moon.anysdk.local/game/goods/new/?name=flightBomb&money=100&desc=flight%20bomb%20can%20....&default=3
         */
        public function sub_goods_new() {
                $data['name'] = $this->getFromRequest("name", null, true);
                $data['money'] = $this->getFromRequest("money", null, true); //分为单位
                $data['category'] = $this->getFromRequest("category", 0);
                $data['description'] = $this->getFromRequest("desc", null, true);
                $data['default_value'] = $this->getFromRequest("default", 0, true);

                $goods = ModelHelper::getMoonGoodsModel()->getGoodsByName($data['name']);
                if (empty($goods)) {
                        $id = ModelHelper::getMoonGoodsModel()->add($data);
                        $goods = ModelHelper::getMoonGoodsModel()->getGoodsById($id);
                } else {
                        $this->responseError(CodeHelper::CODE_RECODR_IS_EXISTS, "goods is exists;", $goods);
                }
                $this->responseSuccess($goods);
        }

        /**
         * 更新道具信息
         * name money category desc
         */
        public function sub_goods_update() {
                $id = $this->getFromRequest("id", null, true);
                $data['name'] = $this->getFromRequest("name", null, true);
                $data['money'] = $this->getFromRequest("money", null, true); //分为单位
                $data['category'] = $this->getFromRequest("category", 0);
                $data['description'] = $this->getFromRequest("desc", null, true);
                $data['default_value'] = $this->getFromRequest("default", 0, true);

                $goods = ModelHelper::getMoonGoodsModel()->getGoodsById($id);
                if (empty($goods)) {
                        $this->responseError(CodeHelper::CODE_RECORD_IS_NOT_EXISTS, "#" . $id . " goods is not exists;");
                } else {
                        ModelHelper::getMoonGoodsModel()->updateById($data, $id);
                        $goods = ModelHelper::getMoonGoodsModel()->getGoodsById($id);
                }
                $this->responseSuccess($goods);
        }

        /**
         * 删除道具
         */
        public function sub_goods_delete() {
                //@todo
        }

        /**
         * goods list
         */
        public function sub_goods_list() {
                $allGoods = ModelHelper::getMoonGoodsModel()->getAllGoods();
                $this->responseSuccess($allGoods);
        }

        /**
         * 同步道具使用 - 游戏客户端请求
         * goods_id  action count
         */
        public function sub_goods_rsync() {
                $this->checkLogin();
                $goodsId = $this->getFromRequest("goods_id", null, true);
//                $action = $this->getFromRequest("action", "reduce"); //reduce,increase
                $action = "reduce";
                $count = $this->getIntFromRequest("count", 0, TRUE);
                if ("reduce" == $action) {
                        $this->reduceGoods($this->_user->id, $goodsId, $count);
                }
                $userGoods = ModelHelper::getMoonUserGoodsModel()->getUserGoodsByUserAndGoods($this->_user->id, $goodsId);
                $available = $userGoods ? $userGoods->available : 0;
                $this->responseSuccess(array("available" => $available));
        }

        /**
         * 接收支付通知api - anysdk服务器支付通知请求
         * 2014-03-29 17:06:01  anysdk支付通知信息：
          Array
          (
          [channel_number] => 000023
          [order_type] => 97
          [user_id] => 142356598
          [game_id] =>
          [server_id] => 13
          [world_id] =>
          [order_id] => PB14032816415546085
          [product_id] => 101
          [product_name] => gold
          [product_count] => 1
          [amount] => 1
          [pay_status] => 1
          [pay_time] => 2014-03-28 16:42:36
          [game_user_id] => 1
          [app_order_id] =>
          [private_data] =>
          [uapi_key] => 3A5F7B04-3F9B-F613-BBD2-EFF8213EF842
          [sign] => fd19656e3280542aa4aebc81c78f3637
          )
         */
        public function sub_pay_notify() {
                $this->payLog($_REQUEST);
                $info = $_REQUEST;
                $this->checkPaySign();

                //or you can get user_id from channel_number and uid
                $this->increaseGoods($info['game_user_id'], $info['product_id'], $info['product_count']);

                echo "ok";

                ModelHelper::getMoonPayNotifyModel()->add($info);
        }

        /**
         * 检查支付通知签名
         * @param type $requestArray
         */
        private function checkPaySign($requestArray) {
                $sign = $requestArray['sign'];
                //@todo gene sign
                $newSign = $sign;
                if (strcmp($sign, $newSign) != 0) {
                        $this->responseError(50002, "支付通知签名验证失败");
                }
        }

        /**
         * 检测支付 并发放道具 - 游戏客户端主动请求
         */
        public function sub_pay_check() {
                $orderId = $this->getFromRequest("order_id", NULL, true);
                if ($orderId) {
                        $payNotifyInfo = ModelHelper::getMoonPayNotifyModel()->getByOrderId($orderId);
                        $this->responseSuccess(array(
                            'goods_id' => $payNotifyInfo['product_id'],
                            "goods_name" => $payNotifyInfo['product_name'],
                            "goods_count" => $payNotifyInfo['product_count'],
                            "user_id" => $payNotifyInfo['user_id']
                        ));
                }
                $this->responseSuccess();
        }

        /**
         * 检测用户是否已经存在
         */
        private function isUserExists($channelNumber, $uid) {
                $user = ModelHelper::getMoonUserModel()->getUserByChannelAndUid($channelNumber, $uid);
                if ($user) {
                        return true;
                }
                return false;
        }

        /**
         * 为当前用户产生一个会话
         */
        private function userSession($userId) {
                $sessionId = SessionHelper::start();
                $userObj = ModelHelper::getMoonUserModel()->getUserById($userId);
                SessionHelper::setUserInfo($userObj);
                SessionHelper::writeClose();
                return $sessionId;
        }

        /**
         * 第一次验证注册用户
         */
        private function addUser($channelNumber, $uid) {
                $data['channel_number'] = $channelNumber;
                $data['uid'] = $data['name'] = $uid;
                return ModelHelper::getMoonUserModel()->add($data);
        }

        /**
         * 增加用户道具个数
         * @param type $userId 用户id
         * @param type $goodsId 道具id
         * @param type $count 个数
         */
        private function increaseGoods($userId, $goodsId, $count) {
                ModelHelper::getMoonUserGoodsModel()->increaseUserGoodsCount($userId, $goodsId, $count);
        }

        /**
         * 减少用户道具个数
         * @param type $userId 用户id
         * @param type $goodsId 物品id
         * @param type $count 减少个数
         */
        private function reduceGoods($userId, $goodsId, $count) {
                ModelHelper::getMoonUserGoodsModel()->reduceUserGoodsCount($userId, $goodsId, $count);
        }

        /**
         * check login
         */
        protected function checkLogin() {
                $si = $this->getFromRequest("r", null, true);
                SessionHelper::start($si);
                $this->_user = SessionHelper::getUserInfo();
                if (empty($this->_user)) {
                        $this->responseSessionOvertime();
                }
        }

        /**
         * login log
         * @param type $message
         */
        private function loginLog($message) {
                Logger::apiLog($message, "login");
        }

        /**
         * pay log
         * @param type $message
         */
        private function payLog($message) {
                Logger::apiLog($message, "pay");
        }

}

?>
