<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once (APPPATH . "third_party/smarty/Smarty.class.php");

/**
 * Template提供了使用模板引擎的操作接口
 * @author jm
 */
class Template extends Smarty {

    /**
     * 后缀格式
     * 
     * @var string
     */
    private $suffix = '.tpl';

    /**
     * 自定义viewpath
     */
    private $_viewPath = null;

    /**
     * 是否注释型标签
     */
    private $_isCommentTag = false;

    /**
     * 模板引擎构造函数
     */
    function __construct($viewPath = null, $_isCommentTag = false) {
        parent::__construct();
        if ($viewPath !== null) {
            $this->_viewPath = $viewPath;
        }
        $this->_isCommentTag = $_isCommentTag;
    }

    /**
     * 模板引擎显示方法
     */
    public function display($template = null, $cache_id = null, $compile_id = null, $parent = null) {
        $this->setTemplateParameter();
        $this->fetch($template, $cache_id, $compile_id, $parent, true);
    }

    /**
     * 解析模板获得html字符串
     * @param type $template 模板文件
     */
    public function tplFetch($template = null) {
        $this->setTemplateParameter();
        return $this->fetch($template);
    }

    /**
     * 设置模板引擎相关参数
     */
    private function setTemplateParameter() {
        $this->setTemplateDir(APPPATH . "views/");
        $this->setCompileDir(APPPATH . "template_c");
        $this->setCacheDir(APPPATH . "cache");
        $this->caching = false;
        if ($this->_isCommentTag) {
            $this->left_delimiter = "<!--{";
            $this->right_delimiter = "}-->";
        }
    }

}
