<?php

/**
 * 架构异常错误操作及错误日志
 *
 * @author jm
 */
class API_Exceptions extends CI_Exceptions {

        /**
         * php 或ci 框架上的语法、逻辑等异常或错误
         * @param type $severity
         * @param type $message
         * @param type $filepath
         * @param type $line
         */
        public function log_exception($severity, $message, $filepath, $line) {
                $severity = (!isset($this->levels[$severity])) ? $severity : $this->levels[$severity];
                $filepath = str_replace("\\", "/", $filepath);
                // For safety reasons we do not show the full file path
                if (FALSE !== strpos($filepath, '/')) {
                        $x = explode('/', $filepath);
                        $filepath = $x[count($x) - 2] . '/' . end($x);
                }
                require_once APPPATH . "helpers/api_code_helper.php";
                self::showMessage(CodeHelper::CODE_SYS_EXCEPTION, $severity . ' [ ' . $message . ' ] ' . rtrim($filepath, ".php") . ' ' . $line, '');
        }

        /**
         * 404 Not Found  - Rewrite show_404 in CI_Exceptions
         * @param type $page
         * @param type $log_error
         */
        public function show_404($page = '', $log_error = TRUE) {
                $uri = '';
                if (array_key_exists('REQUEST_URI', $_SERVER)) {
                        $uri = "'" . $_SERVER['REQUEST_URI'] . "' ";
                }
                $more = "The uri " . $uri . "you requested was not found.";
                self::showMessage(404, 'The uri you requested was not found.', $more);
        }

        /**
         * Show message in $format
         * @param int $code
         * @param array|object $data
         * @param array|object $more
         * @param string $format
         */
        public static function showMessage($code, $data, $more, $format = '') {
                require_once APPPATH . "helpers/api_response_helper.php";
                require_once APPPATH . "helpers/api_string_helper.php";
                require_once APPPATH . "helpers/api_code_helper.php";
                include_once APPPATH . 'helpers/api_track_helper.php';
                require_once APPPATH . "libraries/Logger.php";
                include_once APPPATH . 'config_' . strtolower(ENVIRONMENT) . "/config.php";
                ResponseHelper::renderResponse($code, $data, $more, $format);
        }

        /**
         * 服务器异常
         * @param string $more
         */
        public static function showServerException($more) {
                self::showMessage(CodeHelper::CODE_SYS_EXCEPTION, "服务器异常", $more);
        }

}

?>
