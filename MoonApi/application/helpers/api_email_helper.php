<?php

/**
 * email工具集
 * @author 
 */
class EmailHelper {
    /**
     * 邮件工具sendmail
     */

    const MAIL_PROTOCOL_SENDMAIL = 'sendmail';
    /**
     * 邮件协议smtp
     */
    const MAIL_PROTOCOL_SMTP = 'smtp';

    /**
     * sendmail工具路径
     */
    const MAIL_PATH = '/usr/sbin/sendmail';
    /**
     * 邮件编码
     */
    const MAIL_CHARSET = 'utf-8';
    /**
     * 是否自动换行
     */
    const MAIL_WORDWRAP = TRUE;

    /**
     * 发送邮件
     * @param API_Controller $controller 调用该方法的controller对象
     * @param array $email 邮件信头信息
     * @param string $mailType text或html
     * @param string $returnDebug 调试信息
     * @return boolean 发送状态
     */
    public static function sendEmail($controller, $email, $mailType = 'html', $returnDebug = false) {
        $config['protocol'] = self::MAIL_PROTOCOL_SENDMAIL;
        $config['mailpath'] = self::MAIL_PATH;
        $config['charset'] = self::MAIL_CHARSET;
        $config['wordwrap'] = self::MAIL_WORDWRAP;
        $config['mailtype'] = $mailType;

        $controller->load->library('email');
        $controller->email->initialize($config);
        $controller->email->from($email['from'], $email['fromNickName']);
        $controller->email->to($email['to']);
        $controller->email->subject($email['subject']);
        $controller->email->message($email['message']);

        $sendStatus = $controller->email->send();
        if ($returnDebug) {
            return $controller->email->print_debugger();
        } else {
            return $sendStatus;
        }
    }

}

?>
