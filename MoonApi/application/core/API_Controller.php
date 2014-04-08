<?php

/**
 * api controller
 *
 * @author jm
 */
class API_Controller extends CI_Controller {
        /**
         * authcode加解密key
         */

        const API_AUTH_KEY = "fegn%;vw";

        /**
         * 注册检测key
         */
        const API_CHECK_KEY = "mM40X9ON";

        /**
         * 用户
         * @var user_model
         */
        public $entUser = null;

        /**
         * gamebox配置
         * @var array 
         */
        public $gameboxConfig = array();

        public function __construct() {
                parent::__construct();
                $this->trackSet();
                $this->headerUtf8();
                $this->headerAccessControlAllowOrigin();
                $this->userCheck();
        }

        /**
         * 默认action
         */
        public function index() {
                $this->resposnseCheckRequest();
        }

        /**
         * php输出补充头部指定资源类型及编码
         */
        public function headerUtf8() {
                if (array_key_exists('REQUEST_URI', $_SERVER)) {
                        header('Content-Type:text/html;charset=utf-8');
                }
        }

        /**
         * 跨域请求允许
         */
        public function headerAccessControlAllowOrigin() {
                //尽量限制可访问的域，避免css攻击
                if (array_key_exists('REQUEST_URI', $_SERVER)) {
                        header("Access-Control-Allow-Origin: *");
                }
        }

        /**
         * memcache保存方式session设置
         */
        public function memcacheSessionSetting() {
                /* memcached session */
                $cacheConfig = APPPATH . "config/cache.php";
                if (file_exists($cacheConfig)) {
                        $cache = require $cacheConfig;
                }
                if (!is_array($cache) || !isset($cache['port'])) {
                        $cache['server'] = 'localhost';
                        $cache['port'] = '11911';
                }
                $sessionSavePath = $cache['server'] . ":" . $cache['port'];
                ini_set('session.save_handler', 'memcached');
                ini_set('session.save_path', $sessionSavePath);
        }

        /**
         * 启动memcache保存方式session
         */
        public function memcacheSessionStart() {
                $this->memcacheSessionSetting();
                session_start();
        }

        /**
         * 跟踪设定
         */
        public function trackSet() {
                TrackHelper::mark("ControllerStart");
                TrackHelper::mark("CiPath", $this->router->directory . "/" . $this->router->class . "/" . $this->router->method);
        }

        /**
         * 过滤链
         */
        public function filterChain() {
                
        }

        /**
         * 用户检测是否已经注册登录的用户 关联用户系统
         * note: uuid is a authcode-encode sessionId to identify which sesssion to get
         */
        protected function userCheck($sessionId = '') {
                if (!$this->isNeedUserCheck()) {
                        return;
                }

                if ($sessionId) {
                        session_id($sessionId);
                        SessionHelper::start();
                        $userInfo = SessionHelper::getEntUserInfo();
                        if (is_null($userInfo)) {
                                $this->responseSessionOvertime();
                        } else {
                                $this->entUser = $userInfo;
                                SessionHelper::setEntUserInfo($userInfo);
                        }

                        SessionHelper::writeClose();
                }
        }

        /**
         * 请求的模块是否需要用户检测
         * @return boolean
         */
        private function isNeedUserCheck() {
                $noNeedCheckArray = include_once APPPATH . "config/access.php";

                $class = $this->router->class;
                $method = $this->router->method;
                $classMethods = $noNeedCheckArray[$class];
                if (array_key_exists($class, $noNeedCheckArray)) {
                        if (($classMethods == "*") || in_array($method, $classMethods)) {
                                return false;
                        }
                }
                return true;
        }

        /**
         * get model
         * @param string $modelClassName  model classname
         * @return API_Model
         */
        public function getModel($modelClassName) {
                $modelClassName .= "_model";
                if ($modelClassName) {
                        $this->load->model("$modelClassName");
                        return $this->$modelClassName;
                } else {
                        return null;
                }
        }

        /**
         * 获取参数
         * @param string $param key值
         * @param mixd $default 默认值
         * @param boolean $is_require 是否必须
         * @return mixd
         */
        public function getFromRequest($param, $default = null, $is_require = false) {
                $val = isset($_REQUEST["$param"]) ? $_REQUEST["$param"] : ($is_require ? null : (!is_null($default) ? $default : null));
                if (is_null($val) && $is_require) {
                        $this->response(CodeHelper::CODE_PARAM_REQUIRE, $this->getMessage(CodeHelper::CODE_PARAM_REQUIRE, array("{parameter}" => $param)));
                }
                return trim($val);
        }

        /**
         * 获得数字参数 
         * @param string $param key值
         * @param mixd $default 默认值
         * @param boolean $is_require   是否必须
         * @return mixd
         */
        public function getIntFromRequest($param, $default = null, $is_require = false) {
                $val = $this->getFromRequest($param, $default, $is_require);
                if (!preg_match("/[0-9]+/", $val) && !empty($val)) {
                        $this->response(CodeHelper::CODE_PARAM_REQUIRE_INTEGER, $this->getMessage(CodeHelper::CODE_PARAM_REQUIRE_INTEGER, array("{parameter}" => $param)));
                } else {
                        return $val;
                }
        }

