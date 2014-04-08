<?php

/**
 * sms短信工具集
 */
class SmsHelper {
        /**
         * sms短信平台用户id
         */

        const SMS_UID = 'bjckkj-4';
        /**
         * sms短信平台用户密码
         */
        const SMS_PWD = 'c7cff1';
        /**
         * 短信平台接口url
         */
        const SMS_URL = 'http://si.800617.com:4400/SendSms.aspx';

        /**
         * 要发送的内容为空返回码
         */
        const CODE_CONTENT_EMPTY = -100;

        /**
         * 短信发送封装
         * @return string | if return 1 mean success 详见畅天游企信通标准短信接口文档信息
         */
        public static function sendSms($tel, $content = '') {
                if ($tel && trim($content)) {
                        $data = array
                            (
                            'un' => self::SMS_UID, //用户账号
                            'pwd' => self::SMS_PWD, //MD5位32密码
                            'mobile' => $tel, //号码
                            'msg' => $content, //内容
//                          'time'=>$time,	//定时发送
//                          'mid'=>$mid		//子扩展号
                        );
                        LoadHelper::loadHelpers('api_request_helper.php');
                        $result = RequestHelper::post(self::SMS_URL, $data, 'gb2312');
                        if (preg_match("/^result=(.+)&$/", trim($result), $matches)) {
                                $returnCode = $matches[1];
                        } else {
                                $returnCode = $result;
                        }
                } else {
                        $returnCode = self::CODE_CONTENT_EMPTY;
                }
                return self::converCodeToMessage($returnCode);
        }

        /**
         * 畅天游短信异常返回code
         *  -1 = 用户名和密码参数为空或者参数含有非法字符
          -2 = 手机号参数不正确
          -3 = msg参数为空或长度小于0个字符
          -4 = msg参数长度超过64个字符
          -6 = 发送号码为黑名单用户
          -8 = 下发内容中含有屏蔽词
          -9 = 下发账户不存在
          -10 = 下发账户已经停用
          -11 = 下发账户无余额
          -15 = MD5校验错误
          -16 = IP服务器鉴权错误
          -17 = 接口类型错误
          -18 = 服务类型错误
          -22 = 手机号达到当天发送限制
          -23 = 同一手机号，相同内容达到当天发送限制
          -99 = 系统异常
         * @param int $code 短信提示code
         * @return mix 成功时返回1  其他返回相应提示信息
         */
        public static function converCodeToMessage($code) {
                switch ($code) {
                        case -2:
                                $code = '手机号参数不正确';
                                break;
                        case -1:
                        case -3:
                        case -4:
                        case -6:
                        case -8:
                        case -9:
                        case -10:
                        case -11:
                        case -15:
                        case -16:
                        case -17:
                        case -18:
                        case -22:
                        case -23:
                        case -99:
                                $code = $code . ":短信发送失败";
                                break;
                }
                return $code;
        }

}

?>