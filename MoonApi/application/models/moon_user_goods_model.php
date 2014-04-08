<?php

/**
 * uc_ent_user model
 * id content contact created_time
 * @author 
 */
class Moon_User_Goods_Model extends API_Model {
        /**
         * 表名
         */

        const TABLE_NAME = 'moon_user_goods';

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
         * 添加一个用户
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
        public function getUserGoodsById($id) {
                if ($id) {
                        $objectArray = $this->db->where(array('id' => $id))->limit(1)->get(self::TABLE_NAME)->result();
                        return array_shift($objectArray);
                } else {
                        return null;
                }
        }

        /**
         * get user by user string
         */
        public function getUserGoodsByUserAndGoods($userId, $goodsId) {
                if ($userId && $goodsId) {
                        $objectArray = $this->db->where(array('user_id' => $userId, "goods_id" => $goodsId))->limit(1)->get(self::TABLE_NAME)->result();
                        return array_shift($objectArray);
                } else {
                        return null;
                }
        }

        /**
         * 减少指定用户道具个数
         * @param type $userId
         * @param type $goodsId
         * @param type $count 
         */
        public function reduceUserGoodsCount($userId, $goodsId, $count = 1) {
                if ($userId && $goodsId) {
                        $userGoods = $this->getUserGoodsByUserAndGoods($userId, $goodsId);
                        if ($userGoods) {
                                $count = ($userGoods->available > $count) ? $count : $userGoods->available;
                                $sql = "update " . self::TABLE_NAME . " set available=available-" . $count . " where user_id=" . $userId . " and goods_id=" . $goodsId;
                                $this->db->query($sql);
                        }
                }
        }

        /**
         * 增加指定用户道具个数
         * @param type $userId
         * @param type $goodsId
         * @param type $count 
         */
        public function increaseUserGoodsCount($userId, $goodsId, $count = 1) {
                if ($userId && $goodsId) {
                        $userGoods = $this->getUserGoodsByUserAndGoods($userId, $goodsId);
                        if ($userGoods) {
                                $sql = "update " . self::TABLE_NAME . " set available=available+" . $count . " where user_id=" . $userId . " and goods_id=" . $goodsId;
                                $this->db->query($sql);
                        } else {
                                $currentTime = time();
                                $data = array(
                                    "user_id" => $userId,
                                    "goods_id" => $goodsId,
                                    "available" => $count,
                                    "total" => $count,
                                    "created_time" => $currentTime,
                                    "updated_time" => $currentTime
                                );
                                $this->add($data);
                        }
                }
        }

}

?>
