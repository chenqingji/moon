<?php
/**
 * i18n
 * @author jm
 */
class I18nHelper {
	/**
	 * 简体中文标识
	 */
	const LANGUAGE_CN = "cn";

	/**
	 * 英文标识
	 */
	const LANGUAGE_EN = "en";

	/**
	 * 默认语言(简体中文)
	 */
	const DEFALUT_LANGUAGE = self::LANGUAGE_CN;
        

	/**
	 * 语言类型数组
	 */
	public static $LANGUAGE_ARRAY = array (
			self::LANGUAGE_CN => "简体中文", 
			self::LANGUAGE_EN => "English" );

	/**
	 * 国际化字符对应数组
	 */
	private static $_i18nArray = null;  
        
        /**
         * 当前控制器所在目录 eg: game/
         * @var type 
         */
        private static $_controllerDirectory = '';
        
        /**
         * 当前控制器名称  eg:game
         * @var type 
         */
        private static $_controller = '';
        
        /**
         * 初始化国际化数组-针对当前控制器及通用国际化
         */
        public static function initI18nArray(){
            $languageDir = APPPATH."language/";
            $language = config_item("language");
            $common = $result = array();
            
            $router = load_class("Router",'core');
            self::$_controllerDirectory = $router->directory;
            self::$_controller = $router->class;            
            
            $languageFile = $languageDir.$language."/".self::$_controllerDirectory.self::$_controller."_lang.php";
            $commonFile = $languageDir.$language."/common/common_lang.php";
            if(file_exists($commonFile)){
                $common = include_once $commonFile;
            }
            if(file_exists($languageFile)){
                $result = include_once $languageFile;
            }
            self::$_i18nArray = $common + $result;
        }
        
        /**
         * 获得国际化信息
         * @param type $key
         * @param type $replace
         * @return type
         * @throws Exception
         */
        public static function getMessage($key,$replace=array()){
            if(self::$_i18nArray == null){
                self::initI18nArray();
            }
            if(array_key_exists($key, self::$_i18nArray)){
                $message = self::$_i18nArray[$key];
                if($replace){
                    $message = str_replace(array_keys($replace), array_values($replace), $message);
                }
                return $message;
            }else{
                throw new Exception(self::$_controllerDirectory.self::$_controller."_lang not set :".$key);
            }
        }
        
        public static function getI18nArray(){
            return self::$_i18nArray;
        }
        
}

?>
