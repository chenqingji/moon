<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DbClass
 *
 * @author asus
 */
class DbClass {

        const DB_HOST = '127.0.0.1';
        const DB_USER = 'ucenter';
        const DB_PASSWORD = 'Agcg$?iQbN';

        private $_conn = null;

        public function getDbConnection() {
                if (!$this->_conn) {

                        $this->_conn = mysql_connect(self::DB_HOST, self::DB_USER, self::DB_PASSWORD);
                }

                return $this->_conn;
        }

        //put your code here
}

?>
