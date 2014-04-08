<?php

/**
 * 主要处理后端批量处理script等
 *  global $argv;可以引入参数  todo 
 * php -f commandLine.php [控制器目录] 控制器名 [方法名]
 * commandLine.php在和ci框架system同级目录script下
 * @author jm
 */
class script extends API_Controller {

    /**
     * ceshi
     * 例如：php -f commandLine.php script test
     */
    public function sub_test() {
        $this->checkCommandAuth();
        $this->responseSuccess('are you testing?');
    }

    /**
     * 清空服务器上所有cache数据
     */
    public function sub_flushCache() {
        $this->checkCommandAuth();
        if (Cache::flush()) {
            $this->responseSuccess("成功清空服务器所有cache数据");
        } else {
            $this->responseServerException('Flush cache failed.');
        }
    }

    /**
     * 服务器memcache重载及预加载部分必须的cache
     */
    public function sub_reloadCache() {
        $this->checkCommandAuth();
        echo "Flush Cache...\n";
        Cache::flush();
        echo "Reload Cache Start:\t" . time() . "\n";
        $this->cachePackagesStatus();
        echo "Cache Reload Completed:\t" . time() . "\n";
        $this->responseSuccess("成功重新加载服务器cache数据");
    }



    /**
     * 获得服务器指定key的cache 用于测试不提供外部使用
     */
    private function sub_getCache() {
        $packages = ModelHelper::getPackageInfoModel()->getAll();
        foreach ($packages as $package) {
            $cacheKey = 'status.' . $package['package'];
            print_r(json_decode(Cache::get($cacheKey)));
        }
    }

    /**
     * 设置游戏状态缓存
     * @global Cache $cache
     */
    private function cachePackagesStatus() {
        $packages = ModelHelper::getPackageInfoModel()->getAll();
        $gamesComments = $this->getAllPackagesCommentsUpdateTime();
        $gamesTips = $this->getAllPackagesGuideUpdateTime();

        foreach ($packages as $package) {
            $gameId = $package['id'];
            $gameVersion = $package['versionCode'];
            $gamePackage = $package['package'];
            $tmpArray = array();
            $tmpArray['pkg'] = $gamePackage ? $gamePackage : '';
            $tmpArray['comment'] = isset($gamesComments[$gameId]) ? intval($gamesComments[$gameId]) : 0;
            $tmpArray['tips'] = isset($gamesTips[$gameId]) ? intval($gamesTips[$gameId]) : 0;
            $tmpArray['version'] = intval($gameVersion ? $gameVersion : 0);
            $cacheValue = json_encode($tmpArray);
            $cacheKey = "status." . $gamePackage;
            Cache::set($cacheKey, $cacheValue);
        }
    }

    /**
     * 获取所有游戏评论最新更新时间
     * @return array    array("1"=>"123456789")
     */
    private function getAllPackagesCommentsUpdateTime() {
        $commentsUpdateArray = array();
        $rows = ModelHelper::getGameCommentsModel()->getAllPackagesCommentsLastTime();
        foreach ($rows as $line) {
            $gameId = $line["packageId"];
            $commentsUpdateArray["$gameId"] = $line["createdTime"];
        }
        return $commentsUpdateArray;
    }

    /**
     * 获取所有游戏攻略最新更新时间
     * @global type $db
     * @return array    array("1"=>"1234567890")
     */
    private function getAllPackagesGuideUpdateTime() {
        $returnArray = array();
        $guideArray = ModelHelper::getGuideModel()->getAllGameGuideLastTime();

        $packageInfoModel = ModelHelper::getPackageInfoModel();
        foreach ($guideArray as $line) {
            $packages = $packageInfoModel->getPackagesByGameId($line['gameId']);
            foreach ($packages as $onePackage) {
                $packageId = $onePackage['id'];
                $returnArray["$packageId"] = $line['updatedTime'];
            }
        }
        return $returnArray;
    }

}

?>
