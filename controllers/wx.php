<?php
    define("TOKEN", "ycyc");
    /**
     * 把数组转成xml
     */
    class array2xml
    {
        var $xml;
        function __construct($array) {
            $this->xml = $this->_array2xml($array);
        }
        function getXml() {
            return $this->xml;
        }
        function _array2xml($array)
        {
            $xml='';
            foreach($array as $key=>$val){
                if(is_numeric($key)){
                    $key="item";
                }else{
                    // 去掉空格，只取空格之前文字为key
                    list($key,) = explode(' ',$key);
                }
                $xml .= "<{$key}>";
                $xml.=is_array($val)?$this->_array2xml($val):$val;
                //去掉空格，只取空格之前文字为key
                list($key,)=explode(' ',$key);
                $xml.="</$key>";
            }
            return $xml;
        }
    }
    class weChat
    {
        var $postStr, $postObj;
        var $itemTpl = "<![CDATA[%s]]>";
        var $fromUsername, $toUsername, $msgId, $userMsgType, $event, $createTime;
        var $wxObj;
        var $home_page = "http://m.yicanyican.com";
        var $img_base = "http://www.yicanyican.com/img/wx";
        public function __construct() {
            // $this->postStr = file_get_contents('php://input');
            $this->postStr      = $GLOBALS["HTTP_RAW_POST_DATA"];
            $this->postObj      = simplexml_load_string($this->postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->postObj      = (array)$this->postObj;
            $this->toUsername   = $this->postObj['FromUserName'];
            $this->fromUsername = $this->postObj['ToUserName'];
            $this->msgId        = @$this->postObj['MsgId'];
            $this->userMsgType  = $this->postObj['MsgType'];
            $this->createTime   = time();
            $this->wxObj = new Wx();
        }

        public function valid()
        {
            $echoStr = $_GET["echostr"];
            //valid signature , option
            if($this->checkSignature()){
                echo $echoStr;
                exit;
            }
        }

        private function genItem($title, $picurl, $url = "", $description = "") {
            $title       = sprintf($this->itemTpl, $title);
            $description = sprintf($this->itemTpl, $description);
            $picurl      = sprintf($this->itemTpl, $picurl);
            $url         = sprintf($this->itemTpl, $url);
            return array(
                            'Title' => $title,
                            'Desription' => $description,
                            'PicUrl' => $picurl,
                            'Url' => $url
                        );
        }
        private function sendPicText($msg) {

            $toUserStr   = sprintf($this->itemTpl, $this->toUsername);
            $fromUserStr = sprintf($this->itemTpl, $this->fromUsername);

            $xml_array = array(
                            "xml"=>
                                array(
                                    "ToUserName"=>$toUserStr,
                                    "FromUserName"=>$fromUserStr,
                                    "CreateTime"=>$this->createTime,
                                    "MsgType"=>"news",
                                    "ArticleCount"=>count($msg),
                                    "Articles"=>$msg
                                )
                         );
            $xml = new array2xml($xml_array);
            $xml = $xml->getXml();
            return $xml;
        }

        public function sendText($contentStr) {
            $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        </xml>";
            return sprintf($textTpl, $this->toUsername, $this->fromUsername, $this->createTime, "text", $contentStr);
        }

        private function send_welcome() {
            $welcome_pic = "{$this->img_base}/640320_meitu_1.jpg";
            $order_directly_pic = "{$this->img_base}/8080_meitu_2.jpg";
            $login_pic = "{$this->img_base}/8080_meitu_3.jpg";
            $reg_pic = "{$this->img_base}/8080_meitu_4.jpg";

            // $userWxInfo = $this->wxObj->getUserIdWithWxId($this->toUsername);
            $userWxInfo = false;
            $msg = array();
            if ($userWxInfo) {
                $userId = $userWxInfo['usreId'];
                $last_university_id = $userWxInfo['last_wx_choose_university'];
                $msg[] = $this->genItem("欢迎您{$userId},开始点餐吧", $welcome_pic, $home_page);
                if ($last_university_id) {
                    $q = $this->db->select('schoolShortName, schoolFullName, imgLoc')
                                  ->where('schoolId', $last_university_id)
                                  ->get('schoolInfo');
                    if ($q->num_rows() > 0) {
                        $last_university_short = $q->row_array()['schoolShortName'];
                        $last_university_full = $q->row_array()['schoolFullName'];
                        $last_university_pic = $q->row_array()['imgLoc'];
                        $msg[] = $this->genItem("直接去{$last_university_full}点餐",
                                                "{$this->img_base}/univ/{$last_university_full}",
                                                "{$this->home_page}/restaurant/{$last_university_short}");
                    }
                }
                $msg[] = $this->genItem("查看历史订单", "", "{$this->home_page}/order");
            }
            else {
                $msg[] = $this->genItem("欢迎在一餐易餐订餐", $welcome_pic, $this->home_page);
                $msg[] = $this->genItem("直接订餐", $order_directly_pic, $this->home_page);
                $msg[] = $this->genItem("登录", $login_pic, "{$this->home_page}/login");
                // $msg[] = $this->genItem("注册一餐易餐", $reg_pic. "{$this->home_page}/register");
            }
            echo $this->sendPicText($msg);
        }
        private function dealClick() {
            $eventKey = $this->postObj['EventKey'];
            switch ($eventKey) {
                case "start_order": // 开始订餐
                    $this->send_welcome();
                    break;

                case "today_best":  // 今日推荐
                default:
                    break;
            }
        }

        private function dealEvent() {
            $event        = $this->postObj['Event'];
            switch ($event) {
                case "subscribe":
                    $this->wxObj->insert_new_user($this->toUsername);
                    $this->send_welcome();
                    break;
                case "unsubscribe":
                    $this->wxObj->delete_user($this->toUsername);
                    break;
                case "CLICK":
                    $this->dealClick();
                    break;
                default:
                    ;
            }
        }

        public function responseMsg()
        {
            if (!empty($this->postStr)) {
                switch ($this->userMsgType) {
                    case "event":
                        $this->dealEvent();
                        break;
                    case "text":
                        $key = $this->postObj->Content;
                        echo $this->sendText($key);
                        break;
                    case "location":
                        ;
                        // $label = $postObj->Label;
                        // $scale = $postObj->Scale;
                        // $location_x = $postObj->Location_X;
                        // $location_y = $postObj->Location_Y;
                        // $toMsgType = "text";
                        // $toMsg = "l=".$label.
                        //          "s=".$scale." ".
                        //          $location_x." ".$location_y.
                        //          " ".$fromUsername;
                        // $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $toMsgType, $toMsg);
                        // echo $resultStr;
                        // break;
                    default:
                        ;
                }
            }
        }

        private function checkSignature()
        {
            $signature = $_GET['signature'];
            $timestamp = $_GET['timestamp'];
            $nonce = $_GET['nonce'];

            $token = TOKEN;
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr);
            $tmpStr = implode( $tmpArr );
            $tmpStr = sha1( $tmpStr );

            if( $tmpStr == $signature ){
                return true;
            }else{
                return false;
            }
        }
    }

    /**
     * Controller for weixi
     */
    class Wx extends CI_Controller {
        public function __construct() {
            parent::__construct();
            $this->load->library('get_db_info');
        }

        public function getUserIdWithWxId($wxId) {
            $q = $this->db->select('userId, last_wx_choose_university')
                          ->where("wx_id",$wxId)
                          ->get('userInfo');
            if ($q->num_rows() > 0)
                return $q->row_array();
            else
                return false;

        }
        /**
         * insert a new user when subscribe
         * @param  string $user_wx_id user id
         * @return void
         */
        public function insert_new_user($user_wx_id) {
            $q = $this->db->select('wx_id')
                          ->where('wx_id', $user_wx_id)
                          ->get('wx_user');
            if ($q->num_rows() > 0) {
                $this->db->set('is_valid', '1');
                $this->db->where('wx_id', $user_wx_id);
                $this->db->update('wx_user');
            }
            else {
                $data = array('wx_id'=>$user_wx_id, 'is_valid'=>1);
                $this->db->insert('wx_user', $data);
            }
        }

        public function delete_user($user_wx_id) {
            $this->db->set('is_valid', '0');
            $this->db->where('wx_id', $user_wx_id);
            $this->db->update('wx_user');
        }

        public function index() {
            header("Content-Type: text/html; charset=UTF-8");
            $wechatObj = new weChat();
            $wechatObj->responseMsg();
        }

    }