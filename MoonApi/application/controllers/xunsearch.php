<?php

/**
 * 主要处理后端批量处理script等 只通过后台执行
 * 使用方法：
 * php -f commandLine.php [控制器目录] 控制器名 [方法名]
 * commandLine.php在和ci框架system同级目录script下
 * @author jm
 */
class xunsearch extends API_Controller{
    
    /**
     * 显示xunsearch可执行的方法及方法的功能
     */
    public function sub_index(){
        $methods = get_class_methods("xunsearch");
        foreach($methods as $key=>$method){
            if(!preg_match("/sub_/", $method)){
                unset($methods[$key]);
                continue;
            }
            $methods[$key] = substr($method, 4);
        }
        echo "Method:\n";
        foreach ($methods as $method){
            echo "\t".$method."\n";
        }
    }
    
    /**
     * 提供help帮助
     */
    public function sub_help(){
        $this->sub_index();
    }

    /**
     * 一次性导入guidenode全文搜索索引数据  平滑重新索引 不需要先clean索引数据
     */
    public function sub_addGuideNodeFullSearchIndex() {
        $this->checkCommandAuth();
        $nodes = ModelHelper::getGuideNodeModel()->getAllActiveNodes();
        if ($this->addFullSearchIndex(Constants::FULL_SEARCH_NAME_GUIDE_NODE, $nodes)) {
            $this->responseSuccess("成功导入（重建）" . Constants::FULL_SEARCH_NAME_GUIDE_NODE . "全文搜索索引、数据");
        } else {
            $this->responseServerException('未完成平滑导入（重建）' . Constants::FULL_SEARCH_NAME_GUIDE_NODE . '全文搜索索引、数据');
        }
    }

    /**
     * 清空guidenode全文搜索索引数据
     */
    public function sub_cleanGuideNodeFullSearchIndex() {
        $this->checkCommandAuth();
        if ($this->cleanFullSearchIndex(Constants::FULL_SEARCH_NAME_GUIDE_NODE)) {
            $this->responseSuccess("成功清除" . Constants::FULL_SEARCH_NAME_GUIDE_NODE . "全文搜索索引、数据");
        } else {
            $this->responseServerException("未完成清除" . Constants::FULL_SEARCH_NAME_GUIDE_NODE . "全文搜索索引、数据");
        }
    }

    /**
     * 更新（增删改）guidenode全文搜索索引数据 数据更新后，xunsearch要有一定时间才会更新到服务器上；
     */
    public function sub_updateGuideNodeFullSearchIndex() {
        $this->checkCommandAuth();
        $lines = file(Constants::XUNSEARCH_DIR_PATH . 'app/' . Constants::FULL_SEARCH_NAME_GUIDE_NODE . ".lock");
        $updatedTime = trim($lines[0]);
        if (empty($updatedTime)) {
            $this->responseServerException(Constants::FULL_SEARCH_NAME_GUIDE_NODE . ".lock文件中的最后更新时间不存在，建议指定更新时间或平滑创建所有索引和数据");
        }
        $nodes = ModelHelper::getGuideNodeModel()->getNodesAfterUpdatedTime($updatedTime);
        if ($this->updateFullSearchIndex(Constants::FULL_SEARCH_NAME_GUIDE_NODE, $nodes, $updatedTime)) {
            $this->responseSuccess("成功更新" . Constants::FULL_SEARCH_NAME_GUIDE_NODE . "全文搜索引擎、数据（本次更新：" . date("Y-m-d H:i:s", $updatedTime) . "之后的数据)");
        } else {
            $this->responseServerException("未完成" . Constants::FULL_SEARCH_NAME_GUIDE_NODE . "全文搜索引擎、数据（本次更新：" . date("Y-m-d H:i:s", $updatedTime) . "之后的数据）");
        }
    }

    /**
     * 强制刷新guidenode项目搜索日志
     */
    public function sub_flushGuideNodeIndexLog() {
        $this->checkCommandAuth();
        $this->flushIndexLog(Constants::FULL_SEARCH_NAME_GUIDE_NODE);
    }

