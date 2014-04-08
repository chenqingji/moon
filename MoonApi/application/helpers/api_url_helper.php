<?php

/**
 * url操作
 * @author 
 */
class UrlHelper {

    /**
     * gamebox资源配置
     * @var type 
     */
    private static $_gameboxConfig = array();

    /**
     * 获得游戏图标 绝对链接地址
     * @param string $file
     * @return string
     */
    public static function getGameIconUrl($file) {
        return self::getFileUrl($file, "game_icon");
    }

    /**
     * 获得游戏apk 绝对链接地址
     * @param string $file
     * @return string
     */
    public static function getGameApkUrl($file) {
        return self::getFileUrl($file, "game_apk");
    }

    /**
     * 替换游戏分类图标为链接地址
     * @param string $file
     * @return string
     */
    public static function getCategoryIconUrl($file) {
        return self::getFileUrl($file, "game_category");
    }

    /**
     * 替换专题图标为url链接地址
     * @param string $file
     * @return string
     */
    public static function getFeatureIconUrl($file) {
        return self::getFileUrl($file, "game_feature");
    }

    /**
     * 头条推荐图片url链接地址
     * @param string $file
     * @return string
     */
    public static function getToplineImageUrl($file) {
        return self::getFileUrl($file, "game_topline");
    }

    /**
     * 游戏截图url链接地址
     * @param type $file
     * @param type $package
     * @return type
     */
    public static function getScreenshotUrl($file, $package) {
        $firstChar = substr($package, 0, 1);
        $file = "$firstChar/$package/$file";
        return self::getFileUrl($file, 'game_screenshot');
    }
    
    /**
     * 游戏增量更新包资源url链接
     * @param type $file
     * @return type
     */
    public static function getPackageIncrementUrl($file){
        return self::getFileUrl($file, 'game_increment');
    }

    /**
     * 获得助手 apk url
     * @param type $file
     * @return type
     */
    public static function getClientApkUrl($file) {
        $gameboxConfig = self::getGameboxConfig();
        if (strcmp($file, '') != 0) {
            $file = "http://" . $gameboxConfig['file_host'] . $gameboxConfig['client_apk_dir'] . $file;
        }
        return $file;
    }
    
    /**
     * 获得客户端助手增量更新包url
     * @param string $file
     * @return string
     */
    public static function getClientIncrementUrl($file){
        $gameboxConfig = self::getGameboxConfig();
        if (strcmp($file, '') != 0) {
            $file = "http://" . $gameboxConfig['file_host'] . $gameboxConfig['client_increment_dir'] . $file;
        }
        return $file;        
    }

    /**
     * 获得gamebox的相关配置
     * @return array
     */
    private static function getGameboxConfig() {
        if (self::$_gameboxConfig) {
            $gameboxConfig = self::$_gameboxConfig;
        } elseif (file_exists(APPPATH . "config/gamebox.php")) {
            self::$_gameboxConfig = $gameboxConfig = include_once APPPATH . "config/gamebox.php";
        }
        return $gameboxConfig;
    }

    /**
     * 获得gameboxConfig配置中的绝对或相对目录路径
     * @param type $type
     * @return type
     */
    public static function getDirUrl($type) {
        $gameboxConfig = self::getGameboxConfig();
        return $gameboxConfig[$type . '_dir'];
    }

    /**
     * 获得data资源目录下的目录的绝对路径含data自身
     * @param type $type
     * @return type
     */
    public static function getAbsoluteDirUrl($type) {
        $dataDir = self::getDirUrl('game_data');
        if ($type != 'game_data') {
            return $dataDir . self::getDirUrl($type);
        } else {
            return $dataDir . "/";
        }
    }

    /**
     * 替换资源文件为url链接地址
     * @param string $file
     * @param string $type  资源类型 关系目录名称
     * @return string
     */
    private static function getFileUrl($file, $type) {
        $gameboxConfig = self::getGameboxConfig();
        if (strcmp($file, '') != 0) {
            $file = "http://" . $gameboxConfig['file_host'] . $gameboxConfig[$type . '_dir'] . $file;
        }
        return $file;
    }

    /**
     * 获得游戏所有截图 绝对链接地址
     * @param string $thumbs
     * @return array
     */
    public static function getGameThumbsUrl($thumbs) {
        $returnThumbsArray = array();
        if (strcmp($thumbs, '') != 0) {
            $thumbsArray = explode(',', $thumbs);
        }
        if (self::$_gameboxConfig) {
            $gameboxConfig = self::$_gameboxConfig;
        } elseif (file_exists(APPPATH . "config/gamebox.php")) {
            self::$_gameboxConfig = $gameboxConfig = include_once APPPATH . "config/gamebox.php";
        } else {
            return $thumbsArray;
        }
        foreach ($thumbsArray as $thumb) {
            array_push($returnThumbsArray, "http://" . $gameboxConfig['file_host'] . $gameboxConfig['game_thumbs_dir'] . $thumb);
        }
        return $returnThumbsArray;
    }

}

?>