        /**
         * 获得json解析对象
         * @param string $param key
         * @param string $default  默认
         * @param boolean $is_require   是否必须
         * @return array|object
         */
        public function getJsonFromRequest($param, $default = null, $is_require = false) {
                $jsonString = $this->getFromRequest($param, $default, $is_require);
                $iamarray = json_decode($jsonString, TRUE);
                if (empty($iamarray)) {
                        $this->response(CodeHelper::CODE_PARSE_JSON_FAIL, $this->getMessage(CodeHelper::CODE_PARSE_JSON_FAIL));
                }
                return $iamarray;
        }

        /**
         * 获得上传文件 转移 保存 获取新路径
         * @param type $name
         * @return string
         */
        public function getFileFromRequest($name, $is_require = false) {
                $filePath = "/tmp/test.txt";
                return $filePath;
        }

        /**
         * 检测至少需要其中一个参数值不为空
         * @param array $params array('name','sex','age'...)
         * @return boolean
         */
        public function needOneParamAtLeast($params) {
                $res = false;
                foreach ($params as $one) {
                        $res = (isset($_REQUEST[$one]) || $res);
                }
                return $res;
        }

        /**
         * 获取提示信息
         * @param string $key ngx_messaghe.php中数组对应key值
         * @param array $replace 替换message中的值  key=>value
         * @return mixd
         */
        public function getMessage($key, $replace = array()) {
                return I18nHelper::getMessage($key, $replace);
        }

        /**
         * 接口返回
         * @param int $code   状态号码
         * @param array $message   提示信息
         * @param array $data   返回数据
         * @param string $format    格式
         */
        public function response($code, $message = null, $data = null, $format = 'json') {
                ResponseHelper::renderResponse($code, $message, $data, $format);
        }

        /**
         * 错误接口返回
         * @param int $code   状态号码
         * @param array $message   提示信息
         * @param array $data   返回数据
         * @param string $format    格式
         */
        public function responseError($code, $message = null, $data = null, $format = 'json') {
                $message = is_null($message) ? $this->getMessage($code) : $message;
                ResponseHelper::renderResponse($code, $message, $data, '', $format);
        }

        /**
         * 接口成功返回
         * @param array $data   返回数据
         * @param array $message   成功提示信息
         * @param array $more 更多
         * @param string $format    格式
         */
        public function responseSuccess($data = null, $message = null, $more = array(), $format = 'json') {
                $message = is_null($message) ? $this->getMessage(CodeHelper::CODE_SUCCESS) : $message;
                ResponseHelper::renderResponse(CodeHelper::CODE_SUCCESS, $message, $data, $more, $format);
        }

        /**
         * 系统服务异常
         * @param string $message 异常信息
         * @param type $format 格式
         */
        public function responseServerException($message = null, $format = 'json') {
                $message = is_null($message) ? $this->getMessage(CodeHelper::CODE_SYS_EXCEPTION) : $message;
                ResponseHelper::renderResponse(CodeHelper::CODE_SYS_EXCEPTION, $message, null, '', $format);
        }

        /**
         * 会话超时
         * @param type $format 输出格式
         */
        public function responseSessionOvertime($format = 'json') {
                ResponseHelper::renderResponse(CodeHelper::CODE_SESSION_OVERTIME, $this->getMessage(CodeHelper::CODE_SESSION_OVERTIME), null, '', $format);
        }

        /**
         * 确认请求是否正确
         * @param string $format 输出格式
         */
        public function responseCheckRequest($format = 'json') {
                ResponseHelper::renderResponse(CodeHelper::CODE_CHECK_REQUEST, $this->getMessage(CodeHelper::CODE_CHECK_REQUEST), null, '', $format);
        }

        /**
         * 检测发起脚本是不是 commanLine.php 外部不可访问脚本
         * @return boolean
         */
        public function checkCommandAuth() {
                $phpSelf = $_SERVER['PHP_SELF'];

                if (!strpos($phpSelf, "commandLine.php") && $phpSelf != "commandLine.php") {
                        $this->responseSessionOvertime();
                } else {
                        return true;
                }
        }

        /**
         * 是否mysql操作出现错误，该方法为了补充框架中多层方法未返回相关操作结果，直接检测mysql是否报错并及时抛出异常
         * @return int  0表示正常
         * @throws Exception
         */
        public function isMysqlError() {
                if (mysql_errno()) {
                        throw new Exception("sql:" . mysql_error());
                }
                return mysql_errno();
        }

        /**
         * api接口退出
         */
        public function apiExit() {
                ResponseHelper::responseExit(CodeHelper::CODE_SUCCESS);
        }

        /**
         * In CI Framework will always be involked
         */
        public function _output() {
                $this->apiExit();
        }

}

?>