    /**
     * 增加-导入全文搜索引擎数据 
     * @param string $indexName 全文搜索项目名
     * @param array $nodes 数据 要导入的数据 array(objet('a'=>1...)...)
     * @return boolean 
     */
    private function addFullSearchIndex($indexName, $nodes = null) {
        LoadHelper::loadLibraries("XS.php");
        $xs = new XS($indexName);
        $index = $xs->index;
        $index->beginRebuild();

        //考虑如何使用索引缓冲区
        if ($nodes) {
            foreach ($nodes as $node) {
                $doc = new XSDocument();
                $doc->setFields(array(
                    "id" => $node->id,
                    "guideId" => $node->guideId,
                    "chapterId" => $node->chapterId,
                    "title" => $node->title,
                    "content" => strip_tags($node->content), //不带有html标签
                    "createdTime" => $node->createdTime,
                    "updatedTime" => $node->updatedTime
                ));
                $index->add($doc);
            }
        }

        $index->endRebuild();
        //update index update time
        $this->updateIndexLockUpdatedTime($indexName, time() - 30);
        return true;
    }

    /**
     * 清空全文搜索引擎数据
     * @param string $indexName 全文搜索引擎项目名
     * @return boolean
     */
    private function cleanFullSearchIndex($indexName) {
        LoadHelper::loadLibraries("XS.php");
        $xs = new XS($indexName);
        $index = $xs->index;
        $index->clean();
        return true;
    }

    /**
     * 强制刷新indexName项目的搜索日志    util/Indexer.php --flush-log --project demo
     * @param type $indexName 项目名
     */
    private function flushIndexLog($indexName) {
        echo "\n请执行以下命令(赋予Indexer.php执行权限)：\n";
        echo Constants::XUNSEARCH_DIR_PATH . "util/Indexer.php --flush-log --project " . $indexName . "\n";
    }

    /**
     * 更新全文搜索引擎数据
     * @param string $indexName 全文搜索引擎项目名称
     * @param array $nodes 要更新的数据 array(object('a'=>1...)...)
     * @return boolean
     */
    private function updateFullSearchIndex($indexName, $nodes = null, $updatedTime = 0) {
        if (empty($updatedTime)) {
            $lines = file(Constants::XUNSEARCH_DIR_PATH . 'app/' . $indexName . ".lock");
            $updatedTime = trim($lines[0]);
            if (empty($updatedTime)) {
                $this->responseServerException(Constants::FULL_SEARCH_NAME_GUIDE_NODE . ".lock文件中的最后更新时间不存在，建议指定更新时间或平滑创建所有索引和数据");
            }
        }

        LoadHelper::loadLibraries("XS.php");
        $xs = new XS($indexName);
        $index = $xs->index;

        //考虑如何使用索引缓冲区
        if ($nodes) {
            foreach ($nodes as $node) {
                if (isset($node->isDeleted) && $node->isDeleted) {
                    $index->del($node->id);
                } else {
                    $doc = new XSDocument();
                    $doc->setFields(array(
                        "id" => $node->id,
                        "guideId" => $node->guideId,
                        "chapterId" => $node->chapterId,
                        "title" => $node->title,
                        "content" => strip_tags($node->content),
                        "createdTime" => $node->createdTime,
                        "updatedTime" => $node->updatedTime
                    ));
                    $index->update($doc);
                }
            }
            $updatedTime = time();
        }
        $this->updateIndexLockUpdatedTime($indexName, $updatedTime);
        return true;
    }

    /**
     * 更新本次索引创建或更新的时间
     * @param type $indexName 项目名
     * @param type $updatedTime 更新时间
     * @return type
     */
    private function updateIndexLockUpdatedTime($indexName, $updatedTime) {
        if (empty($indexName) || empty($updatedTime)) {
            return;
        }
        $lockFile = Constants::XUNSEARCH_DIR_PATH . 'app/' . $indexName . ".lock";
        if (!file_exists($lockFile)) {
            fopen($lockFile, 'r');
        }
        chmod($lockFile, 0777);
        file_put_contents($lockFile, $updatedTime);
    }

}

?>
