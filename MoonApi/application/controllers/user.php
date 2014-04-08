<?php

/**
 * 企业用户信息
 *
 * @author 
 */
class User extends API_Controller {
        /**
         * 发送人 管理员邮箱
         */

        const MAIL_FROM = 'jm-cqj@163.com';

        /**
         * 查看用户信息
         */
        public function index() {
                $this->responseSuccess($this->user);
        }

        /**
         * add a enterprise user
         */
        public function sub_add() {
                $corporationId = $this->sub_corporation_add();
                if ($corporationId) {
                        $user = $this->getFromRequest('user', null, true);
                        $name = $this->getFromRequest('name');
                        $pwd = $this->getFromRequest('pwd', null, true);
//                        $email = $this->getFromRequest('email', null, true);
                        $data = array('user' => $user, 'name' => $name, 'pwd' => $pwd, 'is_admin' => 1, 'group_id' => 1, 'corporation_id' => $corporationId);
                        $userId = ModelHelper::getUcEntUserModel()->add($data);
                        $this->responseSuccess($userId);
                }
        }

        /**
         * add a corporation or a company with user
         */
        public function sub_corporation_add() {
                $name = $this->getFromRequest('corporation', null, true);
                $address = $this->getFromRequest('address');
                $email = $this->getFromRequest('email', null, true);

                $data = array('id' => 1, 'name' => $name, 'email' => $email);
                if (!empty($address)) {
                        $data['address'] = $address;
                }
                return ModelHelper::getUcEntCorporationModel()->add($data);
//                $this->responseSuccess();
        }

        /**
         * 修改用户信息
         */
        public function sub_edit() {
                //涉及到应用信息时需要及时更新游戏game_list表的公司名称xin
        }

        /**
         * 修改密码  修改密码前修改增加保密邮箱或绑定手机号码
         */
        public function sub_edit_password() {
                $md5InputOldPassword = $this->getFromRequest('old', null, true);
                $md5InputNewPassword = $this->getFromRequest('new', null, true);

                if (strcmp($md5InputOldPassword, $this->user->pass) == 0) {
                        if (strcmp($md5InputOldPassword, $md5InputNewPassword) == 0) {
                                $this->responseSuccess(10015, '新旧密码一致，密码未作更改');
                        } else {
                        $data = array();
                $data[ 'pass']

 =   $md5InputNewPassword    ; if(ModelHelper::getUserModel()->update($this->user->id, $data)) {

                        }
                        }
                        }
                        }

                        /**
                         * 找回密码 优先支持email 支持短信  凭什么条件找密码，email？imei imsi mac
                         */
                        public function sub_find_password() {
                        $to = $this->getFromRequest('email', null);
                        $tel = $this->getFromRequest('tel', null);

                        if($to) {
                        LoadHelper::loadHelpers('api_email_helper.php');
                        //to verify email validate @todo
                        if(!ValidatorHelper::isEmail($to)) {
                        $this->response(CodeHelper::CODE_CHECK_EMAIL_INPUT, $this->getMessage('check_input_email', array( 

                'email'  =>  $to)  ));
                                }
                                $emailArray = array();
                        $emailArray['subject'] = '忘记密码提示(zhushou.com)';
                $emailArray['message'] = $this->getFindPasswordEmailMessage();
                $emailArray['from'] = self::MAIL_FROM;
                $emailArray['fromNickName'] = 'admin-nick-' . date('Y-m-d H:i:s', time());
                $emailArray['to'] = $to;
                $return = EmailHelper::sendEmail($this, $emailArray, 'html', true);
        } elseif ($tel) {
                $content = '您好，感谢注册该软件，请输入手机验证码XXXXXX完成剩余操作。【索美科技】';
                LoadHelper::loadHelpers('api_sms_helper.php');
                if (!ValidatorHelper::isPhoneNumber($tel)) {
                        $this->response(CodeHelper::CODE_CHECK_TELPHONE_INPUT, $this->getMessage('check_input_telphone', array("tel" => $tel)));
                }
                $return = SmsHelper::sendSms($tel, $content);
        } else {
                $this->response(CodeHelper::CODE_PARAM_MISSING, $this->getMessage('missing_someone_parameter', array("{parameter}" => 'email || tel')));
        }
}

/**
 * 指定找回密码 邮件具体内容
 * @return type
 */
private function getFindPasswordEmailMessage() {
        $emailTitle = '忘记密码提示（zhushou.com）';
        $zhushouLogo = '';
        $userNickName = '耳东陈';
        $toModifyPasswordUrl = '';
        $toContactUsUrl = '';
        $toZhushouSiteUrl = '';

        return '
<STYLE type="text/css">  <!--@import url(scrollbar_7824.css); -->BODY { font-size: 14px; line-height: 1.5  } </STYLE>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>' . $emailTitle . '</title>
</head>
<body><table style="display:none;" width="1" height="1"><tr><td><img width="1" height="1" src="' . $zhushouLogo . '"></td></tr></table>
    <table width="650" border="0" align="center" cellpadding="0" cellspacing="0" style="border: 1px solid #999999;">
        <tr>
            <td style="font-size: 12px; line-height: 20px; text-align: left;">
                <table border="1" cellspacing="0" cellpadding="0" style="width: 465.0pt; height: 72px;
    margin-left: 7.5pt; border: none; border-bottom: solid #333333 1.0pt">
    <tr>
    </tr>
</table>
                <table width="620" border="0" cellpadding="0" cellspacing="0" style="margin-left: 10px;">
                    <tr>
                        <td style="font-size: 12px; line-height: 25px; padding-top: 10px;">
                            <strong>尊敬的' . $userNickName . '，您好:</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="line-height: 20px; padding-top: 0px; font-size: 12px;">
                            您在zhushou（zhushou.com）点击了“忘记密码”按钮，故系统自动为您发送了这封邮件。您可以点击以下链接修改您的密码：<br />
                            <a href="' . $toModifyPasswordUrl . '"></a>
                        </td>
                    </tr>
                    <tr>
                        <td style="line-height: 20px; padding-top: 8px; font-size: 12px;">
                            此链接有效期为两个小时，请在两小时内点击链接进行修改，每天最多允许找回5次密码。如果您不需要修改密码，或者您从未点击过“忘记密码”按钮，请忽略本邮件。
                            <br />
                        </td>
                    </tr>
                    <tr>
                        <tr>
                            <td style="line-height: 20px; padding-top: 2px; font-size: 12px;">
                                <p>
                                    如有任何疑问，请联系zhushou客服，客服热线：<span lang="EN-US" xml:lang="EN-US">400-400-400</span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br />
                            </td>
                        </tr>
                        <tr>
    <td style="line-height: 40px; font-size: 12px">
        <strong>欢迎您再次使用zhushou相关工具！<span lang="EN-US" xml:lang="EN-US"></span></strong>
    </td>
</tr>
<tr>
    <td align="left" style="border: none; padding: 1.5pt 0cm 7.5pt 0cm; border-top: dashed #999999 1.0pt">
        <p style="line-height: 15.0pt">
            <span style="font-size: 9.0pt; color: #999999">您之所以收到这封邮件，是因为您曾经游戏好助手zhushou的用户。<br />
                本邮件由zhushou系统自动发出，请勿直接回复！<br />
                如果您有任何疑问或建议，请<a href="' . $toContactUsUrl . '">联系我们</a><br />
                zhushou官方网站（<a href="' . $toZhushouSiteUrl . '">zhushou.com</a>）
                - 游戏上你的好伙伴</span></p>
    </td>
</tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
';
}

}

?>
