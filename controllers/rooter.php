<?php
	/**
	 * 后台管理界面
	 */
	class Rooter extends CI_Controller {
		var $head = '/img/rooter/'; // 图片地址头部
		public function __construct() {
			parent::__construct();
			$this->load->library('session');
			$this->load->helper('captcha');
			$this->load->library('get_db_info');
			$this->load->library('header');
			$this->load->library('picture');
			$this->load->model('rooter_info');
			$this->load->model('store_info');
			$this->load->model('about_us_info');
			$this->load->model('m_sms');
			$this->load->library('word');
			$this->SUPER_ROOT = '5';
			$this->REGION_ROOT = '4';
			$this->REGION_NEW_STORE = '3';
			$this->REGION_DAILY = '2';
		}
		public function _load_head() {
			$this->load->view('template/head', $this->header->set_header('后台管理界面','',''));
		}
		private function _check_login_valid() {
			$prio = $this->session->userdata('prio');
			$user_id = $this->session->userdata('a_id');
			$password = $this->session->userdata('a_pa'); // already md5ed
			if (!$prio || !$user_id || !$password)
				return 0;
			$q = $this->db->select('mana_prio as prio, mana_region as region')
								  ->where('mana_id', $user_id)
								  ->where('mana_pass', $password)
								  ->get('management');
			if ($q->num_rows() > 0) {
				$row = $q->row_array();
				if (('root'.$row['prio']) == $prio) {
					$re = array();
					$re['prio'] = $row['prio'];
					$re['region'] = $row['region'];
					return $re;
				}
				else
					return 0;
			}
			else
				return 0;
			// if ($this->session->userdata("root_5"))
			// 	return 5;
			// if ($this->session->userdata('root_4'))
			// 	return 4;
			// if ($this->session->userdata('root_3'))
			// 	return 3;
			// if ($this->session->userdata('root_2'))
			// 	return 2;
			// return 0;
		}

		private function _create_captcha()
		{
			srand((double)microtime()*1000000);
			$random_word = rand(1000,9999);
			$this->session->set_userdata('a_captcha', md5($random_word));
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
			return $cap ['image'];
		}

		public function index()
		{
// 			$phone='123';
// $content='啦啦啦';
// $sn = "DXX-WSS-10C-05978";
// $pass = $sn . "753250";
// $pass = strtoupper(md5($pass));
// $content = $content . "【易餐·千红】";
// echo $content;
// echo "<br />";
// $content = mb_convert_encoding($content, "GB2312", "UTF-8");
// // $content = iconv( "UTF-8", "GB2312//IGNORE" , $content);
// echo $content;
// echo "--<br />";

// //
// $content = urlencode($content);
// echo "\n";
// $url = "http://sdk.entinfo.cn:8060/webservice.asmx/gxmt&sn={$sn}&pwd={$pass}&mobile={$phone}&content={$content}&ext=&stime=&rrid=";
// echo $url;

			$code = $this->input->post('code', TRUE);
			$a_id = $this->input->post('ad_id', TRUE);
			$a_pa = $this->input->post('ad_passwd', TRUE);
			$data = array();
			if ($code && $a_id && $a_pa) {
				if (md5($code) == $this->session->userdata('a_captcha')) {
					$q = $this->db->select('mana_prio as prio, mana_region as region')
								  ->where('mana_id', trim($a_id))
								  ->where('mana_pass', md5(trim($a_pa)))
								  ->get('management');
					if ($q->num_rows() > 0) {
						$row = $q->row_array();
						$this->session->set_userdata(array('prio'=>'root' . $row['prio'],
						                              	   'a_id'=>$a_id,
						                              	   'a_pa'=>md5($a_pa)));
						$this->manager();
						return;
					} else {
						$data['fail'] = 1;
						$data['msg'] = '找不到这个人';
					}
				}
				else {
					$data['fail'] = 1;
					$data['msg'] = '验证码错误';
				}
			}
			else if ($this->_check_login_valid()) {
				$this->manager();
				return;
			}
			$this->_load_head();
			$data['cap'] = $this->_create_captcha();
			$this->load->view('rooter/login', $data);
		}

		public function manager() {
			$re = $this->_check_login_valid();
			if ($re) {
				switch ($re['prio']) {
					case $this->SUPER_ROOT:
					case $this->REGION_ROOT:
						$this->sale_today();
						break;
					case $this->REGION_NEW_STORE:
						$this->new_store();
						break;
					case $this->REGION_DAILY:
						/**
						 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
						 */
						$this->sale_man();
					default:
						return;
				}
			}
			else
				header('location: /rooter');
		}
		public function check_store_login_id() {
			if ($this->_check_login_valid()) {
				header("content-type: application/json; charset=utf-8");
				$post = $this->input->post('store_login_id');
				$re =false;
				if ($post && $this->rooter_info->check_store_login_id($post))
					$re = true;
				echo json_encode($re);
			}

		}
		public function check_store_name() {
			if ($this->_check_login_valid()) {
				header("content-type: application/json; charset=utf-8");
				$post = $this->input->post('store_name');
				$re =false;
				if ($post && $this->rooter_info->check_store_name($post))
					$re = true;
				echo json_encode($re);
			}
		}
		private function new_store_echo_error($region, $re, $post, $error="数据库更新成功") {
			$re['post'] = $post;
			if ($re['state']) {
				$re['post'] = array();
				$re['error'] = $error;
			}
			$re['university'] = $this->rooter_info->get_all_university_in_region($region);
			$re['store_type_info'] = $this->rooter_info->get_all_store_type();
			$re['block_info'] = $this->store_info->get_block();
			$this->load->view('rooter/new_store', $re);
		}
		private function _load_left($re="") {
			if ($re) {
				switch ($re['prio']) {
					case $this->SUPER_ROOT:
						$data['super_root'] = 1;
						break;
					case $this->REGION_ROOT:
						$data['region_root'] = 1;
						break;
					case $this->REGION_NEW_STORE:
						$data['region_new_store'] = 1;
						break;
					case $this->REGION_DAILY:
						$data['region_daily'] = 1;
						break;
				}
				$this->load->view('rooter/left', $data);
			}
		}
		public function new_store() {
			$region_info = $this->_check_login_valid();
			if ($region_info
			    && ($region_info['prio'] == $this->REGION_NEW_STORE || $region_info['prio'] == $this->REGION_ROOT))
				;
			else
				return;

			$this->_load_head();
			$this->_load_left($region_info);
			$post = $this->input->post(NULL, TRUE);
			$re['state'] = 0;
			if (!$post) {
				$re['state'] = 1;
				return $this->new_store_echo_error($region_info['region'], $re, "", "");
			}

			$pics_loc;

			if (count($_FILES) == 0) {
				$re['error'] = "空图片文件";
				return $this->new_store_echo_error($region_info['region'], $re, $post);
			}
			foreach ($_FILES as $key=>$row) {
				$picname = $row['name'];
				$type = strstr($picname, '.');
				if ($type != '.gif' &&
					$type != '.jpg' &&
					$type != '.png') {

					$re['error']="图片格式不对";
					return $this->new_store_echo_error($region_info['region'], $re, $post);
				}
				else {
					$rand = rand(100, 999);
        			$pics_loc = date("YmdHis") . $rand . $type;
					move_uploaded_file($row['tmp_name'], LOC_PREFIC."/img/store/".$pics_loc);
					$this->picture->compress(LOC_PREFIC."/img/store/".$pics_loc, $type, 170, 120);
				}
			}

			if ($post['store_passwd'] == $post['store_passwd_confirm']) {
				$delivery_order = $post['delivery_order'];
				$delivery_type = 0;
				foreach ($delivery_order as $row)
					$delivery_type |= $row;
				// var_dump($delivery_type);
				$n = $this->rooter_info->new_store( $post['store_name'],
												   $post['store_loc'],
												   $pics_loc,
												   $post['store_login_id'],
												   $post['store_passwd'],
												   $post['store_tel'],
												   $post['store_tel_2'],
												   $post['store_tel_3'],
												   $post['delivery_cost'],
												   $post['university_id'],
												   $post['start_hour'],
												   $post['start_minite'],
												   $post['end_hour'],
												   $post['end_minite'],
												   $delivery_type,
												   $post['max_order'],
												   $post['store_type'] );
				if (!$n['state'])
					$re['error'] = $n['error'];
				else
					$re['state'] = 1;
			}
			else
				$re['error'] = "密码不相等";

			$this->new_store_echo_error($region_info['region'], $re, $post);
		}

		public function ajax_new_store_type($block, $id, $name) {
			if (!$this->_check_login_valid())
				return;
			$name = urldecode($name);
			$id = urldecode($id);
			if ($block == 0) {
				$this->db->where('storeTypeId', $id);
				$this->db->set('storeTypeName', $name);
				$this->db->update('storeType');
				if ($this->db->affected_rows() > 0)
					echo json_encode(array('state'=>1));
				else
					echo json_encode(array('state'=>0));
			}
			else if ($block == 1) {
				$this->db->set('block_name', $name);
				$this->db->where('block_num', $id);
				$this->db->update('delivery_order');
				if ($this->db->affected_rows() > 0)
					echo json_encode(array('state'=>1));
				else
					echo json_encode(array('state'=>0));
			}
		}

		public function new_store_type() {
			$region_info = $this->_check_login_valid();
			if ($region_info
			    && $region_info['prio'] == $this->SUPER_ROOT)
				;
			else
				return;

			$this->_load_head();
			$this->_load_left($region_info);
			$store_type = $this->input->post('store_type', TRUE);
			$block_name = $this->input->post('new_block', TRUE);
			if ($store_type) {
				$this->rooter_info->new_store_type($store_type);
				$data['info'] = "插入种类成功";
			}
			if ($block_name) {
				$q = $this->db->select('block_num')
					  	      ->get('delivery_order');
				$start = 1;
				foreach ($q->result_array() as $row) {
					if ($start == $row['block_num'])
						$start <<= 1;
					else
						break;
				}
				$this->db->insert('delivery_order', array(
														  'block_name'=>$block_name,
														  'block_num'=>$start));
				$data['info'] = '添加区域成功';
			}

			$data['store_type'] = $this->rooter_info->get_all_store_type();
			$q = $this->db->select('block_name, block_num')
			              ->get('delivery_order');
			$data['block_info'] = $q->result_array();

			$this->load->view('rooter/new_store_type', $data);
		}

		public function new_univ() {
			$region_info = $this->_check_login_valid();
			if ($region_info && $region_info['prio'] == $this->REGION_ROOT)
				;
			else
				return;
			$this->_load_head();
			$this->_load_left($region_info);
			$file_type = 'univ';
			$univ_type = $this->input->post(NULL, TRUE);
			if ($univ_type) {
				$re = $this->picture->upload_img('univ');
				if ($re['state']) {
					$q = $this->db->select('schoolId')
								  ->where('schoolFullName', $univ_type['univ_full_name'])
								  ->or_where('schoolShortName', $univ_type['univ_short_name'])
								  ->get('schoolInfo');
					if ($q->num_rows() > 0)
						$data['info'] = "名字重复";
					else {
						$this->rooter_info->new_univ_one_region($region_info['region'], $univ_type, $re['error']);
						$data['info'] = "插入数据成功";
					}
				}
			}
			$data['univ_type'] = $this->rooter_info->get_all_univ_type_one_region($region_info['region']);

			$this->load->view('rooter/new_univ', $data);
		}

		private function check_univ_valid($region_id, $univ_id)
		{
			$q = $this->db->select('region_id')
						  ->from('region')
						  ->join('schoolInfo', 'region.region_id = schoolInfo.region_code')
						  ->where('region.region_id', $region_id)
						  ->where('schoolInfo.schoolId', $univ_id)
						  ->get();
			if ($q->num_rows() > 0)
				return 1;
			return 0;
		}
		private function check_store_valid($region_id, $store_id)
		{
			$q = $this->db->select('region_id')
						  ->from('region')
						  ->join('schoolInfo', 'schoolInfo.region_code = region.region_id')
						  ->join('storeLoc', 'storeLoc.belongTo = schoolInfo.schoolId')
						  ->where('region.region_id', $region_id)
						  ->where('storeLoc.storeId', $store_id)
						  ->get();
			if ($q->num_rows() > 0)
				return 1;
			return 0;
		}
		public function change_store_state($univ = 0, $store_id = 0, $delete_this = 0) {
			$region_info = $this->_check_login_valid();
			if ($region_info
			    && $region_info['prio'] == $this->REGION_ROOT)
				;
			else
				return;
			$this->_load_head();
			$this->_load_left($region_info);
			$data = array();
			if ($univ == 0)
				$data['university'] = $this->rooter_info->get_all_university_in_region($region_info['region']);
			else {
				if ($store_id == 0) {
					if (!$this->check_univ_valid($region_info['region'], $univ))
						return;
					$data['store'] = $this->rooter_info->get_all_store_one_univ($univ);
					$data['university_id'] = $univ;
				}
				else {
					if (!$this->check_store_valid($region_info['region'], $store_id))
						return;
					if (!$delete_this) {
						$data['university_id'] = $univ;
						$data['store_id'] = $store_id;
						$data['delete'] = true;
						$data['block_info'] = $this->store_info->get_block();
						$q = $this->db->select('delivery_order')
									  ->where('storeId', $store_id)
									  ->get('storeIntro');
						$data['now_block'] = $q->row_array()['delivery_order'];
 					}
					else if ($delete_this == 1)
						$this->rooter_info->delete_store($store_id, '5'); // close
					else if ($delete_this == 2)
						$this->rooter_info->delete_store($store_id, '0'); // open
				}
			}
			$this->load->view("rooter/change_store_state", $data);
		}

		function change_block() {
			if (!$this->_check_login_valid())
				return;
			$store_id = $this->input->post('store_id', TRUE);
			$block = $this->input->post('delivery_order', TRUE);
			$res = 0;
			if ($block)
				foreach ($block as $one_block)
					$res |= $one_block;
			$this->db->set('delivery_order', $res);
			$this->db->where('storeId', $store_id);
			$this->db->update('storeIntro');
			echo "修改成功";
		}

		function change_store_order($univ = 0) {
			$region_info = $this->_check_login_valid();
			if ($region_info
			    && $region_info['prio'] == $this->REGION_ROOT)
				;
			else
				return;
			$this->_load_head();
			$this->_load_left($region_info);
			$data = array();
			if ($univ == 0)
				$data['university'] = $this->rooter_info->get_all_university_in_region($re['region']);
			else {
				if (!$this->check_univ_valid($region_info['region'], $univ))
					return;
				if ( ($waimai = $this->input->post('store_order_0', TRUE)) &&
					 ($yuding = $this->input->post('store_order_1', TRUE)) ) {
						$this->rooter_info->update_store_order($univ, $waimai, $yuding);
						$data['post'] = true;
				}
				$data['store_delivery_order'] = $this->rooter_info->get_all_store_one_univ_order_type($univ);
				$data['university_id'] = $univ;
			}
			$this->load->view('rooter/change_store_order', $data);
		}

		function change_store_password($store_id = 0)  {
			$region_info = $this->_check_login_valid();
			if ($region_info
			    && $region_info['prio'] == $this->REGION_ROOT)
				;
			else
				return;
			$this->_load_head();
			$this->_load_left($region_info);
			$data = array();
			$_load_input_passwd = false;
			if ($store_id == 0) {
				$store_post_id = $this->input->post('store_id', TRUE);
				if (!$store_post_id) {
					$data['input_a_store'] = true;
				}
				else {
					if (!$this->check_store_valid($region_info['region'], $store_post_id))
						return;
					$_load_input_passwd = true;
					$store_id = $store_post_id;
				}
			}
			else {
				if (!$this->check_store_valid($region_info['region'], $store_id))
					return;
				$data['alert'] = true;
				$passwd_1 = $this->input->post('passwd_1');
				$passwd_2 = $this->input->post('passwd_2');
				if ($passwd_1 && $passwd_2 && ($passwd_1 == $passwd_2) && strlen($passwd_1) >= 6) {
					$this->store_info->change_passwd($store_id, $passwd_1);
					$data['success'] = true;
				}
				$_load_input_passwd = true;
			}
			if ($_load_input_passwd) {
				$data['store_id'] = $store_id;
				$data['store_name'] = $this->get_db_info->get_store_name($store_id);
			}

			$this->load->view("rooter/change_store_password", $data);
		}

		function add_announce($delete_announce_id = 0) {
			$region_info = $this->_check_login_valid();
			if ($region_info && $region_info['prio'] == $this->SUPER_ROOT)
				;
			else
				return;
			$this->_load_head();
			$this->_load_left($region_info);
			$announce_content = file_get_contents("php://input");
			$announce_content = urldecode($announce_content);
			$announce_content = substr($announce_content, strlen("announce_content="));
			if ($announce_content) {
				$in_data = array('announce_content'=>$announce_content,
								 'createTime'=>date('Y-m-d H:i:s'));
				$this->db->insert('announcement', $in_data);
				$data['success'] = true;
			}
			if ($delete_announce_id != 0) {
				$this->db->delete('announcement', array('announce_id'=>$delete_announce_id));
				$data['delete'] = true;
			}
			$data['old_announce'] = $this->about_us_info->get_announce();
			$this->load->view('rooter/add_announce', $data);
		}

		function add_footer($delete = -1) {
			$region_info = $this->_check_login_valid();
			if ($region_info && $region_info['prio'] == $this->SUPER_ROOT)
				;
			else
				return;
			$this->_load_head();
			$this->_load_left($region_info);
			if (-1 == $delete) {

				$check = $this->input->post('footer_content', true);
				$footer_content = file_get_contents("php://input");
				$footer_content = urldecode($footer_content);
				$footer_content = substr($footer_content, strlen("footer_content="));
				if ($check) {
					$now = date('YmdHis');
					$fp = fopen(LOC_PREFIC.'/text/'.$now, 'w');
					if (NULL == $fp) {
						$data['open_file_failed'] = true;
						$this->load->view('rooter/add_footer', $data);
						return;
					}

					fprintf($fp, "%s", $footer_content);
					fclose($fp);
					$data['href'] = WHOAMI."/notice/{$now}";
				}
				else {
					$zh = $this->input->post('footer_lable_name_zh', TRUE);
					$href = $this->input->post('href', TRUE);
					if ($zh && $href) {
						$insert_data = array('label_name'=>$zh,
											 'link_href'=>$href);
						$this->db->insert('footer_note', $insert_data);
						$data['insert_href'] = true;
					}
				}
			}
			else {
				$this->db->where('orders', $delete);
				$this->db->delete('footer_note');
				$data['deleted'] = true;
			}
			$data['content'] = $this->about_us_info->get_footer();
			$this->load->view('rooter/add_footer', $data);
		}
		private function get_month_0() {
			$q = $this->db->select('DISTINCT(createDate) as createDate')
						  ->where('unix_timestamp(createTime) BETWEEN
						  		   unix_timestamp(DATE_SUB(curdate(), INTERVAL day(curdate())-1 DAY)) AND
						  		   unix_timestamp(now())')
						  ->order_by('createDate', 'desc')
						  ->get('eachFoodSaleInfo');
			return $q->result_array();
		}

		private function get_month_1($one) {
			$q = $this->db->select('DISTINCT(createDate) as createDate')
						  ->where("unix_timestamp(createTime) BETWEEN
						  		   unix_timestamp(DATE_SUB(curdate()-day(curdate())+1, INTERVAL {$one} MONTH))
						  		   AND
						  		   unix_timestamp(last_day(DATE_SUB(curdate()-day(curdate())+1, INTERVAL {$one} MONTH)))")
						  ->where('validity', '1')
						  ->order_by('createDate', 'desc')
						  ->get('eachFoodSaleInfo');
			return $q->result_array();
		}
		// $month == 0 =>这个月
		// == 1 => 前一个月
		// == 2 => 前前
		private function _query_sale_details($store_id, $month, &$all_store_id, &$all_money, &$sale_counter) {
			if ($store_id == YCYC_ID)
				return;
			// 一天一天来
			$count = 0;
			$month_money = 0;

			foreach ($month as &$row) {
				$q = $this->db->select('saleId')
									  ->where('createDate', $row['createDate'])
									  ->where('storeId', $store_id)
									  ->where('validity', '1')
									  ->order_by('createTime', 'desc')
									  ->get('eachFoodSaleInfo');
				$temp = array();
				$temp['createDate'] = $row['createDate'];
				$temp['sale_id'] = $q->result_array();
				$all_store_id['sale'][] = $temp;
				$money = 0;

				foreach($all_store_id['sale'][$count]['sale_id'] as &$row_2) {
					$sale_counter++;
					$q = $this->db->select('foodInfo.foodId as foodId,
											foodInfo.foodName as foodName,
								   			multiFoodSaleInfo.price as price,
								   			multiFoodSaleInfo.num as num')
								  ->from('multiFoodSaleInfo')
								  ->join('foodInfo', 'multiFoodSaleInfo.foodId = foodInfo.foodId')
								  ->where('saleId', $row_2['saleId'])
								  ->get();
					$row_2['food_info'] = $q->result_array();
					foreach ($row_2['food_info'] as $f) {
						$m = $f['price'] * $f['num'];
						$money += $m;
						$all_money += $m;
						$month_money += $m;
					}
				}
				$all_store_id['sale'][$count]['money'] = $money;
				$all_store_id['money'] = $month_money; // 一个月总价格
				$count++;
			}
		}

		private function _get_date($month) {
			$re = array();
			switch ($month) {
				case '0':
					$re = $this->get_month_0();
					$this_month = date('Y-m');
					break;
				case '1':
				case '2':
					$re = $this->get_month_1($month);
					$this_month = date('Y-m', mktime(0, 0, 0, date('m')-$month, 1, date('Y')));
					break;
				default:
					$re['error'] = true;
			}
			$m_date['month_date'] = $re;
			$m_date['this_month'] = $this_month;
			return $m_date;
		}

		private function _sale_history_one_region($region_id, $month, $month_info)
		{
			$all_store_id_query = $this->db->select('storeIntro.storeId as storeId,
														 storeIntro.storeName as storeName,
														 storeIntro.telephone as telephone')
										   ->from('storeIntro')
										   ->join('storeLoc', 'storeLoc.storeId = storeIntro.storeId')
										   ->join('schoolInfo', 'storeLoc.belongTo = schoolInfo.schoolId')
										   ->join('region', 'region.region_id = schoolInfo.region_code')
										   ->get();

			$all_store = $all_store_id_query->result_array();
			$all_money = 0;
			$sale_counter = 0;
			foreach ($all_store as &$all_store_id) {
				// 查看这个月
				$this->_query_sale_details($all_store_id['storeId'], $month_info['month_date'], $all_store_id, $all_money, $sale_counter);
			}
			$data['all_store'] = $all_store;
			$data['all_money'] = $all_money;
			$data['sale_counter'] = $sale_counter;
			$data['this_month'] = $month_info['this_month'];
			$data['sale_month'] = $month;
			return $data;
		}
		function sale_history() {
			$region_info = $this->_check_login_valid();
			if ($region_info
			    && ($region_info['prio'] == $this->SUPER_ROOT || $region_info['prio'] == $this->REGION_ROOT)) {
			    	;
			} else
				return;
			$this->_load_head();
			$this->_load_left($region_info);
			$month = $this->input->post('sale_month', TRUE);
			$data = array();
			if (!$month)
				$month = '0';

			$month_info = $this->_get_date($month);
			if (@$month_info['month_date']['error'])
				return;
			if ($region_info['prio'] == $this->REGION_ROOT) {
				$data = $this->_sale_history_one_region($region_info['region'], $month, $month_info);
				$this->load->view('rooter/choose_month', $data);
			} else {
				$q = $this->db->select('region_id, region_name')
					      	  ->get('region');
				foreach ($q->result_array() as $row) {
					$one_region = $this->_sale_history_one_region($region_info['region'], $month, $month_info);
					$one_region['region_name'] = $row['region_name'];
					$data[] = $one_region;
				}
				$result['all_history'] = $data;
				$result['this_month'] = $month_info['this_month'];
				$result['sale_month'] = $month;
				$this->load->view('rooter/choose_month_rooter', $result);
			}

		}

		private function get_sale_detail($store_id, $sale_month, &$data) {
			$one_store = array();
			$month = $this->_get_date($sale_month);
			$all_money = 0;
			$sale_counter = 0;
			$this->_query_sale_details($store_id, $month['month_date'], $one_store, $all_money, $sale_counter);
			$data['one_store'] = $one_store;
			$data['store_id'] = $store_id;
			$data['this_month'] = $month['this_month'];
		}

		function logout()
		{
			$this->session->unset_userdata('prio');
			$this->session->unset_userdata('a_id');
			$this->session->unset_userdata('a_pa'); // already md5ed
			header('location: /rooter');
		}

		function print_sale($sale_month, $store_id) {
			if (!$this->_check_login_valid())
				return;
			$this->word->start();
			$this->get_sale_detail($store_id, $sale_month, $data);
			$data['store_name'] = $this->get_db_info->get_store_name($store_id);
			$this->load->view('rooter/print_sale', $data);
			$file_name = "{$store_id}_{$data['this_month']}.doc";
			$this->word->save(LOC_PREFIC."/doc/{$file_name}");//保存word并且结束.
			$file = fopen(LOC_PREFIC."/doc/{$file_name}","r");
            header("Content-type: application/octet-stream");
            header("Accept-Ranges: bytes");
            header("Accept-Length: ".filesize(LOC_PREFIC."/doc/{$file_name}"));
            header("Content-Disposition: attachment; filename=".$file_name);
            echo fread($file, filesize(LOC_PREFIC."/doc/{$file_name}"));
            fclose($file);
		}
		function store_sale_details($sale_month, $store_id) {
			if (!$this->_check_login_valid())
				return;
			$this->_load_head();
			$this->_load_left();
			$this->get_sale_detail($store_id, $sale_month, $data);

			$this->load->view('rooter/sale_details', $data);
		}

		private function _sale_today_one_region($region)
		{
			$today = date('Y-m-d');
			$q = $this->db->select('COUNT(saleId) as today_sale_num')
							  ->where('createDate', $today)
							  ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
							  ->where('validity', '1')
							  ->from('eachFoodSaleInfo')
							  ->join('storeLoc', 'storeLoc.storeId = eachFoodSaleInfo.storeId')
							  ->join('schoolInfo', 'schoolInfo.schoolId = storeLoc.belongTo')
							  ->join('region', 'region.region_id = schoolInfo.region_code')
							  ->where('region.region_id', $region)
							  ->get();
			$data['today_sale_num'] = $q->row_array()['today_sale_num'];

			$q = $this->db->select('price, num')
					      ->from('eachFoodSaleInfo')
					      ->join('multiFoodSaleInfo', 'eachFoodSaleInfo.saleId = multiFoodSaleInfo.saleId')
						  ->where('createDate', $today)
						  ->where('validity', '1')
						  ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
						  ->join('storeLoc', 'storeLoc.storeId = eachFoodSaleInfo.storeId')
						  ->join('schoolInfo', 'schoolInfo.schoolId = storeLoc.belongTo')
						  ->join('region', 'region.region_id = schoolInfo.region_code')
						  ->where('region.region_id', $region)
						  ->get();
			$money = 0;
			foreach($q->result_array() as $row) {
				$money += $row['price'] * $row['num'];
			}
			$data['today_sale_money'] = $money;

			$q = $this->db->select('COUNT(saleId) as urgent_num')
					      ->where('createDate', $today)
					      ->where('urgent_time >', $today)
					      ->where('storeId !=', YCYC_ID)
					      ->get('eachFoodSaleInfo');
			$data['urgent_num'] = $q->row_array()['urgent_num'];
			return $data;
		}
		function sale_today() {
			$region_info = $this->_check_login_valid();
			if ($region_info
			    && ($region_info['prio'] == $this->SUPER_ROOT || $region_info['prio'] == $this->REGION_ROOT)) {
			    	;
			} else
				return;
			$this->_load_head();
			$this->_load_left($region_info);
			if ($region_info['prio'] == $this->REGION_ROOT) {
				$data = $this->_sale_today_one_region($region_info['region']);
				$q = $this->db->select('region_name')
							  ->where('region_id', $region_info['region'])
							  ->get('region');
				$data['region_name'] = $q->row_array()['region_name'];
				$this->load->view('rooter/today_sale', $data);
			} else {
				$q = $this->db->select('region_name, region_id')
							  ->get('region');
				$data = array();
				foreach ($q->result_array() as $row) {
					$res = $this->_sale_today_one_region($row['region_id']);
					$data[] = array('region_name'=>$row['region_name'],
					                 'region_info'=>$res);
				}
				$res['region'] = $data;
				$this->load->view('rooter/all_today_sale', $res);
			}
		}

		function pos() {
			$region_info = $this->_check_login_valid();
			if ($region_info && $region_info['prio'] == $this->SUPER_ROOT)
				;
			else
				return;
			$this->_load_head();
			$this->_load_left($region_info);

			$data = array();
			$store_id  = $this->input->post('store_id');
			$imei 	   = $this->input->post('imei');
			$telephone = $this->input->post('telephone');
			$cancel    = $this->input->post('cancel');
			if ($store_id && $imei) {
				$this->db->where('IMEI', $imei);
				$this->db->set('pos_valid', '0');
				$this->db->update('storeIntro');
				$this->db->where('storeId', $store_id);

				$this->db->set('IMEI', $imei);
				$this->db->set('pos_valid', '1');
				$this->db->update('storeIntro');
				$data['imei'] = true;
				$data['success'] = false;
				if($this->db->affected_rows() > 0)
					$data['success'] = true;
			}

			else if ($telephone) {
				$head = '@@@12345';
				// 设置头部;
				if ($this->m_sms->send_raw($telephone, "{$head} h 一餐易餐电子小票\n----") == '0'
					&&
					$this->m_sms->send_raw($telephone, "{$head} s 1") == '0' // 开通GPRS
					&&
					$this->m_sms->send_raw($telephone, "{$head} z yyy.yicanyican.com") == '0' // 设置主机名称
					&&
					$this->m_sms->send_raw($telephone, "{$head} e 112.124.6.93") == '0' // 设置ip
					&&
					$this->m_sms->send_raw($telephone, "{$head} % 20") == '0' // 设置长连接时间
					&&
					$this->m_sms->send_raw($telephone, "{$head} @ get_sms?") == '0' // 设置get
					&&
					$this->m_sms->send_raw($telephone, "{$head} [ 0") == '0' // 设置get
					&&
					$this->m_sms->send_raw($telephone, "{$head} r") == '0') // 查询IMEI
				{
						$data['post_tel'] = true;
						$data['telephone'] = $telephone;
				}
				else
					$data['error'] = true;
			}
			else if ($store_id && $cancel) {
				$this->db->set('pos_valid', '0');
				$this->db->where('storeId', $store_id);
				$this->db->update('storeIntro');
				$data['cancel'] = true;
			}
			$this->load->view('rooter/pos', $data);
		}


		function set_pic_title($pic, $x, $y, &$title) {
			$title = array(
						'path'=>LOC_PREFIC.$pic[count($pic) - 1]['path'],
						'title'=>$pic[count($pic) - 1]['title'],
						'x_note'=>$x,
						'y_note'=>$y
					 );
		}

		function set_pic_path(&$pic, $file_name, $title_name) {

			$pic[] = array(
						'path'=>"{$this->head}{$file_name}.jpg",
						'title'=>$title_name,
						'item_name'=>$file_name
						);
		}

		function show_deep_search_super_root()
		{
			$from_day = date('Y-m-d', strtotime("-5month"));
			$q = $this->db->select('createDate,
							   count(saleId) as c_sale_id')
						 ->from('eachFoodSaleInfo')
						 ->where('createDate >=', $from_day)
						 ->where('eachFoodSaleInfo.validity', '1')
						 ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
						 ->group_by('createDate')
						 ->get();

			$sale_count = $q->result_array();
			$q = $this->db->select('user_l_tel, user_addr, createDate, count(saleId) AS c_sale_id')
						 ->where('createDate >=', $from_day)
						 ->where('eachFoodSaleInfo.validity', '1')
						 ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
						 ->group_by('user_l_tel')
						 ->group_by('createDate')
						 ->get('eachFoodSaleInfo');

			$tel_sale_detail = $q->result_array();
			$q = $this->db->select('eachFoodSaleInfo.createDate as createDate,
				 					multiFoodSaleInfo.price as price,
				 					multiFoodSaleInfo.num as num')
						  ->from('eachFoodSaleInfo')
						  ->join('multiFoodSaleInfo', 'multiFoodSaleInfo.saleId = eachFoodSaleInfo.saleId')
						  ->where('createDate >=', $from_day)
						  ->where('eachFoodSaleInfo.validity', '1')
						  ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
						  ->order_by('createDate', 'ASC')
						  ->get();
			$old_createDate = $q->row_array()['createDate'];
			$sum = 0;
			$money = array();
			foreach ($q->result_array() as $row) {
				if ($old_createDate != $row['createDate']) {
					$money[]['money'] = $sum;
					$sum = 0;
					$old_createDate = $row['createDate'];
				}
				$sum += $row['price'] * $row['num'];
			}
			$money[]['money'] = $sum;

			$daily_inc = array();
			$until_yesterday_tel = array();
			$old_user = array();
			$old_user_ratio = array();
			$tol = count($sale_count);
			foreach ($sale_count as $key => $row) {
				$yesterday_tel = $this->db->select('COUNT(telephone) as inc')
											  ->where('createTime <', $row['createDate'])
											  ->get('first_telephone');
				if ($key == $tol - 1) {
					$q = $this->db->select('COUNT(telephone) as inc')
						  ->where('createTime >=', $row['createDate'])
						  ->get('first_telephone');
					$q_old_user = $this->db->select('COUNT(telephone) as c_sale')
										   ->from('eachFoodSaleInfo')
										   ->join('first_telephone', 'first_telephone.telephone = eachFoodSaleInfo.user_l_tel')
										   ->where('eachFoodSaleInfo.createTime >=', $row['createDate'])
										   ->where('first_telephone.createTime <', $row['createDate'])
										   ->where('eachFoodSaleInfo.validity', '1')
									 	   ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
										   ->get();
				}
				else {
					$q = $this->db->select('COUNT(telephone) as inc')
							  ->where('createTime >=', $row['createDate'])
							  ->where('createTime <=', $sale_count[$key + 1]['createDate'])
							  ->get('first_telephone');
					$q_old_user = $this->db->select('COUNT(telephone) as c_sale')
										   ->from('eachFoodSaleInfo')
										   ->join('first_telephone', 'first_telephone.telephone = eachFoodSaleInfo.user_l_tel')
										   ->where('eachFoodSaleInfo.createTime >=', $row['createDate'])
										   ->where('eachFoodSaleInfo.createTime <=', $sale_count[$key + 1]['createDate'])
										   ->where('first_telephone.createTime <', $row['createDate'])
										   ->where('eachFoodSaleInfo.validity', '1')
						 				   ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
										   ->get();
				}


				$daily_inc[]['inc'] = $q->row_array()['inc'];
				$until_yesterday_tel[]['until_yesterday_tel'] = $yesterday_tel->row_array()['inc'];
				$old_user[]['c_sale'] = $q_old_user->row_array()['c_sale'];
				$old_user_ratio[]['c_sale_ratio'] = $q_old_user->row_array()['c_sale'] / $yesterday_tel->row_array()['inc'];
			}

			// $q_dis_tol = $this->db->select('count(saleId) as s_t')
			// 					  ->where('eachFoodSaleInfo.validity', '1')
			// 			 		  ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
			// 			 		  ->get('eachFoodSaleInfo');
			// $dis_tol = $q_dis_tol->row_array()['s_t'];
			// $keyword = array("西校区", "本部", "南门", "甬江", "一村", "二村", "其他");
			// $keyword_num = count($keyword);
			// foreach ($keyword as $key => $val) {
			// 	if ($key == $keyword_num - 1)
			// 		break;
			// 	$q_loc = $this->db->select('COUNT(saleId) as district')
			// 					  ->like('user_addr', $val)
			// 					  ->where('eachFoodSaleInfo.validity', '1')
			// 			 		  ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
			// 					  ->get('eachFoodSaleInfo');
			// 	$district[]['district'] = $q_loc->row_array()['district'];
			// 	$dis_tol -= $q_loc->row_array()['district'];
			// }
			// $district[]['district'] = $dis_tol;
			$pic = array();
			$title = array();

			$a = array();
			foreach ($sale_count as $row)
				$a[] = substr($row['createDate'], 5); //得到日期

			$this->set_pic_path($pic, 'c_sale_id', '每日订单人数');
			$this->set_pic_title($pic, '日期', '人数', $title);
			$this->picture->gen_sale_count_pic($a, $sale_count,
				 							   $pic[count($pic) - 1]['item_name'], $title);


			$this->set_pic_path($pic, 'inc', '每日手机人数增量');
			$this->set_pic_title($pic, '日期', '增量', $title);
			$this->picture->gen_sale_count_pic($a, $daily_inc,
				 							   $pic[count($pic) - 1]['item_name'], $title);

			$this->set_pic_path($pic, 'money', '每日销售额');
			$this->set_pic_title($pic, '日期', '销售额', $title);
			$this->picture->gen_sale_count_pic($a, $money,
				 							   $pic[count($pic) - 1]['item_name'], $title);


			$this->set_pic_path($pic, 'until_yesterday_tel', '截至昨日的手机注册量');
			$this->set_pic_title($pic, '日期', '手机注册量', $title);
			$this->picture->gen_sale_count_pic($a, $until_yesterday_tel,
				 							   $pic[count($pic) - 1]['item_name'], $title);

			$this->set_pic_path($pic, 'c_sale_ratio', '老顾客回头率');
			$this->set_pic_title($pic, '日期', '回头率', $title);
			$this->picture->gen_sale_count_pic($a, $old_user_ratio,
				 							   $pic[count($pic) - 1]['item_name'], $title);

			$this->set_pic_path($pic, 'c_sale', '老顾客回头数量');
			$this->set_pic_title($pic, '日期', '回头率', $title);
			$this->picture->gen_sale_count_pic($a, $old_user,
				 							   $pic[count($pic) - 1]['item_name'], $title);

			// $this->set_pic_path($pic, 'district', '销售总量区域分布');
			// $this->set_pic_title($pic, '区域', '销售总量', $title);
			// $this->picture->gen_sale_count_pic($keyword, $district,
			// 	 							   $pic[count($pic) - 1]['item_name'], $title);

			$data['pic'] = $pic;
			$this->load->view('rooter/show_deep_search', $data);
		}

		function show_deep_search_region_root($region_id)
		{
			$from_day = date('Y-m-d', strtotime("-5month"));
			$q = $this->db->select('createDate,
							   count(saleId) as c_sale_id')
						 ->from('eachFoodSaleInfo')
						 ->join('storeLoc', 'storeLoc.storeId = eachFoodSaleInfo.storeId')
					     ->join('schoolInfo', 'schoolInfo.schoolId = storeLoc.belongTo')
						 ->join('region', 'region.region_id = schoolInfo.region_code')
						 ->where('region.region_id', $region_id)
						 ->where('createDate >=', $from_day)
						 ->where('eachFoodSaleInfo.validity', '1')
						 ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
						 ->group_by('createDate')
						 ->get();

			$sale_count = $q->result_array();
			$q = $this->db->select('user_l_tel, user_addr, createDate, count(saleId) AS c_sale_id')
						 ->where('createDate >=', $from_day)
						 ->where('eachFoodSaleInfo.validity', '1')
						 ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
						 ->group_by('user_l_tel')
						 ->group_by('createDate')
						 ->from('eachFoodSaleInfo')
					     ->join('storeLoc', 'storeLoc.storeId = eachFoodSaleInfo.storeId')
					     ->join('schoolInfo', 'schoolInfo.schoolId = storeLoc.belongTo')
						 ->join('region', 'region.region_id = schoolInfo.region_code')
						 ->where('region.region_id', $region_id)
						 ->get();

			$tel_sale_detail = $q->result_array();
			$q = $this->db->select('eachFoodSaleInfo.createDate as createDate,
				 					multiFoodSaleInfo.price as price,
				 					multiFoodSaleInfo.num as num')
						  ->from('eachFoodSaleInfo')
						  ->join('multiFoodSaleInfo', 'multiFoodSaleInfo.saleId = eachFoodSaleInfo.saleId')
						  ->where('createDate >=', $from_day)
						  ->where('eachFoodSaleInfo.validity', '1')
						  ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
						  ->join('storeLoc', 'storeLoc.storeId = eachFoodSaleInfo.storeId')
					      ->join('schoolInfo', 'schoolInfo.schoolId = storeLoc.belongTo')
						  ->join('region', 'region.region_id = schoolInfo.region_code')
						  ->where('region.region_id', $region_id)
						  ->order_by('createDate', 'ASC')
						  ->get();
			$old_createDate = $q->row_array()['createDate'];
			$sum = 0;
			$money = array();
			foreach ($q->result_array() as $row) {
				if ($old_createDate != $row['createDate']) {
					$money[]['money'] = $sum;
					$sum = 0;
					$old_createDate = $row['createDate'];
				}
				$sum += $row['price'] * $row['num'];
			}
			$money[]['money'] = $sum;

			$daily_inc = array();
			$until_yesterday_tel = array();
			$old_user = array();
			$old_user_ratio = array();
			$tol = count($sale_count);
			foreach ($sale_count as $key => $row) {
				$yesterday_tel = $this->db->select('COUNT(telephone) as inc')
										  ->where('createTime <', $row['createDate'])
										  ->get('first_telephone');
				if ($key == $tol - 1) {
					$q = $this->db->select('COUNT(telephone) as inc')
						  ->where('createTime >=', $row['createDate'])
						  ->get('first_telephone');
					$q_old_user = $this->db->select('COUNT(telephone) as c_sale')
										   ->from('eachFoodSaleInfo')
										   ->join('first_telephone', 'first_telephone.telephone = eachFoodSaleInfo.user_l_tel')
										   ->where('eachFoodSaleInfo.createTime >=', $row['createDate'])
										   ->where('first_telephone.createTime <', $row['createDate'])
										   ->where('eachFoodSaleInfo.validity', '1')
									 	   ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
										   ->get();
				}
				else {
					$q = $this->db->select('COUNT(telephone) as inc')
							  ->where('createTime >=', $row['createDate'])
							  ->where('createTime <=', $sale_count[$key + 1]['createDate'])
							  ->get('first_telephone');
					$q_old_user = $this->db->select('COUNT(telephone) as c_sale')
										   ->from('eachFoodSaleInfo')
										   ->join('first_telephone', 'first_telephone.telephone = eachFoodSaleInfo.user_l_tel')
										   ->where('eachFoodSaleInfo.createTime >=', $row['createDate'])
										   ->where('eachFoodSaleInfo.createTime <=', $sale_count[$key + 1]['createDate'])
										   ->where('first_telephone.createTime <', $row['createDate'])
										   ->where('eachFoodSaleInfo.validity', '1')
						 				   ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
										   ->get();
				}


				$daily_inc[]['inc'] = $q->row_array()['inc'];
				$until_yesterday_tel[]['until_yesterday_tel'] = $yesterday_tel->row_array()['inc'];
				$old_user[]['c_sale'] = $q_old_user->row_array()['c_sale'];
				$old_user_ratio[]['c_sale_ratio'] = $q_old_user->row_array()['c_sale'] / $yesterday_tel->row_array()['inc'];
			}

			// $q_dis_tol = $this->db->select('count(saleId) as s_t')
			// 					  ->where('eachFoodSaleInfo.validity', '1')
			// 			 		  ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
			// 			 		  ->get('eachFoodSaleInfo');
			// $dis_tol = $q_dis_tol->row_array()['s_t'];
			// $keyword = array("西校区", "本部", "南门", "甬江", "一村", "二村", "其他");
			// $keyword_num = count($keyword);
			// foreach ($keyword as $key => $val) {
			// 	if ($key == $keyword_num - 1)
			// 		break;
			// 	$q_loc = $this->db->select('COUNT(saleId) as district')
			// 					  ->like('user_addr', $val)
			// 					  ->where('eachFoodSaleInfo.validity', '1')
			// 			 		  ->where('eachFoodSaleInfo.storeId !=', YCYC_ID)
			// 					  ->get('eachFoodSaleInfo');
			// 	$district[]['district'] = $q_loc->row_array()['district'];
			// 	$dis_tol -= $q_loc->row_array()['district'];
			// }
			// $district[]['district'] = $dis_tol;
			$pic = array();
			$title = array();

			$a = array();
			foreach ($sale_count as $row)
				$a[] = substr($row['createDate'], 5); //得到日期

			$this->set_pic_path($pic, 'c_sale_id', '每日订单人数');
			$this->set_pic_title($pic, '日期', '人数', $title);
			$this->picture->gen_sale_count_pic($a, $sale_count,
				 							   $pic[count($pic) - 1]['item_name'], $title);


			$this->set_pic_path($pic, 'inc', '每日手机人数增量');
			$this->set_pic_title($pic, '日期', '增量', $title);
			$this->picture->gen_sale_count_pic($a, $daily_inc,
				 							   $pic[count($pic) - 1]['item_name'], $title);

			$this->set_pic_path($pic, 'money', '每日销售额');
			$this->set_pic_title($pic, '日期', '销售额', $title);
			$this->picture->gen_sale_count_pic($a, $money,
				 							   $pic[count($pic) - 1]['item_name'], $title);


			$this->set_pic_path($pic, 'until_yesterday_tel', '截至昨日的手机注册量');
			$this->set_pic_title($pic, '日期', '手机注册量', $title);
			$this->picture->gen_sale_count_pic($a, $until_yesterday_tel,
				 							   $pic[count($pic) - 1]['item_name'], $title);

			$this->set_pic_path($pic, 'c_sale_ratio', '老顾客回头率');
			$this->set_pic_title($pic, '日期', '回头率', $title);
			$this->picture->gen_sale_count_pic($a, $old_user_ratio,
				 							   $pic[count($pic) - 1]['item_name'], $title);

			$this->set_pic_path($pic, 'c_sale', '老顾客回头数量');
			$this->set_pic_title($pic, '日期', '回头率', $title);
			$this->picture->gen_sale_count_pic($a, $old_user,
				 							   $pic[count($pic) - 1]['item_name'], $title);

			// $this->set_pic_path($pic, 'district', '销售总量区域分布');
			// $this->set_pic_title($pic, '区域', '销售总量', $title);
			// $this->picture->gen_sale_count_pic($keyword, $district,
			// 	 							   $pic[count($pic) - 1]['item_name'], $title);

			$data['pic'] = $pic;
			$this->load->view('rooter/show_deep_search', $data);
		}
		function show_deep_search() {
			$region_info = $this->_check_login_valid();
			if ($region_info
			    && ($region_info['prio'] == $this->SUPER_ROOT || $region_info['prio'] == $this->REGION_ROOT)) {
			    	;
			} else
				return;
			$this->_load_head();
			$this->_load_left($region_info);

			// $from_day = '2013-10-15';
			if ($region_info['prio'] == $this->SUPER_ROOT)
				$this->show_deep_search_super_root();
			else
				$this->show_deep_search_region_root($region_info['region']);

		}

		function get_pos_imei($telephone) {
			if (!$this->_check_login_valid())
				return;
			$this->_load_head();
			$this->_load_left();

			$q = $this->db->select('phone, content, time')
						 ->where('phone', $telephone)
						 ->order_by('time', 'desc')
						 ->limit(1)
						 ->get('sms_receive');
			$data['data'] = $q->row_array();
			$this->load->view('rooter/get_pos_imei', $data);
		}

		function sms_record()
		{
			$region_info = $this->_check_login_valid();
			if ($region_info && $region_info['prio'] == $this->SUPER_ROOT)
				;
			else
				return;
			$this->_load_head();
			$this->_load_left($region_info);
    // msgPhone       varchar(20),
    // msgContent     varchar(1000),
    // msgSender      int not null,
    // sendTime       TIMESTAMP,
    // is_disabled    char,
			$q = $this->db->select('msgPhone, msgContent, msgSender, sendTime, is_disabled')
						  ->limit(100)
						  ->order_by('sendTime', 'desc')
						  ->get('sms_text');
			$data['sms_info'] = $q->result_array();
			$this->load->view('rooter/sms_record', $data);
		}

		function sms_configure() {
			$region_info = $this->_check_login_valid();
			if ($region_info && $region_info['prio'] == $this->SUPER_ROOT)
				;
			else
				return;
			$this->_load_head();
			$this->_load_left($region_info);

			$modify_sender = $this->input->post('modify_sender', TRUE);
			$disable = $this->input->post('disable_sms', TRUE);
			if ($modify_sender) {
				$this->db->set('currentSender', $modify_sender);
				if ($disable != '1')
					$disable = '0';
				$this->db->set('disableSms', $disable);
				$this->db->update('configure');
			}

			$q = $this->db->select('currentSender, disableSms')
						  ->get('configure');
			$arr = $q->row_array();
			if ($arr['currentSender'] == DUANXINBAO)
				$data['smsSender'] = "短信宝";
			else if ($arr['currentSender'] == HUAXIN)
				$data['smsSender'] = "创世华信";
			else if ($arr['currentSender'] == CAIXUNTONG)
				$data['smsSender'] = "财迅通";
			else
				$data['smsSender'] = "未知错误";

			$q = $this->db->select('sms_sender')
					 	  ->get('sms_sender');

			$all_sms_sender = array();
			foreach ($q->result_array() as $row) {
				if ($row['sms_sender'] == DUANXINBAO)
					$all_sms_sender[] = array('value'=>$row['sms_sender'],
					                          'name'=>"短信宝");
				else if ($row['sms_sender'] == HUAXIN)
					$all_sms_sender[] = array('value'=>$row['sms_sender'],
					                          'name'=>"创世华信");
				else if ($row['sms_sender'] == CAIXUNTONG)
					$all_sms_sender[] = array('value'=>$row['sms_sender'],
					                          'name'=>"财迅通");
			}
			$data['all_sms_sender'] = $all_sms_sender;
			$data['disableSms'] = $arr['disableSms'];
			$this->load->view('rooter/sms_configure', $data);

		}

		function new_region()
		{
			$region_info = $this->_check_login_valid();
			if ($region_info && $region_info['prio'] == $this->SUPER_ROOT)
				;
			else
				return;
			$this->_load_head();
			$this->_load_left($region_info);
			$post = $this->input->post(NULL, TRUE);
			$data = array();
			if ($post == null)
				$this->load->view('rooter/new_region', $data);
			else {
				$re = $this->picture->upload_img('region');
				$data['success'] = 1;
				if ($re['state']) {
					$valid = $this->rooter_info->region_valid($post['userid'], $post['region_name']);
					if ($valid['valid']) {
						if ($post['password'] == $post['password_again'])
							$this->rooter_info->new_region($post, $re['error']);
						else {
							$data['success'] = 0;
							$data['msg'] = '两次密码不一致';
						}
					}
					else {
						$data['success'] = 0;
						$data['msg'] = $valid['msg'];
					}
				}
				else {
					$data['success'] = 0;
					$data['msg'] = $re['error'];
				}
				$this->load->view('rooter/new_region', $data);
			}
		}

		function new_manage_guy()
		{
			$region_info = $this->_check_login_valid();
			if ($region_info && $region_info['prio'] == $this->REGION_ROOT)
				;
			else
				return;
			$this->_load_head();
			$this->_load_left($region_info);
			$post = $this->input->post(NULL, TRUE);
			$data = array();
			// var_dump($post);
			if ($post) {
				if ($post['guy_type'] && $post['userid']
				    && $post['password'] && $post['password_again']) {
					if ($post['password'] == $post['password_again']) {
						$q = $this->db->select('mana_id')
									  ->where('mana_id', $post['userid'])
									  ->get('management');
						if ($q->num_rows() == 0) {
							if ($post['guy_type'] == '1')
								$prio = $this->REGION_NEW_STORE;
							else if ($post['guy_type'] == '2')
								$prio = $this->REGION_DAILY;
							else
								return;
							$insert_data = array('mana_id'=>$post['userid'],
							                     'mana_pass'=>md5($post['password']),
							                     'mana_prio'=>$prio,
							                     'mana_region'=>$region_info['region']);
							$this->db->insert('management', $insert_data);
							$data['msg'] = '创建成功';
						} else
							$data['msg'] = '用户名重复!';
					} else
						$data['msg'] = '两次密码输入不一致';
				} else
					$data['msg'] = '输入有误，不能为空';
			}
			$this->load->view('rooter/new_manage_guy', $data);
		}

		function sms_caixuntong_chargeup()
		{
			$region_info = $this->_check_login_valid();
			if ($region_info && $region_info['prio'] == $this->SUPER_ROOT)
				;
			else
				return;
			$this->_load_head();
			$this->_load_left($region_info);

			$cardno = $this->input->post('no', true);
			$cardpwd = $this->input->post('pwd', true);

			$data['balance'] = $this->m_sms->caixuntong_balance();
			if (@$cardno && $cardpwd) {
				$data['charge'] = $this->m_sms->caixuntong_chargeup($cardno, $cardpwd);
			}
			$this->load->view('rooter/sms_caixuntong_balance', $data);
		}
	}


