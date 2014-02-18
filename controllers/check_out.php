<?php
	/**
	 * 用于结帐的页面
	 * @version 1.0
	 */
	require_once("config/alipay.config.php");
	require_once("lib/alipay_submit.class.php");
	class Check_out extends CI_Controller {
		var $alipay_config;
		public function __construct() {
			parent::__construct();
			$this->load->helper('cookie');
			$this->load->model('user_info');
			$this->load->model('food_info');
			$this->load->model('store_info');
			$this->load->model('sale_info');
			$this->load->model('quick_find');
			$this->load->library('header');
			$this->load->library('get_db_info');
			$this->load->model('m_sms');
			$this->load->helper('captcha');
			$this->load->library('session');
			$this->load->model('about_us_info');

	// --------------------------------------------------------------------
	/**
	 * alipay configuration not finished
	 */
			$this->alipay_config['partner']			= '2088902873253414';
			$this->alipay_config['key']				= 'yln5q8gs7xt1h9fwadf8v09nxj5g6079';
			$this->alipay_config['sign_type']    		= strtoupper('MD5');
			$this->alipay_config['input_charset']		= strtolower('utf-8');
			$this->alipay_config['cacert']    		= getcwd().'\\cacert.pem';
			$this->alipay_config['transport']    		= 'http';
			$this->alipay_config['payment_type'] 		= '1';
			$this->alipay_config['notify_url']   		=  "http://www.yicanyican.com/ali_notify";
			$this->alipay_config['return_url']   		= "http://localhost/management";
			$this->alipay_config['quantity']     		= '1';
			$this->alipay_config['logistics_fee'] 	= '0.00';
			$this->alipay_config['logistics_type'] 	= 'EXPRESS';
			$this->alipay_config['logistics_payment'] = 'SELLER_PAY';
			$this->alipay_config['seller_email'] 		= 'zhulinchao7@gmail.com';
	// --------------------------------------------------------------------
		}

		/**
		 * 加载头部信息
		 * @param  string $title in <head></head>
		 * @return void
		 */
		private function _load_top($title) {
			$this->load->view('template/head',
							  $this->header->set_header($title));
			$userinfo = $this->user_info->get_login();
			// 没有使用了, 用来提示用户订餐的流程
			$userinfo['in_step'] = true;
			// 比如现在是第四步了
			$userinfo['step_four'] = 'active';
			$this->load->view('topbar', $userinfo);
		}
		/**
		 * 显示用户订单信息
		 * @param  json-string $s_my_cart 用户订餐信息
		 * @return void
		 */
		private function _show_order($s_my_cart) {
			$my_cart = json_decode($s_my_cart, true);
			foreach($my_cart as &$row) {
				$store_id = $row['store_id'];
				$row['imgLoc'] = $this->get_db_info->get_store_img_loc($store_id);
				$row['delivery_type'] = $this->get_db_info->get_store_delivery_type($store_id);
			}
			$data['my_cart'] = $my_cart; // 以php数组存在
			$data['s_my_cart'] = $s_my_cart; //以json数据存在
			$data['recent_loc'] = $this->user_info->get_recent(); //一个数组
		// ----------------------------------------------------------------------
		// create captcha
			srand((double)microtime()*1000000);
			$random_word = rand(1000,9999);
			$this->session->set_userdata('captcha', $random_word);
			$vals = array(
					'word' => $random_word,
					'img_path' => './captcha/',
					'img_url' => './captcha/',
					'font_path' => './path/to/fonts/texb.ttf',
					'img_width' => '150',
					'img_height' => 30,
					'expiration' => 7200
				);
			$cap = create_captcha($vals);
			$data['cap'] = $cap ['image'];
		// ----------------------------------------------------------------------

			$user_login = $this->user_info->get_login();
			if ($user_login['login'])
				$data['all_address'] = $this->user_info->get_address($user_login['userName']);
			$this->load->view('check_out/check_out', $data);
			$footer['footer'] = $this->about_us_info->get_footer();
			$this->load->view('template/footer_not_index', $footer);
		}
		public function index() {

			$title = "最后一步结账，欢迎来到一餐易餐订餐，想叫外卖，上一餐易餐";
			$this->_load_top($title);
			$my_cart = "";
			if ( ($my_cart = $this->input->post('my_cart', TRUE) ) ||
				 ($my_cart = $this->input->cookie('my_cart', TRUE))) {
				// var_dump($my_cart);
				$this->_show_order($my_cart);
			}
			else {
				$data['empty'] = true;
				$this->load->view('check_out/check_out', $data);
				$footer['footer'] = $this->about_us_info->get_footer();
				$this->load->view('template/footer_not_index', $footer);
			}
		}

		/**
		 * 修改用户的地址, state=>1 for success
		 * @return void
		 */
		public function modify_addr() {
			$user_login = $this->user_info->get_login();
			if ($user_login['login']) {
				$userid = $user_login['userName'];
				$post = $this->input->post(NULL, TRUE);
				$this->db->where('id', $post['modify_pos']);
				$this->db->where('userId', $userid);
				$this->db->set("userPos", $post['addr']);
				$this->db->set("userPhone_main", $post['main_phone']);
				$this->db->set("userPhone_short", $post['short_phone']);
				$this->db->update('userPosition');
				echo json_encode(array('state'=>1));
			}
		}

		/**
		 * 删除用户的地址, state=>1 for success
		 * @return void
		 */
		public function delete_addr() {
			$user_login = $this->user_info->get_login();
			if ($user_login['login']) {
				$userid = $user_login['userName'];
				$pos = $this->input->post('delete_pos', TRUE);
				$this->db->where('id', $pos);
				$this->db->where('userId', $userid);
				$this->db->delete('userPosition');
				echo json_encode(array('state'=>1));
			}
		}

		/**
		 * create a new address, stata=>1 for succress
		 * @return string 	the insert_id
		 */
		public function new_addr() {
			$user_login = $this->user_info->get_login();
			if ($user_login['login']) {
				$userid = $user_login['userName'];
				$data = array('userPos'=>$this->input->post('addr', TRUE),
							  'userPhone_main'=>$this->input->post('main_phone', TRUE),
							  'userPhone_short'=>$this->input->post('short_phone', TRUE),
							  'userId'=>$userid);
				$this->db->insert('userPosition', $data);
				echo json_encode(array('state'=>1, 'pos'=>$this->db->insert_id()));
			}
		}

		/**
		 * 提交订单
		 * @return void stata {
		 *      4: 验证码错误
		 *      3: 第一次使用，需要验证
		 *      2: 手机打不通
		 * }
		 */
		public function up_order() {
			// header("content-type: application/json; charset=utf-8");
			$captcha = $this->input->post('captcha', true);
			$se_cap = $this->session->userdata['captcha'];
			// 一个漏洞，直接把验证码当session写出去了
			if (!$captcha || !$se_cap || $captcha != $se_cap) {
				$re['state'] = 4;
				echo json_encode($re);
				return;
			}
			$this->session->set_userdata('trusted_user',true);
			$post = $this->input->post(NULL, TRUE);
			$loc = $post['loc'];
			if (count($loc) < 2) { //至少两个
				return;
			}
			// 如果是空号
			$user_login = $this->user_info->get_login();
			if ($user_login['login']) {
				$has_default_address = $this->input->post('has_default_address', TRUE);
				// $fp = fopen("log.s", "w");
				// fprintf("%s", $has_default_address);
				if ($has_default_address == 0) {
					$dat = array(
								"userId"=>$user_login['userName'],
								"userPos"=>$loc['addr'],
								"userPhone_main"=>$loc['l_tel'],
								"userPhone_short"=>$loc['s_tel']
						   );
					$this->db->insert('userPosition', $dat);
				}
				else {
					$pos = $this->input->post('pos', TRUE);
					$this->db->where('id', $pos);
					$this->db->where('userId', $user_login['userName']);
					$this->db->set('is_default', '1');
					$this->db->update('userPosition');
				}
			}

			$check_phone = $this->m_sms->check_phone_avai($loc['l_tel']);
			// 返回值2：表示已经使用过该号码
			// 1=>空号
			// 0=>第一次使用
			if ($check_phone == '1') {
				$re['state'] = 2;
				$re['error'] = '对不起你的手机暂时无法接通，请你保证手机通畅后继续订单，一餐易餐欢迎你哦！';
				echo json_encode($re);
				return;
			}
			else if ($check_phone == '0') {
				$re['state'] = 3;
				echo json_encode($re);
				return;
			}
			//
			$this->send_shanghu_message($re);
			echo json_encode($re);
		}

		/**
		 * 发送信息给商户
		 */
		function send_shanghu_message(&$re) {
			$post = $this->input->post(NULL, TRUE);
			if (!$post)
				return;
			$cart = $post['cart'];
			if (count($cart) == 0) {
				return;
			}
			$loc = $post['loc'];
			if (count($loc) < 2) { //至少两个
				return;
			}

			$login = $this->user_info->get_login();
			if ($login['login']) {
				$buyerId = $login['userName'];
				$login = true;
			}
			else {
				$buyerId = $loc['l_tel'];
				$login = false;
			}
			// 暂时不加手机短信提示,
			// 已经加上手机提示
			$re['state'] = 1;
			$re['time'] = date("Y-m-d H:i:s");
			$counter = 0;
			// 设置cookie
			$history_order = $this->input->cookie('history_order',TRUE);
			$history_order = json_decode($history_order, TRUE);

			$first_sale_id = 0;
			$all_sum_money = 0;
			$loc['addr'] = urldecode($loc['addr']);
			$alipay = $this->input->post('ali', TRUE);
			foreach($cart as $store) {
				$counter++;
				$sum  = 0;
				$a = array();
				foreach($store['blanket'] as $blanket) {
					$b = array();
					if (!isset($blanket['food']))
						continue;
					foreach($blanket['food'] as $food) {
							$sum += $food['price'] * $food['number'];
							if ($food['number'] > 0) {
								$f_n = $this->food_info->get_food_name_price($food['food_id']);
								if (!$f_n)
									return;
								$b[] = array("food_id"=>$food['food_id'],
										     "food_num"=>$food['number'],
										     "food_name"=>urldecode($f_n['foodName']),
										     "food_price"=>$f_n['price'],
										);
							}
							else
								return; // 不正常订单
					}
					$a[] = $b;
				}
				$store['taste'] = urldecode($store['taste']);
				if ($sum >= $store['delivery_cost']) {
					$sale_info = array(
						"buyerId"=>$buyerId,
						"storeId"=>$store['store_id'],
						"university_id"=>$store['university_id'],
						"createTime"=>date("Y-m-d H:i:s"),
						"createDate"=>date("Y-m-d"),
						"user_addr"=>$loc['addr'],
						"user_l_tel"=>$loc['l_tel'],
						"user_s_tel"=>$loc['s_tel'],
						"taste"=>$store['taste'],
						"validity"=>'0',
						"from_tel"=>'1'
					);
					$this->db->insert('eachFoodSaleInfo', $sale_info);
					$sale_id = $this->db->insert_id();
					if ($first_sale_id == 0)
						$first_sale_id = $sale_id;
					foreach ($a as $key => $val) {
						foreach ($val as $vval) {
							$sale = array(
								"saleId"=>$sale_id,
								"foodId"=>$vval['food_id'],
								"price"=>$vval['food_price'],
								"num"=>$vval['food_num'],
								"kuang"=>$key
							);
							$this->db->insert('multiFoodSaleInfo', $sale);
							$all_sum_money += $vval['food_price'] * $vval['food_num'];
						}
					}

					// start gen alipay info
					if ($alipay)
						continue;

					// store_deliver:
					// storeName, telephone, telephone_1, telephone_2
					// 计算这是今天的第几单
					$store_deliver = $this->food_info->get_store_deliver($store['store_id']);
					$store_id = $store['store_id'];

					$num_ = $this->m_sms->get_sale_num_today($store_id);
					$d = date("m.d");
					$dd = explode('.', $d);
					$len = 4 - strlen($num_);
					$n = $store_id.$dd[0].$dd[1];
					while ($len > 0) {
						$n .= '0';
						$len--;
					}
					$n .= $num_;
					// 发给商家短信，或者pos

					$q = $this->db->select('pos_valid')
								 ->where('storeId', $store_id)
								 ->get('storeIntro');
					$pos_valid = $q->row_array()['pos_valid'];
					// if (!$pos_valid) { // 发送短信
						if (!$this->m_sms->send_order($sale_id,
													 $n,
													 $store_deliver,
													 $store['taste'],
													 $a,
													 $loc)) {
							// 修改商户状态为太忙碌
							$this->store_info->set_state($store_id, '3');
							if ($re['state'] == 1) {
								$re['error'] = "{$store['store_name']}"; // 商家信息错误
								$re['state'] = 0;
							}
							else
								$re['error'] .= "、{$store['store_name']}";
						}
						else {
							$this->store_info->update_store_sale_order($sale_id, $store_id, $a);
							// 表示发送了
							$this->sale_info->update_pos_send($sale_id);
							// 商户根据confirm time判断是否为新订单，如果还没有confirm则confirmtime为null
						}
						// 如果没有登录，则更新history_order
					// }
					if (!$login) {
						$history_order[] = array("sale_id"=>$sale_id);
					}
				}
				else
					return;// abnormal，直接退出
			}

			if ($alipay) {
				$out_trade_no = $first_sale_id;
		        $subject = "订单编号:{$first_sale_id}";
		        $price = $all_sum_money;
		        $receive_name = $buyerId;
		        $receive_address = $loc['addr'];
		        $receive_zip = 310000;
		        $receive_phone = $loc['s_tel'];
		        $receive_mobile = $loc['l_tel'];
		        $parameter = array(
								"service" => "create_partner_trade_by_buyer",
								"partner" => trim($this->alipay_config['partner']),
								"payment_type"	=> $this->alipay_config['payment_type'],
								"notify_url"	=> $this->alipay_config['notify_url'],
								"return_url"	=> $this->alipay_config['return_url'],
								"seller_email"	=> $this->alipay_config['seller_email'],
								"out_trade_no"	=> $out_trade_no,
								"subject"	=> $subject,
								"price"	        => $price,
								"quantity"	=> $this->alipay_config['quantity'],
								"logistics_fee"	=> $this->alipay_config['logistics_fee'],
								"logistics_type"	=> $this->alipay_config['logistics_type'],
								"logistics_payment"	=> $this->alipay_config['logistics_payment'],
								"receive_name"	=> $receive_name,
								"receive_address"	=> $receive_address,
								"receive_zip"	=> $receive_zip,
								"receive_phone"	=> $receive_phone,
								"receive_mobile"	=> $receive_mobile,
								"_input_charset"	=> trim(strtolower($this->alipay_config['input_charset']))
							);
				$alipaySubmit = new AlipaySubmit($this->alipay_config);
				$re['alipay_url'] = $alipaySubmit->buildRequestForm($parameter);
				$re['state'] = 1;
			}

			$cookie = array(
					    'name'   => 'history_order',
					    'value'  => json_encode($history_order),
					    'expire' => COOKIE_TIME,
					    'path'   => '/',
					    );
			$this->input->set_cookie($cookie);
		}
		/**
		 * 没有用到
		 */
		public function error($err) {
			switch ($err) {
				case '0':
					echo "您的手机暂时不能接通";
					break;
				case '1':
					echo "商家的手机暂时不能接通";
			}
		}

		/**
		 * 手机验证
		 */
		function confirm_phone() {
			if (!$this->session->userdata('trusted_user'))
				return;
			$phone = $this->input->post('phone');
			$confirm_code = $this->input->post('confirm_code');
			$re = array();
			$re['phone'] = 0;
			if ($phone && $confirm_code) {
				$q = $this->db->select('telephone')
							  ->where('telephone', $phone)
							  ->where('code', $confirm_code)
							  ->where('unix_timestamp(now()) - unix_timestamp(createTime) <', 300)
							  ->get('first_telephone');
				if ($q->num_rows() > 0) {
					$this->db->set('confirmed', '1');
					$this->db->where('telephone', $phone);
					$this->db->update('first_telephone');
					$re['phone'] = 1;
					$this->send_shanghu_message($re);
				}
			}
			echo json_encode($re);
		}
	}