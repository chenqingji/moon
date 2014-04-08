<?php

/**
 * 在线工具类
 */
class tool extends API_Controller {

        public function sub_success_list() {
                $param = $this->getFromRequest('param', NULL);
                if (empty($param)) {
                        $param = 'is-null';
                }
                $list = array('your-param' => $param, 'a' => 'apple', 'b' => 'banana', 'c' => 'cook', 'd' => array('qq', 'ww', 'ee'));
                $this->responseSuccess($list);
        }

        public function sub_fail_list() {
//        $this->response(CodeHelper::CODE_SYS_EXCEPTION);
                $this->response(CodeHelper::CODE_SYS_EXCEPTION, 'this is error message.');
        }

        public function sub_test() {
//                $title = 'http://www.anysdk.com/2014/02/8';
//                $link = "渠道管理； sdk相关参数管理； 批量制作渠道包； 发布包管理； 游戏相关资料管理； icon批量制作； 接入 <a style=\'text-decoration:none;\' href=\'http://www.anysdk.com/2014/02/8\'>[more]</a>";
//                $data = array("title"=>$title,'link'=>$link);
//                ModelHelper::getUcNoticeModel()->add($data);
                print_r($_REQUEST);exit;
        }

        /**
         * 用于解析经过json_encode汉字  如\u65e0\u6743\u8fdb\u884c\u64cd\u4f5c
         */
        public function sub_unescape() {
                $string = $this->getFromRequest('s');
                echo "<script>alert(unescape('" . $string . "'));</script>";
                exit;
        }

        /**
         * 用户ip
         */
        public function sub_client_ip() {
                $this->load->helper('api_client');
                echo ClientHelper::getRemoteRealIp();
        }

        /**
         * api请求统计
         */
        public function sub_request_statistics() {
                $startdate = $this->getFromRequest('startdate', '');
                $enddate = $this->getFromRequest('enddate', '');

                LoadHelper::loadLibraries('LogAnalyse.php');
                $output = LogAnalyse::collectKeywordLogMoreDay(LogAnalyse::KEYWORD_CI_PATH, strtotime($startdate), strtotime($enddate));

                $realStartTime = $output['startTime'];
                unset($output['startTime']);
                $realEndTime = $output['endTime'];
                unset($output['endTime']);

//        $output = LogAnalyse::collectKeywordLog(LogAnalyse::KEYWORD_CI_PATH, $date);
                $outputArray = LogAnalyse::cipathMap($output);

                $outputValues = array_values($output);
                sort($outputValues, SORT_NUMERIC);
                $maxCount = array_pop($outputValues);
                $extend = $maxCount / 1000; //默认请求最多的api 进度条宽为1000px
                //newProgressbar("progressbar1",1000);
                //<div class="line"><div id="progressbar1" title="123/xxx"></div><div class="lableDiv">&nbsp;&nbsp;12/xxx</div></div>
                $scriptString = "";
                $divHtml = '';
                $num = 1;
                foreach ($outputArray as $key => $one) {
                        $scriptString .= "newProgressbar('progressbar" . $num . "'," . ceil($one[1] / $extend) . ");";
                        $divHtml .= '<div class="line"><div id="progressbar' . $num . '" title="' . $one[1] . "/" . $one[0] . '"></div><div class="lableDiv">&nbsp;&nbsp;' . $one[1] . "/" . $one[0] . '</div></div>';
                        $num++;
                }


                LoadHelper::loadLibraries('Template.php');
                $tpl = new Template();
                $tpl->assign('scripts', $scriptString);
                $tpl->assign('divhtml', $divHtml);
                $tpl->assign("startdate", date("Y/m/d", $realStartTime));
                $tpl->assign("enddate", date("Y/m/d", $realEndTime));
//        $tpl->assign("startdate", $startdate);
//        $tpl->assign("enddate", $enddate);        
                $tpl->display('request_statistics.tpl');
        }

        /**
         * 测试curl模拟上传文件请求
         */
        public function sub_test_curl() {
                $data = array('name' => 'chenqingji', 'email' => 'chenqingji@163.com', 'apifile' => '@/GameBox/data/upload/chelper/1367133320008607800.apk');
                $url = 'http://localhost:8084/index.php?A=api_upload';
                LoadHelper::loadHelpers('api_request_helper.php');
                print_r(RequestHelper::curlPost($data, $url));
        }

        /**
         * 测试全文搜索
         */
        public function sub_test_search() {
                $keyword = $this->getFromRequest('keyword');

                LoadHelper::loadLibraries('XS.php');
                $xs = new XS(Constants::FULL_SEARCH_NAME_GUIDE_NODE);
                $search = $xs->getSearch();

//        $docs = $search->search($keyword);
                $docs = $search->setQuery($keyword)->setSort('id', true)->setFuzzy(false)->addRange('guideId', 7, 7)->search();
//        $matchCount = $search->count($keyword);
                $matchCount = count($docs);

                echo "总共匹配数：" . $matchCount . "<br />";
                if ($matchCount) {
                        foreach ($docs as $doc) {
                                $title = strip_tags($doc->title);
                                $content = strip_tags($doc->content);
                                echo "<br />"
                                . $doc->rank() . '.<br />ID:' . $doc->id . '<br />guideId:' . $doc->guideId . '<br />chapterId:' . $doc->chapterId . "<br />" . "匹配度： [" . $doc->percent() . "%]<br />"
                                . "最后更新时间：" . date("Y-m-d H:i:s", $doc->updatedTime) . "<br />标题：" . $search->highlight($title) . "<br />内容：" . $search->highlight($content)
                                . "<br />";

                                echo "-------------------------------------------------------------------------------------------------------------------------------------------<br/>";
                        }
                } else {
                        //需要后台去更新搜索日志分析才会有搜索纠正或建议  ./Indexer.php --flush-log gamebox
                        $correctedArray = $search->getCorrectedQuery($keyword);
                        $expandedArray = $search->getExpandedQuery($keyword);
                        print_r(array_unique(array_merge($correctedArray, $expandedArray)));
                        exit;
                }
        }

}

?>
