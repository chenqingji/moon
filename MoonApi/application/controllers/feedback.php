<?php

/**
 * 意见反馈
 * @author jm
 */
class feedback extends API_Controller{
    
    /**
     * 用户意见反馈 内容 联系方式
     */
    public function index(){
        $content = $this->getFromRequest('content', '', TRUE);
        $contact = $this->getFromRequest('contact', '', TRUE);

        if(strlen($contact) > 128){
            $this->response(CodeHelper::CODE_PARAM_TOO_LONG, $this->getMessage('feedback_contact_too_long'));
        }
        $id = ModelHelper::getFeedbackModel()->addFeedback(array('content'=>$content,'contact'=>$contact));
        if(is_null($id)){
            $this->responseServerException();
        }
        $this->responseSuccess($id);
    }
    
    /**
     * 获得一条反馈信息  for test
     */
    public function sub_get(){
        $feedbackId = $this->getIntFromRequest('id', null, true);
        $feedback = ModelHelper::getFeedbackModel()->getFeedback($feedbackId);
        $this->responseSuccess($feedback);
    }
}

?>
