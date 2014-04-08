<?php

/**
 * api return code define
 * @author jm
 */
class CodeHelper {
        /**
         * 操作成功
         */

        const CODE_SUCCESS = 200;

        /**
         * json解析错误
         */
        const CODE_PARSE_JSON_FAIL = 201;

        /**
         * 请求错误，没有响应文件请求
         */
        const CODE_PAGE_NOT_FOUND = 404;

        /**
         * 服务不可用
         */
        const CODE_SYS_EXCEPTION = 10000;

        /**
         * id不存在
         */
        const CODE_ID_NOT_EXISTS = 10001;

        /**
         * 文件太大
         */
        const CODE_FILE_TOO_LARGE = 10002;

        /**
         * 会话超时
         */
        const CODE_SESSION_OVERTIME = 10003;

        /**
         * {parameter}参数要求非空
         */
        const CODE_PARAM_REQUIRE = 10004;

        /**
         * {parameter}参数要求为整数
         */
        const CODE_PARAM_REQUIRE_INTEGER = 10005;

        /**
         * 缺少参数{parameter}
         */
        const CODE_PARAM_MISSING = 10006;

        /**
         * 请检查请求是否缺少参数
         */
        const CODE_CHECK_REQUEST = 10007;

        /**
         * 没有找记录等
         */
        const CODE_NOT_FOUND = 10008;

        /**
         * 用户已经存在-注册
         */
        const CODE_USER_EXIST = 10010;

        /**
         * 文件上传失败
         */
        const CODE_UPLOAD_FAIL = 10011;

        /**
         * 参数值太长
         */
        const CODE_PARAM_TOO_LONG = 10012;
        /**
         * 检查email是否正确
         */
        const CODE_CHECK_EMAIL_INPUT = 10013;
        /**
         * 检查手机号码是否正确
         */
        const CODE_CHECK_TELPHONE_INPUT = 10014;

        /**
         * 用户或密码错误
         */
        const CODE_USER_PWD_NOT_MATCH = 10015;

        /**
         * 已经存在记录
         */
        const CODE_RECODR_IS_EXISTS = 10016;

        /**
         * 不存在该记录
         */
        const CODE_RECORD_IS_NOT_EXISTS = 10017;
        
        /**
         * 无权操作
         */
        const CODE_NO_RIGHT = 10018;


        /**
         * 添加游戏失败
         */
        const CODE_GAME_ADD_FAILED = 20001;
        /**
         * 编辑游戏失败
         */
        const CODE_GAME_EDIT_FAILED = 20002;
        /**
         * 添加channel失败
         */
        const CODE_CHANNEL_ADD_FAILED = 20003;

        /**
         * 添加sdk失败
         */
        const CODE_SDK_ADD_FAILED = 20004;
        /**
         * 添加渠道参数失败
         */
        const CODE_USER_SDK_PARAM_ADD_FAILED = 20005;
        
        /**
         * 不存在渠道号：{idChannel}
         */
        const CODE_ANYSDK_CHANNEL_NOT_EXISTS = 20006;
        
        /**
         * 不存在SDK：{idSDK}
         */
        const CODE_ANYSDK_SDK_NOT_EXISTS = 20007;
        
        /**
         * 渠道信息更新失败：{idChannel}
         */
        const CODE_ANYSDK_CHANNEL_EDIT_FAILED = 20008;
        

}

?>
