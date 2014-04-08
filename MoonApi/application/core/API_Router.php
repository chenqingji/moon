<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of API_Router
 *
 * @author jm
 */
class API_Router extends CI_Router{
    
    /**
     * 获得控制器所在目录
     * @return string
     */
    public function getControllerDirectory(){
        return $this->directory;
    }
    
    /**
     * 获得控制器
     * @return type
     */
    public function getController(){
        return $this->class;
    }
    
    /**
     * 获得控制器操作方法
     * @return type
     */
    public function getMethod(){
        return $this->method;
    }
    
}

?>
