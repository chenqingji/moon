<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of API_Model
 *
 * @author jm
 */
class API_Model extends CI_Model {

        /**
         * database resource
         * @var CI_DB_active_record CI_DB_mysql_driver  CI_DB_driver
         */
        public $db = null;

        /**
         * sql查询默认limit
         * @var int  
         */
        private $_defaultLimit = 20;

        /**
         * 数据查询后得到的页码相关信息
         * @var array 
         */
        private $_page = array();

        /**
         * 构造器
         */
        public function __construct() {
                parent::__construct();
        }

        /**
         * load databases
         * @param string $dbName 数据库名
         */
        public function loadDatabase($dbName = '') {
                if (empty($dbName)) {
                        $dbName = 'default';
                }
                $this->db = $this->load->database($dbName, true);
//              $this->db = parent::__get("db");
        }

        /**
         * 生成页码相关信息
         * @param type $model   db
         * @param int $limit   要获取的记录数
         * @param int $offset  要获取的记录起始位置
         */
        public function getResultArray($model, $limit, $offset = 0, $countTotal = true) {
                if ($countTotal) {
                        $countModel = clone $model;
                        $total = $countModel->count_all_results();
                        $limit = $limit ? $limit : $total;
                } else {
                        $limit = $limit ? $limit : $this->_defaultLimit;
                }
                $result = $model->limit($limit, $offset)->get()->result_array();
                $count = count($result);
                if ($countTotal) {
                        $totalPages = $limit ? ceil($total / $limit) : 0;
                        $currentPage = $limit ? intval($offset / $limit) : 0 + 1;
                        $pageSize = min($limit, $count);
                        $this->_page = array('pageinfo' => array("pagenum" => $totalPages, "pageindex" => $currentPage, "pagesize" => $pageSize, "total" => $total));
                }
                return $result;
        }

        /**
         * 获得分页信息
         * @return type
         */
        public function getPageInfo() {
                return $this->_page;
        }

        /**
         * 获取表信息
         * @param string/array $where
         * @param int $limit
         * @param string $fields
         * @return mixd
         */
        public function get($tableName, $where = null, $limit = null, $fields = "") {
                if ($where) {
                        $this->db->where($where, null, false);
                }
                if ($limit) {
                        $this->db->limit($limit);
                }
                if (strcmp($fields, '') != 0) {
                        $this->db->select($fields);
                }
                $infos = $this->db->get($tableName)->result_array();

                if (($limit == 1) && $fields && (strpos($fields, ',') === false)) {
                        if (!empty($infos)) {
                                $info = array_shift($infos);
                                return $info["$fields"];
                        } else {
                                return null;
                        }
                }

                return $infos;
        }

}

?>
