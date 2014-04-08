<?php
/**
 * crond  每天间隔8小时更新一次rss from www.anysdk.com/feed
 */

$rss = new AnysdkRss();
$rss->getRss();

/**
 * get www.anysdk.com/feed rss
 */
class AnysdkRss {

        /**
         * rss url
         */
        const RSS_URL = "http://www.anysdk.com/category/news/feed";
        /**
         * dbname
         */
        const DEFAULT_DB_NAME = 'punchbox_ucenter_v5';

        /**
         * db object
         * @var type 
         */
        private $_db = null;

        public function __construct() {
                include './DbClass.php';
                $this->_db = new DbClass();
        }

        /**
         * get db connection resource
         * @return type
         */
        private function getConn() {
                return $this->_db->getDbConnection();
        }

        /**
         * get rss from rss_url
         */
        public function getRss() {
                $xml = simplexml_load_file(self::RSS_URL);
                $lastLine = $this->getLastRecord();
                if ($lastLine) {
                        $lastUpdateTime = strtotime(date("Y-m-d", $lastLine['updated_time']));
                } else {
                        $lastUpdateTime = 0;
                }
                foreach ($xml->channel->item as $value) {
                        $data = array();
                        $data['public_time'] = strtotime(rtrim($value->pubDate, "+0000"));
                        if ($data['public_time'] < $lastUpdateTime) {
                                echo $value->title." exists =>continue;\n";
                                continue;
                        }else{
                                echo $value->title." begin =>insert;\n";
                        }
                        $data['title'] = $value->title;
                        $data['link'] = $value->link;
                        $data['description'] = rtrim($value->description, "[&#8230;]") . "<a style='text-decoration:none;' href='" . $data['link'] . "'>[more]</a>";
                        $this->editUcNotice($data);
                }
        }

        /**
         * get last notice record
         * @return type
         */
        private function getLastRecord() {
                $conn = $this->getConn();
                mysql_select_db(self::DEFAULT_DB_NAME);
                $sql = "select * from uc_notice order by public_time limit 1";
                $res = mysql_query($sql) or die(mysql_error());
                $line = array_shift(mysql_fetch_array($res));
//                $lastUpdateTime = $line['updated_time'];
//                $dateStartTime = strtotime(date("Y-m-d",$lastUpdateTime));
                return $line;
        }

        /**
         * insert or edit notice
         * @param type $data
         */
        public function editUcNotice($data) {

                $data = array_map("mysql_escape_string", $data);
                $conn = $this->getConn();
                mysql_select_db(self::DEFAULT_DB_NAME);
                $sql = "select * from uc_notice where title='" . ($data['title']) . "' and link='" . ($data['link']) . "'";
                $res = mysql_query($sql) or die(mysql_error());
                if (!mysql_num_rows($res)) {
                        echo "inserting;\n";
                        $currentTime = time();
                        $inSql = "insert into uc_notice(title,link,description,public_time,created_time,updated_time) 
                                values('" . $data['title'] . "','" . $data['link'] . "','" . $data['description'] . "','" . $data['public_time'] . "','" . $currentTime . "','" . $currentTime . "')";
                        mysql_query($inSql) or die(mysql_error());
                }else{
                        echo"skip;\n";
                }
        }

}
?>