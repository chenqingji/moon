<?php

/**
 * 数组工具集
 * @author 
 */
class ArrayHelper {

    /**
     * 用于替换数组或对象key值
     * @param array $rows 二维数组
     * @param array $replaceArray 数组中要替换的字段 array("apple"=>"a","balana"=>"b")
     * @return array
     */
    public static function replaceArraysKey($rows, $replaceArray = array()) {
        if (!empty($replaceArray)) {
            foreach ($rows as $key => $row) {
                $rows[$key] = self::replaceArrayKey($row, $replaceArray);
            }
        }
        return $rows;
    }

    /**
     * 用户替换数组或对象中key值
     * @param type $row 一维数组
     * @param type $replaceArray    数组中要替换的字段 array("apple"=>"a","balana"=>"b")
     * @return array
     */
    public static function replaceArrayKey($row, $replaceArray = array()) {
        if (!empty($replaceArray)) {
            foreach ($replaceArray as $subject => $replace) {
                if (isset($row[$subject])) {
                    $row[$replace] = $row[$subject];
                    unset($row[$subject]);
                }
            }
        }
        return $row;
    }

    /**
     * 截取数组或对象中指定字段
     * @param array $rows   二维数组
     * @param array $subKeyArray 数组中要截取的字段 array('id','name','...')
     * @return array
     */
    public static function cutArraysColumn($rows, $subKeyArray = array()) {
        if (!empty($subKeyArray) && $rows) {
            foreach ($rows as $key => $row) {
                $rows[$key] = self::cutArrayColumn($row, $subKeyArray);
            }
        }
        return $rows;
    }

    /**
     * 截取数组或对象中指定的字段
     * @param type $row 一维数组
     * @param type $subKeyArray 数组中要截取的字段 array('id','name','...')
     * @return type
     */
    public static function cutArrayColumn($row, $subKeyArray = array()) {
        if (!empty($subKeyArray)) {
            foreach ($row as $key => $val) {
                if (!in_array($key, $subKeyArray)) {
                    unset($row[$key]);
                }
            }
        }
        return $row;
    }

    /**
     * 截取一行数据中部分字段、替换game_icon game_apk game_thumbs为全路径、替换部分字段名
     * @param array $rows   二维数组
     * @param array $subKeyArray 数组中要截取的字段 array('id','name','...')
     * @param array $replaceKeyArray 数组中要替换的字段 array("apple"=>"a","balana"=>"b")
     * @return array
     */
    public static function replaceGameInfo($rows, $subKeyArray = array(), $replaceKeyArray = array()) {
        $returnArray = array();
        foreach ($rows as $key => $row) {
            $row = self::cutArrayColumn($row, $subKeyArray);
            if (isset($row['iconUrl'])) {
                $row['iconUrl'] = UrlHelper::getGameIconUrl($row['iconUrl']);
            }
            if (isset($row['downloadUrl'])) {
                $row['downloadUrl'] = UrlHelper::getGameApkUrl($row['downloadUrl']);
            }
            if (isset($row['thumbs'])) {
                $row['thumbs'] = UrlHelper::getGameThumbsUrl($row['thumbs']);
            }
            if(isset($row['banner'])){
                $row['banner'] = UrlHelper::getGameIconUrl($row['banner']);
            }            
            if(isset($row['banner2'])){
                $row['banner2'] = UrlHelper::getGameIconUrl($row['banner2']);
            }
            array_push($returnArray, self::replaceArrayKey($row, $replaceKeyArray));
        }
        return $returnArray;
    }

    /**
     * 对象转换成数组  （递归转换）
     * @param array|object $data
     * @return array
     */
    public static function object2array($data) {
        $newarray = array();
        if (is_object($data)) {
            foreach ($data as $key => $value) {
                if (is_object($value)) {
                    $newarray[$key] = self::object2array($value);
                } else {
                    $newarray[$key] = $value;
                }
            }
        }else{
            $newarray = $data;
        }
        return $newarray;
    }

}

?>
