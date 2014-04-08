<?php

/**
 * uc_game_list model
 * id content contact created_time
 * @author 
 */
class Moon_Login_Info_Model extends API_Model {
        /**
         * 表名
         */

        const TABLE_NAME = 'moon_login_info';

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
         * 添加一个游戏
         * @param array|object $data
         * @return int
         * @throws Exception
         */
        public function add($data) {
                $id = null;
                if (is_array($data)) {
                        if (is_array($data) && !isset($data['create_time'])) {
                                $data['create_time'] = time();
                                $data['update_time'] = time();
                        }
                        $this->db->insert(self::TABLE_NAME, $data);
                        $id = $this->db->insert_id();
                } else {
                        throw new Exception('$data is not array or object');
                }
                return $id;
        }

        /**
         * 更改游戏配置
         * @param type $gameId
         * @param type $data
         * @return null
         * @throws Exception
         */
        public function edit($gameId, $data) {
                if (is_array($data)) {
                        if (is_array($data) && !isset($data['update_time'])) {
                                $data['update_time'] = time();
                        }
                        $this->db->where('game_id', $gameId);
                        return $this->db->update(self::TABLE_NAME, $data);
                } else {
                        throw new Exception('$data is not array or object');
                }
                return null;
        }

        /**
         * 通过id获取一个游戏
         * @param type $id
         * @return null|object
         */
        public function getGameById($id) {
                if ($id) {
                        $objectArray = $this->db->where(array('game_id' => $id))->get(self::TABLE_NAME)->result();
                        return array_shift($objectArray);
                } else {
                        return null;
                }
        }

        /**
         * get game by corporationId and Gamename
         * @param type $corporationId
         * @param type $gameName
         * @return null
         */
        public function getGameByCiAndGn($corporationId, $gameName) {
                if ($gameName && $corporationId) {
                        $objectArray = $this->db->where(array('game_name' => $gameName, 'corporation_id' => $corporationId))->limit(1)->get(self::TABLE_NAME)->result();
                        return array_shift($objectArray);
                } else {
                        return null;
                }
        }

        /**
         * get games by corporationId 
         * @param type $corporationId
         * @return array
         */
        public function getGamesByCorporationId($corporationId) {
                if ($corporationId) {
                        return $this->db->where(array('corporation_id' => $corporationId))->get(self::TABLE_NAME)->result_array();
                }
                return array();
        }
        
        /**
         * delete game by gameid
         * @param type $gameId
         * @return type
         */
        public function deleteGameByGameId($gameId){
                return $this->db->delete(self::TABLE_NAME, array('game_id'=>$gameId), 1);
        }

}

?>
