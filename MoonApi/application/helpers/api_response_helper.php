<?php

/**
 * 请求响应
 *
 * @author jm
 */
class ResponseHelper {

        /**
         * 接口返回
         * @param int $code   状态号码
         * @param array $message   提示信息
         * @param array $data   返回数据
         * @param array $more 更多
         * @param string $format    格式
         */
        public static function renderResponse($code, $message = null, $data = null, $more = array(), $format = 'json') {
                $returnArray = array('code' => $code, 'message' => $message);
                if (!is_null($data)) {
                        $data = StringHelper::nullToEmpty($data);
                        $returnArray['data'] = $data;
                }
                if (CodeHelper::CODE_SUCCESS != $code) {
                        TrackHelper::mark("FAILED_MESSAGE", print_r($returnArray, true));
                }
                if (is_array($more) && $more) {
                        $returnArray = $returnArray + $more;
                }
                echo json_encode($returnArray);
                self::responseExit($code, $data);
        }

        /**
         * 退出
         */
        public static function responseExit($code = 10000, $data = null) {
                TrackHelper::mark("RequestEnd");
                TrackHelper::trackLog("ControllerStart", "RequestEnd", $code, $data);
                exit;
        }

}

?>
