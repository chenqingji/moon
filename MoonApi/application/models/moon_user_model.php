<?php

/**
 * moon_user model
 * @author 
 */
class Moon_User_Model extends API_Model {
        /**
         * 表名
         */

        const TABLE_NAME = 'moon_user';

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
         * add a company or corporation
         * @param array|object $data
         * @return int
         * @throws Exception
         */
        public function add($data) {
                $id = null;
                if (is_array($data) || is_object($data)) {
                        if (is_array($data) && !isset($data['created_time'])) {
                                $data['created_time'] = time();
                                $data['updated_time'] = time();
                        }
                        $this->db->insert(self::TABLE_NAME, $data);
                        $id = $this->db->insert_id();
                } else {
                        throw new Exception('$data is not array or object');
                }
                return $id;
        }

        /**
         * 通过id获得一条记录
         * @param type $id
         * @return null|object
         */
        public function getUserById($id) {
                if ($id) {
                        $objectArray = $this->db->where(array('id' => $id))->get(self::TABLE_NAME)->result();
                        return array_shift($objectArray);
                } else {
                        return null;
                }
        }

        /**
         * get user by name string
         */
        public function getUserByChannelAndUid($channelNumber, $uid) {
                $objectArray = $this->db->where(array('channel_number' => $channelNumber, 'uid' => $uid))->limit(1)->get(self::TABLE_NAME)->result();
                return array_shift($objectArray);
        }

}

?>
