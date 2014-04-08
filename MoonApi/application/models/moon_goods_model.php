<?php

/**
 * uc_app model
 * id content contact created_time
 * @author 
 */
class Moon_Goods_Model extends API_Model {
        /**
         * 表名
         */

        const TABLE_NAME = 'moon_goods';

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

        /**
         * update by idChannel and idSDK
         * @param type $data
         * @param type $id
         * @return boolean
         */
        public function updateById($data, $id) {
                if (!isset($data['updated_time'])) {
                        $data['updated_time'] = time();
                }
                return $this->db->update(self::TABLE_NAME, $data, array('id' => $id));
        }

        /**
         * 通过id获取一条记录
         * @param type $id
         * @return null|object
         */
        public function getGoodsById($id) {
                if ($id) {
                        $objectArray = $this->db->where(array('id' => $id))->get(self::TABLE_NAME)->result();
                        return array_shift($objectArray);
                } else {
                        return null;
                }
        }

        /**
         * 获取所有物品信息
         * @return type
         */
        public function getAllGoods() {
                return $this->db->get(self::TABLE_NAME)->result();
        }

        /**
         * 通过name获取一条记录
         * @param type $name
         * @return null|object
         */
        public function getGoodsByName($name) {
                if ($name) {
                        $objectArray = $this->db->where(array('name' => $name))->get(self::TABLE_NAME)->result();
                        return array_shift($objectArray);
                } else {
                        return null;
                }
        }

        /**
         * delete app by idSDK
         * @param type $idSDK
         * @return boolean
         */
        public function deleteById($id) {
                if ($id) {
                        return $this->db->delete(self::TABLE_NAME, array('id' => $id));
                }
                return true;
        }

        /**
         * delete app by idSDK and idChannel
         * @param type $idChannel
         * @param type $idSDKk
         * @return boolean
         */
        public function deleteAppByIcAndIs($idChannel, $idSDK) {
                if ($idChannel && $idSDK) {
                        return $this->db->delete(self::TABLE_NAME, array('idChannel' => $idChannel, 'idSDK' => $idSDK));
                }
                return true;
        }

}

?>
