<?php

/**
 * 关于项目的一些配置控制
 *
 * @author 
 */
class server extends API_Controller {

    private $_specialZone = array('北京', '上海', '天津', '重庆', '香港', '澳门', '台湾');

    /**
     * 城市开关配置
     */
    public function sub_isenabled() {
        $areaModel = ModelHelper::getAreaModel();
        $isEnabled = $areaModel->getDeaultEnabled();
        $province = $this->getFromRequest('province', '');
        $city = $this->getFromRequest('city', '');
        if (!empty($province)) {
            $areaInfo = new stdClass();
            $areaInfo->province = $province;
            $areaInfo->city = $city;
        } else {
            $areaInfo = ClientHelper::getRemoteCity();
        }

        if (!empty($areaInfo) && !empty($areaInfo->province)) {
            if (in_array($areaInfo->province, $this->_specialZone)) {
                $areaInfo->city = $areaInfo->province;
            }
            $isEnabled = $areaModel->isCityEnabled($areaInfo->province, $areaInfo->city);
        }
        $this->responseSuccess($isEnabled);
    }

}

?>
