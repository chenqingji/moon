<?php

/**
 * moon_pay_notify model
 * @author 
 */
class Moon_Pay_Notify_Model extends API_Model {
        /**
         * 表名
         */

        const TABLE_NAME = 'moon_pay_notify';

        /**
         * 构造器
         */
        public function __construct() {
                parent::__construct();
                $this->loadDatabase();
        }

        /**
         * 通用get
         * @param string/array $where   where
         * @param int $limit    limit
         * @param string $fields    fields
         * @return mixd
         */
        public function get($where = '', $limit = '', $fields = "") {
                return parent::get(self::TABLE_NAME, $where, $limit, $fields);
        }

        /**
         * get notify info by order id
         * @param type $orderId
         * @return null
         */
        public function getByOrderId($orderId) {
                if ($orderId) {
                        $objectArray = $this->db->where(array('order_id' => $orderId))->limit(1)->get(self::TABLE_NAME)->result();
                        return array_shift($objectArray);
                } else {
                        return null;
                }
        }

        /**
         * 添加一个应用
         * @param array|object $data
         * @return int
         * @throws Exception
         */
        public function add($data) {
                $id = null;
                if (is_array($data)) {
                        if (!isset($data['created_time'])) {
                                $data['updated_time'] = $data['created_time'] = time();
                        }
                        $this->db->insert(self::TABLE_NAME, $data);
                        $id = $this->db->insert_id();
                } else {
                        throw new Exception('$data is not array or object');
                }
                return $id;
        }

}

?>
