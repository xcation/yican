<?php
	/**
	 * 也是rooter的一部分
	 */
	class Saleman extends CI_Controller {
		public function __construct() {
			parent::__construct();
			$this->load->library('session');
			$this->load->library('get_db_info');
			$this->load->library('header');
			$this->load->model('store_info');
			$this->SUPER_ROOT = '5';
			$this->REGION_ROOT = '4';
			$this->REGION_NEW_STORE = '3';
			$this->REGION_DAILY = '2';
		}
		public function _load_head() {
			$this->load->view('template/head', $this->header->set_header('业务员后台管理界面','',''));
		}
		function _load_left() {
			$region_info = $this->_check_login_valid();
			if ($region_info) {
				if ($region_info['prio'] == $this->SUPER_ROOT)
					$data['super_root'] = 1;
				else if ($region_info['prio'] == $this->REGION_ROOT
				         || $region_info['prio'] == $this->REGION_NEW_STORE
				         || $region_info['prio'] == $this->REGION_DAILY)
					$data['region_manage'] = 1;
				$this->load->view('saleman/left', $data);
			}
		}
		private function _check_login_valid() {
			// if ($this->session->userdata("saleman"))
			// 	return true;
			// return false;
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
					if ($row['prio'] == $this->REGION_DAILY
					    || $row['prio'] == $this->REGION_ROOT
					    || $row['prio'] == $this->REGION_NEW_STORE
					    || $row['prio'] == $this->SUPER_ROOT) {
						$re['prio'] = $row['prio'];
						$re['region'] = $row['region'];
						return $re;
					}
				}
				else
					return 0;
			}
			else
				return 0;
		}

		public function index() {
			$post = $this->input->post(NULL, TRUE);
			if ($post) {
				if ($post['sale_man'] == SALEMAN_ID && md5($post['sale_passwd'] == SALEMAN_PASSWORD)) {
					$this->session->set_userdata(array("saleman"=>true));
					header("location: /saleman/manager");
					return;
				}
				else {
					$this->_load_head();
					$this->load->view('saleman/login');
					return;
				}
			}

			if ($this->_check_login_valid())
				header("location: /saleman/manager");
			else {
				$this->_load_head();
				$this->load->view('saleman/login');
				return;
			}
		}
		public function manager() {
			if ($this->_check_login_valid())
				$this->cancel_sale();
		}


		public function cancel_sale($confirm = 0) {
			$region_info = $this->_check_login_valid();
			if (!$region_info)
				return;
			$this->_load_head();
			$this->_load_left();

			$q = $this->db->select('saleId,
								    buyerId,
								    eachFoodSaleInfo.storeId as storeId,
								    storeIntro.storeName as storeName,
								    storeIntro.telephone as phone,
								    university_id,
								    schoolInfo.schoolFullName as university_name,
								    eachFoodSaleInfo.createTime as createTime,
								    eachFoodSaleInfo.cancel_post_time as cancel_post_time,
								    eachFoodSaleInfo.cancel_reason as cancel_reason,
								    user_addr,
								    user_l_tel,
								    user_s_tel')
						  ->from('eachFoodSaleInfo')
						  ->join('storeIntro', 'storeIntro.storeId = eachFoodSaleInfo.storeId')
						  ->join('schoolInfo', 'schoolInfo.schoolId = eachFoodSaleInfo.university_id')
						  ->join('region', 'region.region_id = schoolInfo.region_code')
						  ->where('cancel', '1')
						  ->where('region.region_id', $region_info['region'])
						  ->order_by('cancel_post_time', 'asc')
						  ->get();
			$cancel_info['cancel_info'] = $q->result_array();

			if ($q->num_rows() > 0) {
				foreach($cancel_info['cancel_info'] as &$row) {
					$q = $this->db->select('multiFoodSaleInfo.foodId as foodId,
										   foodName,
										   multiFoodSaleInfo.price as price,
										   multiFoodSaleInfo.num as num')
								  ->from('multiFoodSaleInfo')
								  ->join('foodInfo', 'foodInfo.foodId = multiFoodSaleInfo.foodId')
								  ->where('saleId', $row['saleId'])
								  ->get('');
					$row['sale_details'] = $q->result_array();
				}
			}
			if ($confirm)
				$cancel_info['confirm'] = true;
			$this->load->view('saleman/cancel_sale', $cancel_info);
		}

		function deal_cancel($success, $sale_id) {
			if (!$this->_check_login_valid())
				return;
			if ($success) {
				$data = array('cancel_deal_time'=>date('Y-m-d H:i:s'),
					          'cancel'=>'3',
					          'validity'=>'2');
				$this->db->where('saleId', $sale_id);
				$this->db->update('eachFoodSaleInfo', $data);
			}
			else {
				$data = array('cancel_deal_time'=>date('Y-m-d H:i:s'),
					          'cancel'=>'2');
				$this->db->where('saleId', $sale_id);
				$this->db->update('eachFoodSaleInfo', $data);
			}
			header('location: /saleman/cancel_sale/1');
		}

		function telephone_search() {
			if (!$this->_check_login_valid())
				return;
			$this->_load_head();
			$this->_load_left();
			$tel = $this->input->post('telphone', TRUE);
			$data = array();
			if ($tel) {
				$data['post'] = true;
				$data['tel'] = $tel;
				$q = $this->db->select('saleId, user_addr,
										createTime')
							  ->where('validity', '1')
							  ->where('user_l_tel', $tel)
							  ->order_by('createTime', 'desc')
							  ->get('eachFoodSaleInfo');
				$data['sale'] = $q->result_array();
			}
			$this->load->view('saleman/telephone_search', $data);
		}

		function _load_sale_detail($sale_id, &$data) {
			$sale_info = array();
			$select_option = "saleId, eachFoodSaleInfo.storeId AS storeId,
						    user_addr, user_l_tel,
						    user_s_tel, taste,
						    university_id,
						    eachFoodSaleInfo.createTime AS createTime,
						    first_urgent_time,
						    storeIntro.storeName AS storeName,
						    storeIntro.telephone AS contact_phone,
						    storeIntro.telephone_1 AS contact_phone_1,
						    storeIntro.telephone_2 AS contact_phone_2,
						    score,
						    validity,
						    judgement AS delivery_comment,
						    from_tel";
			$q = $this->db->select($select_option)
					  ->from('eachFoodSaleInfo')
					  ->join('storeIntro', 'eachFoodSaleInfo.storeId=storeIntro.storeId')
					  ->where('saleId', $sale_id)
					  ->order_by('eachFoodSaleInfo.createTime', 'desc')
					  ->get();
			if ($q->num_rows() > 0) {
				$sale_info = $q->row_array();
				$q = $this->db->select('foodInfo.foodId as foodId,
										foodInfo.foodName as foodName,
							   			multiFoodSaleInfo.price as price,
							   			multiFoodSaleInfo.num as num')
							  ->from('multiFoodSaleInfo')
							  ->join('foodInfo', 'multiFoodSaleInfo.foodId = foodInfo.foodId')
							  ->where('saleId', $sale_id)
							  ->get();
				$sale_info['food_info'] = $q->result_array();
				$data['sale'] = $sale_info;
			}
			else {
				$data['empty'] = true;
			}
		}
		function sale_details($sale_id = 0) {
			if (!$this->_check_login_valid())
				return;
			$this->_load_head();
			$this->_load_left();

			$this->_load_sale_detail($sale_id, $data);
			$this->load->view('saleman/sale_details', $data);
		}

		function urgent() {
			$region_info = $this->_check_login_valid();
			if (!$region_info)
				return;
			$this->_load_head();
			$this->_load_left();

			$q = $this->db->select('saleId, urgent_time')
						  ->where('urgent', '1')
						  ->from('eachFoodSaleInfo')
						  ->join('schoolInfo', 'schoolInfo.schoolId = eachFoodSaleInfo.university_id')
						  ->join('region', 'region.region_id = schoolInfo.region_code')
						  ->where('region.region_id', $region_info['region'])
						  ->get();
			if ($q->num_rows() > 0) {
				$data['urgent'] = $q->result_array();
			}
			else
				$data['empty'] = true;
			$this->load->view('saleman/urgent', $data);
		}

		function urgent_finished($sale_id) {
			if (!$this->_check_login_valid())
				return;
			header("content-type: application/json; charset=utf-8");
			$this->db->set('urgent', '0');
			$this->db->set('deal_urgent_time', date('Y-m-d H:i:s'));
			$this->db->where('saleId', $sale_id);
			$this->db->update('eachFoodSaleInfo');
			if ($this->db->affected_rows() > 0)
				echo json_encode(array('state'=>1));
			else
				echo json_encode(array('state'=>0));
		}

		function pei() {
			$region_info = $this->_check_login_valid();
			if (!$region_info)
				return;
			$this->_load_head();
			$this->_load_left();

			$q = $this->db->select('saleId, pei_time')
						  ->where('pei', '1')
						  ->from('eachFoodSaleInfo')
						  ->join('schoolInfo', 'schoolInfo.schoolId = eachFoodSaleInfo.university_id')
						  ->join('region', 'region.region_id = schoolInfo.region_code')
						  ->where('region.region_id', $region_info['region'])
						  ->get();
			if ($q->num_rows() > 0) {
				$data['pei'] = $q->result_array();
			}
			else
				$data['empty'] = true;
			$this->load->view('saleman/pei', $data);
		}

		function pei_finished($sale_id) {
			if (!$this->_check_login_valid())
				return;
			header("content-type: application/json; charset=utf-8");
			$this->db->set('pei', '0');
			$this->db->set('deal_pei_time', date('Y-m-d H:i:s'));
			$this->db->where('saleId', $sale_id);
			$this->db->update('eachFoodSaleInfo');
			if ($this->db->affected_rows() > 0)
				echo json_encode(array('state'=>1));
			else
				echo json_encode(array('state'=>0));
		}


		function sale_id_search() {
			if (!$this->_check_login_valid())
				return;
			$this->_load_head();
			$this->_load_left();
			$data = array();
			$sale_id = $this->input->post('sale_id', TRUE);
			if ($sale_id) {
				$this->_load_sale_detail($sale_id, $data);
				$data['post'] = true;
				$data['sale_id'] = $sale_id;
			}
			$this->load->view('saleman/sale_id_search', $data);
		}

		function pos_error() {
			if (!$this->_check_login_valid())
				return;
			$this->_load_head();
			$this->_load_left();
			$data = array();

			$q = $this->db->select('storeId as store_id,
								   storeName as store_name,
								   telephone as tel_1,
								   telephone_1 as tel_2,
								   telephone_2 as tel_3')
						 ->where('pos_error_send', '1')
						 ->get('storeIntro');
			$data['pos_error'] = $q->result_array();
			$this->load->view('saleman/pos_error', $data);
		}

		function pos_error_finished($store_id) {
			if (!$this->_check_login_valid())
				return;
			header("content-type: application/json; charset=utf-8");
			$this->db->set('pos_error_send', '0');
			$this->db->where('storeId', $store_id);
			$this->db->update('storeIntro');
			if ($this->db->affected_rows() > 0)
				echo json_encode(array('state'=>1));
			else
				echo json_encode(array('state'=>0));
		}

	  	private function _getRand($proArr) {
			$result = '';

			//概率数组的总概率精度
			$proSum = array_sum($proArr);
			//概率数组循环
			foreach ($proArr as $key => $proCur) {
			    $randNum = mt_rand(1, $proSum);
			    if ($randNum <= $proCur) {
			        $result = $key;
			        break;
			    }
			    else
			        $proSum -= $proCur;
			}
			unset ($proArr);
			return $result;
		}

		function lottory() {
			if (!$this->_check_login_valid())
				return;
			$again = $this->input->post('again', TRUE);
			$posibility = $this->input->post('posibility', TRUE);
			$first_posibility = $this->input->post('first_posibility', TRUE);
			if ($first_posibility) {
				$this->_load_head();
				$this->_load_left();
				$data['first_posibility'] = $first_posibility;
				$this->load->view('saleman/lottory', $data);
				return;
			}
			if ($posibility == -1) {
				echo json_encode(array("err"=>1));
				return;
			}
			if ($again == 1) {
				if ($posibility == 0.1)
					$cheat_pos = 0;
				else
					$cheat_pos = $posibility * 100;
				$second = $third = (int)((100 - $cheat_pos) / 6);
				$last = 100 - $second *  5 - $cheat_pos;
				$prize_arr = array(
							    '0' => array('id'=>1,'min'=>1,'max'=>29,'prize'=>'一等奖','v'=>$cheat_pos),
							    '1' => array('id'=>2,'min'=>302,'max'=>328,'prize'=>'0','v'=>$second),
							    '2' => array('id'=>3,'min'=>242,'max'=>268,'prize'=>'0','v'=>$second),
							    '3' => array('id'=>4,'min'=>182,'max'=>208,'prize'=>'0','v'=>$second),
							    '4' => array('id'=>5,'min'=>122,'max'=>148,'prize'=>'1','v'=>$third),
							    '5' => array('id'=>6,'min'=>62,'max'=>88,'prize'=>'1','v'=>$third),
							    '6' => array('id'=>7,'min'=>array(32,92,152,212,272,332),
								'max'=>array(58,118,178,238,298,358),'prize'=>'1','v'=>$last)
							);
				foreach ($prize_arr as $key => $val)
					$arr[$val['id']] = $val['v'];

				$rid = $this->_getRand($arr); //根据概率获取奖项id

				$res = $prize_arr[$rid-1]; //中奖项
				$min = $res['min'];
				$max = $res['max'];
				if($res['id']==7){ //七等奖
					$i = mt_rand(0,5);
					$result['angle'] = mt_rand($min[$i],$max[$i]);
				}
				else
					$result['angle'] = mt_rand($min,$max); //随机生成一个角度
				if ($res['prize'] == '0')
					$result['prize'] = "参赛奖";
				else if ($res['prize'] == '1')
					$result['prize'] = "谢谢参与";
				else
					$result['prize'] = "一等奖";
				$result['err'] = 0;
				echo json_encode($result);
			}
			else {
				$this->_load_head();
				$this->_load_left();
				$this->load->view('saleman/lottory');
			}
		}
	}
