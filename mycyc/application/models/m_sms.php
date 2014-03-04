<?php
	class M_sms extends CI_Model {
		var $smsapi = "api.smsbao.com"; //短信网关
		var $charset = "utf8"; //文件编码
		var $user = SMS_ID; //短信平台帐号
		var $pass;

		public function __construct() {
			$this->load->database();
			$this->load->library('get_db_info');
			$this->pass = md5(SMS_PASSWORD); //短信平台密码

			// caixuntong
			$this->CAIXUNTONGURL = "http://sdk.entinfo.cn:8060/webservice.asmx/";
			$this->CAIXUNTONGSN = "DXX-WSS-10C-05978";
			$this->CAIXUNTONGPWD = "753250";
			require_once(LOC_PREFIC."/lib/snoopy.php");
		}

		public function get_set_code($phone) {
			srand((double)microtime()*1000000);
			$code = rand(1000,9999);
			$data = array(
						"telephone"=>"$phone",
						"four_bit_code"=>"$code"
					);
			$this->db->insert('user_confirm_sms', $data);
			return $code;
		}
		public function check_code($phone, $code) {
			$msg=array();
			$msg['valid'] = false;
			$query =    $this->db->select('time_submit')
								 ->where('telephone', $phone)
								 ->where('four_bit_code', $code)
								 ->order_by('time_submit', "desc")
								 ->limit(1)
								 ->get('user_confirm_sms');
			if ($query->num_rows() > 0) {
				$submit_time = $query->row_array()['time_submit'];
				$submit_time = strtotime($submit_time);
				if (time() - $submit_time > PHONE_VALID_TIME)
					$msg['tel_con'] = '超时';
				else
					$msg['valid'] = true;
			}
			else {
				$msg['tel_con'] = '验证码错误';
			}
			return $msg;
		}
		// 返回值2：表示已经使用过该号码
		// 1=>空号
		// 0=>第一次使用
		public function check_phone_avai($phone) {
			$q = $this->db->select('telephone')
						  ->where('telephone', $phone)
						  ->where('confirmed', '1')
						  ->get('first_telephone');
			if ($q->num_rows() > 0)
				return '2';

			// 产生验证码
			srand((double)microtime()*1000000);
			$code = rand(1000,9999);
			// 如果是首次，首先先发短信
			$return_code = $this->_send($phone, "欢迎您首次在一餐易餐订餐，您的验证码为".$code."，为确认您的手机号，请在5分钟内在电脑上输入验证码以便订单确认，谢谢。");
			// $return_code = '0';
			if ($return_code) {
				$q = $this->db->select('telephone')
						  ->where('telephone', $phone)
						  ->get('first_telephone');

				if ($q->num_rows() > 0) {

					$this->db->set('createTime', date("Y-m-d H:i:s"));
					$this->db->set('code', $code);
					$this->db->where('telephone', $phone);
					$this->db->update('first_telephone');
				}
				else {
					$data = array('telephone'=>$phone,
								  'createTime'=>date('Y-m-d H:i:s'),
								  'code'=>$code);
					$this->db->insert('first_telephone', $data);
				}
				return '0';
			}
			else
				return '1';
		}

		private function __send($currentSender, $phone, $content) {
			$snoopy = new snoopy();
			if (DUANXINBAO == $currentSender) {
				$sendurl = "http://{$this->smsapi}/sms?u={$this->user}&p={$this->pass}&m={$phone}&c=".urlencode($content."【一餐易餐】");
				$snoopy->fetch($sendurl);
				if ($snoopy->results == '0')
					return true;
				return false;
			} else if (HUAXIN == $currentSender) {
				$hx_usr_id = "1784";
				$account = "sc10243";
				$password = "314159zlc";
				$content = urlencode($content);
				$sendurl = "http://121.101.221.34:8888/sms.aspx?action=send&userid={$hx_usr_id}&account={$account}&password={$password}&mobile={$phone}&content={$content}";
				$snoopy->fetch($sendurl);
				$xml = htmlspecialchars_decode($snoopy->results);
				$xml = simplexml_load_string($xml);
				$returnsms = $xml; //->returnsms;
				if ($returnsms->returnstatus == "Success" && $returnsms->message == "ok") {
					$money_left = $returnsms->remainpoint;
					if ($money_left < SMS_EMERGENCY_NUM) {
						$content = urlencode("一餐易餐余额少于100，请尽快充值, 短信平台编号{$currentSender}");
						$sendurl = "http://121.101.221.34:8888/sms.aspx?action=send&userid={$hx_usr_id}&account={$account}&password={$password}&mobile=".EMERGENCY_PHONE."&content={$content}";
						$snoopy->fetch($sendurl);
					}
					return true;
				}
				return false;
			} else if (CAIXUNTONG == $currentSender) {
				$sn = $this->CAIXUNTONGSN;
				$pass = $sn . $this->CAIXUNTONGPWD;
				$pass = strtoupper(md5($pass));
				$content = $content . "【易餐·千红】";
				$content = mb_convert_encoding($content, "GB2312", "UTF-8");
				// $content = @iconv( "UTF-8", "gb2312//IGNORE" , $content);
				$content = urlencode($content);
				$url = $this->CAIXUNTONGURL . "gxmt?sn={$sn}&pwd={$pass}&mobile={$phone}&content={$content}&ext=&stime=&rrid=";
				// echo $url;
				// echo "<br />";
				$snoopy->fetch($url);
				// var_dump($url);
				// $re = @iconv("gb2312", "UTF-8", $snoopy->results);
				$re = $snoopy->results;
				$len = strlen("<string xmlns=\"http://tempuri.org/\">");
				if (substr($re, $len, 1) == '-') {
					return false;
				}
				return true;
				// $len_end = strpos($re, "</string>");

				// $xml = $re; // htmlspecialchars_decode($re);
				// $xml = simplexml_load_string($xml);
				// $returnsms = $xml; //->returnsms;
				// var_dump($returnsms);

				// echo "------\n";
				// var_dump($return_a);
				// var_dump($return->state);
				// echo "------\n";

				// var_dump($return_a['state']);
				// var_dump($returnsms->state);
			} else
				return false;
		}

		private function _send($phone, $content) {
			// echo $phone, $content;
			// 返回 '0' 视为发送成功，其他内容为错误提示内容
			$q = $this->db->select('disableSms, currentSender')
						  ->get('configure');
			$disableSms = $q->row_array()['disableSms'];
			$currentSender = $q->row_array()['currentSender'];
			$data = array('msgPhone'=>$phone,
			              'msgContent'=>$content,
			              'msgSender'=>$currentSender,
			              'is_disabled'=>$disableSms);
			$this->db->insert('sms_text', $data);

			if (1 == $disableSms)
				return false;

			if (DUANXINBAO == $currentSender) {
				$snoopy = new snoopy();
				$left_url = "http://www.smsbao.com/query?u={$this->user}&p={$this->pass}";
				$snoopy->fetch($left_url);
				$result = $snoopy->results;
				sscanf($result, "%d", $v);
				if ($v == 0) {
					sscanf($result, "%d %d,%d", $v, $u, $l);
					if ($l < SMS_EMERGENCY_NUM)
						$this->__send($currentSender, EMERGENCY_PHONE, "一餐易餐余额少于100，请尽快充值, 短信平台编号{$currentSender}");
				}
			}

			/// send messages
			if (DUANXINBAO == $currentSender) {
				if (strlen($content) >= 320) {
					$left = mb_substr($content, 313);
					$content = mb_substr($content, 0, 313);
					$this->__send($phone, $content);
					return $this->__send($currentSender, $phone, $left);
				} else {
					return $this->__send($currentSender, $phone, $content);
				}
			} else
				return $this->__send($currentSender, $phone, $content);
		}
		public function send_welcome($phone) {

			$code = $this->get_set_code($phone);
			$content = '欢迎您注册一餐易餐，您的验证码是：'.$code.'。请在一分钟内输入，谢谢您的注册~';
			// if ($this->_send($phone, $content) == 0)
				return true;
			// return false;
		}

		public function send_urgent($sale_id) {
			$content = "有催单，订单号{$sale_id}，请马上处理";
			$this->_send(URGENT_PHONE, $content);
		}

		public function send_pei($sale_id) {
			$content = "有超时白吃，订单号{$sale_id}，请马上处理";
			$this->_send(URGENT_PHONE, $content);
		}
		public function send_order($sale_id, $id, $phone, $taste, $a, $loc) {
			$content = $sale_id.'#'.$id.':';
			$sum = 0;
			foreach ($a as $key => $b) {
				$key_1 = $key + 1;
				$content .= "框{$key_1}:";
				foreach ($b as $c) {
					$content .= "[".$c['food_name'].
							    "(".$c['food_price'].")"."x".$c['food_num']."]";
					$sum += $c['food_num'] * $c['food_price'];
				}
			}

			$content .= " 地址：".$loc['addr']." 长号".$loc['l_tel']."-".$loc['s_tel']." 总计：". $sum." ".$taste;

			// 连续向三个号码发如果都不在返回null
			return $this->_send($phone['telephone'], $content);
				// return false;
				/*
				if ($phone['telephone_1'] !== null) {
					if ($this->_send($phone['telephone_1'], $content) != '0') {
						if ($phone['telephone_2'] !== null) {
							if ($this->_send($phone['telephone_2'], $content) != '0')
								return false;
						}
						else
							return false;
					}
				}
				else {
					if ($phone['telephone_2'] !== null) {
						if ($this->_send($phone['telephone_2'], $content) != '0')
							return false;
					}
					else
						return false;
				}
				*/
			// }
			// return true;
		}

		function get_sale_num_today($store_id) {
			$s_q = $this->db->query("SELECT COUNT(saleId) AS num
									 FROM eachFoodSaleInfo
									 WHERE storeId={$store_id}
									 AND (unix_timestamp(createTime) BETWEEN unix_timestamp(curdate()) AND unix_timestamp(DATE_ADD(curdate(), INTERVAL 1 DAY)))");
			return $s_q->row_array()['num'];
		}

		function send_pos_error($store_id) {
			$store_name = $this->get_db_info->get_store_name($store_id);
			$this->_send(URGENT_PHONE, "有商家{$store_name}，id为{$store_id}的pos机出现问题，请处理");
		}

		function send_raw($phone, $content) {
			$snoopy = new snoopy();
			$sendurl = "http://{$this->smsapi}/sms?u={$this->user}&p={$this->pass}&m={$phone}&c=".urlencode($content);
			$snoopy->fetch($sendurl);
			return $snoopy->results;
		}


		function caixuntong_balance()
		{
			$snoopy = new snoopy();
			$sn = $this->CAIXUNTONGSN;
			$pass = $sn . $this->CAIXUNTONGPWD;
			$pass = strtoupper(md5($pass));
			$url = $this->CAIXUNTONGURL . "balance?sn={$sn}&pwd={$pass}";
			$snoopy->fetch($url);
			return $snoopy->results;
		}

		function caixuntong_chargeup($cardno, $carpwd)
		{
			$snoopy = new snoopy();
			$sn = $this->CAIXUNTONGSN;
			$pass = $sn . $this->CAIXUNTONGPWD;
			$pass = strtoupper(md5($pass));
			$url = $this->CAIXUNTONGURL . "ChargeUp?sn={$sn}&pwd={$pass}&cardno={$cardno}&carpwd={$carpwd}";
			$snoopy->fetch($url);
			return $snoopy->results;
		}
	}

