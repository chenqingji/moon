<?php

/**
 * 加载内部工具类文件
 * @author 
 */
class LoadHelper {

    /**
     * 加载配置、helper、lib
     * @param type $filename
     * @param type $dirname
     */
    public static function load($filename, $dirname = 'config') {
        include_once APPPATH . $dirname . "/" . $filename;
    }

    /**
     * 加载配置
     * @param type $filename
     */
    public static function loadConfig($filename, $return = false) {
        $dirname = 'config';
        if ($return) {
            return include_once APPPATH .$dirname. "/" . $filename;
        } else {
            $this->load($filename, $dirname);
        }
    }

    /**
     * 加载帮助文件
     * @param type $filename
     */
    public static function loadHelpers($filename) {
        self::load($filename, 'helpers');
    }

    /**
     * 加载lib文件
     * @param type $filename
     */
    public static function loadLibraries($filename) {
        self::load($filename, 'libraries');
    }

}

?>
