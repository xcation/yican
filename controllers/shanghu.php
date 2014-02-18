<?php
// 不要忘记所有都需要检查session
	/**
	 * 商户管理界面
	 */
	class Shanghu extends CI_Controller {
		public function __construct() {
			parent::__construct();
			$this->load->library('session');
			$this->load->library('header');
			$this->load->library('state_name');
			$this->load->model('shanghu_info');
			$this->load->model('food_info');
			$this->load->model('store_info');
			$this->load->library('get_db_info');
			$this->load->library('picture');
			$this->load->helper('cookie');
		}
		private function _load_top($title) {
			$this->load->view('template/head', $this->header->set_header($title));
			$data['shanghu'] = true;
			$data['search_not_avai'] = true;
			$this->load->view('topbar', $data);
		}
		private function _check_store_not_out($store_id) {
			$q = $this->db->select('state')
						  ->where('storeId', $store_id)
						  ->where('state', '5')
						  ->get('storeIntro');
			if ($q->num_rows() > 0)
				return false;
			return true;
		}
		private function _check_valid($store_id) {
			$s_i = $this->session->userdata('storeid');
			$store_login_id = $this->get_db_info->get_store_login_id($store_id);
			if ($s_i && $s_i == $store_login_id)
				return $this->_check_store_not_out($store_id);
			$c_i = $this->input->cookie('storeid');
			if ($c_i && $c_i == $store_login_id)
				return $this->_check_store_not_out($store_id);
			return false;
		}
		public function index($store_id) {
			$this->food_manage($store_id);
		}
		private function _load_nav($store_id) {
			$nav['store_id'] = $store_id;
			$nav['s_name'] = $this->get_db_info->get_store_name($store_id);
			$nav['now_state_id'] = $this->get_db_info->get_shanghu_now_state_with_id($store_id);
			$nav['now_state_name'] = $this->state_name->get_state_name($nav['now_state_id']);
			$nav['all_state'] = $this->state_name->get_all_state_array();
			$this->load->view('shanghu/nav', $nav);
		}
		public function food_manage($store_id) {
			if ($this->_check_valid($store_id)) {
				$this->_load_top('美食管理');
				$this->_load_nav($store_id);
				$food['data'] = $this->shanghu_info->food_type($store_id);

				$this->load->view('shanghu/new_food', $food);
			}
			else {
				header('location: /login');
			}
		}
		function store_info_manage($store_id) {
			if ($this->_check_valid($store_id)) {
				$this->_load_top('商店信息管理');
				$this->_load_nav($store_id);

				$this->_load_store_info($store_id);
			}
			else {
				header('location: /login');
			}
		}

		private function _load_store_info($store_id, $error = null) {
			$data = $this->shanghu_info->get_all_store_info($store_id);
			$data['error'] = $error;
			$this->load->view('shanghu/info_manage', $data);
		}
		function post_store_info($store_id) {
			if ($this->_check_valid($store_id)) {
				$post = $this->input->post(NULL, TRUE);
				if (!$post)
					return;
				$this->_load_top('提交商店信息');
				$this->_load_nav($store_id);

				$pics_loc = null;

				foreach ($_FILES as $key=>$row) {

					// echo "<script type='text/javascript'>alert('{$pics_loc}');</script>";
					$picname = $row['name'];
					if (!$picname)
						break;
					$type = strstr($picname, '.');
					if ($type != '.gif' &&
						$type != '.jpg' &&
						$type != '.png') {

						$this->_load_store_info($store_id, "图片格式不对");
						return;
					}
					else {
						$rand = mt_rand(100, 999);
	        			$pics_loc = date("YmdHis") . $rand . $type;

	        			$old_pic_loc = $this->get_db_info->get_store_img_loc($store_id);
	        			$this->picture->delete_store_pic(LOC_PREFIC."/img/store/".$old_pic_loc);
						move_uploaded_file($row['tmp_name'], LOC_PREFIC."/img/store/".$pics_loc);
						$this->picture->compress(LOC_PREFIC."/img/store/".$pics_loc, $type, 170, 120);
					}
				}


				$this->shanghu_info->confirm_store_info($store_id, $post, $pics_loc);

				$this->_load_store_info($store_id, "更新成功");
			}
			else {
				header('location: /login');
			}
		}
		function m_is_digit($price) {
			return preg_match("/^[0-9]+\.?[0-9]*$/",$price);
		}
		function ajax($store_id, $op, $food_id, $name) {
			if (!$this->_check_valid($store_id))
				return;
			header("content-type: application/json; charset=utf-8");
			$name=urldecode($name);

			$a = array('food_name'=>"foodName",
					   'price'=>"price",
					   'food_note'=>"note",
					   'food_avail'=>'isAvailable');
			$o = array(
					$a[$op]=>$name
				);
			if ($op=='price' && !$this->m_is_digit($name) ) {
				echo json_encode(array("state"=>0,"error"=>'异常输入'));
				return;
			}
			$this->db->where('foodId', $food_id);
			$this->db->update('foodInfo', $o);
			echo json_encode(array("state"=>1));
		}
		function img_upload($store_id) {
			if (!$this->_check_valid($store_id))
				return;

			header("content-type: text/html; charset=utf-8");
			$re = array();
			if (count($_FILES) == 0) {
				$re['error'] = "空文件";
				$re['state'] = 0;
			}
			foreach ($_FILES as $key=>$row) {
				$picname = $row['name'];
				$type = strstr($picname, '.');
				if ($type != '.gif' &&
					$type != '.jpg' &&
					$type != '.png') {
					$re['state'] = 0;
					$re['error']="图片格式不对";
				}
				else {
					$rand = rand(100, 999);
        			$pics = date("YmdHis") . $rand . $type;
					$id = explode("_", $key);
					$id=$id[2];
					$re['state'] = 1;
					move_uploaded_file($row['tmp_name'], LOC_PREFIC."/img/food/".$pics);

					$this->picture->compress(LOC_PREFIC."/img/food/".$pics, $type, 240, 140);

					$this->shanghu_info->add_food_img($id, $pics);
					$re['path'] = $pics;
				}
			}
			echo json_encode($re);
		}
		function add_dish($store_id, $food_type, $avail, $name = "", $price = "", $note = "") {
			header("content-type: application/json; charset=utf-8");
			if (!$this->_check_valid($store_id))
				return;
			$food_type = urldecode($food_type);
			$food_type = explode('_', $food_type);
			$food_type = $food_type[3];
			$name = urldecode($name);
			$price = urldecode($price);
			$note = urldecode($note);
			$avail = urldecode($avail);
			$error = false;
			if( $this->m_is_digit($price) && $name != "")
				$food_id = $this->shanghu_info->add_dish($store_id, $food_type, $name, $price, $note, $avail);
			else {
				$food_id = 0;
				$error = "异常输入";
			}
			echo json_encode(array("id"=>$food_id,"error"=>$error));
		}
		function new_type($store_id, $val) {
			header("content-type: application/json; charset=utf-8");
			if (!$this->_check_valid($store_id))
				return;
			$val = urldecode($val);
			$type_id = $this->shanghu_info->add_food_type($store_id, $val);
			echo json_encode(array("id"=>$type_id));
		}
		function check_new_sale($store_id) {
			echo $this->shanghu_info->check_new_sale($store_id);
		}
		function check_received($sale_id) {
			$data = array("confirmTime"=>date("Y-m-d H:i:s"));
			$this->db->where('saleId', $sale_id);
			$this->db->update('eachFoodSaleInfo', $data);
		}
		// 修改商户的工作状态
		function change_state($store_id, $state_id) {
			$this->db->where('storeId', $store_id);
			$data = array(
						"user_in_charge_time"=>date("Y-m-d H:i:s"),
						"state"=>$state_id
					);
			$this->db->update('storeIntro', $data);
		}
		function delete_food($store_id, $food_id) {
			if (!$this->_check_valid($store_id))
				return;
			$this->db->set('deleted', '1');
			$this->db->where('foodId', $food_id);
			$q = $this->db->update('foodInfo');
			header("content-type: application/json; charset=utf-8");
			echo json_encode(true);
		}
		function delete_food_type($store_id, $type_id) {
			if (!$this->_check_valid($store_id))
				return;
			$this->db->set('deleted', '1');
			$this->db->where('foodTypeId', $type_id);
			$this->db->update('foodTypeInEachStore');

			$this->db->set('deleted', '1');
			$this->db->where('foodTypeId', $type_id);
			$this->db->update('foodInfo');
			header("content-type: application/json; charset=utf-8");
			echo json_encode(array('state'=>true));
		}
		function sale_info($store_id) {
			if ($this->_check_valid($store_id)) {
				$this->_load_top('交易信息查询');
				$this->_load_nav($store_id);
				$post = $this->input->post('sale_month', TRUE);
				if (!$post)
					$sale['sale_info'] = $this->shanghu_info->sale_money($store_id, '0');
				else
					$sale['sale_info'] = $this->shanghu_info->sale_money($store_id, $post);
				if (@$sale['sale_info']['error']) {
					$sale['error'] = true;
				}
				$this->load->view('shanghu/sale_money', $sale);

			}
			else {
				header('location: /login');
			}
		}
		function details($store_id, $date) {
			if ($this->_check_valid($store_id)) {
				$this->_load_top('交易详情');
				$this->_load_nav($store_id);
				$sale['details'] = $this->shanghu_info->sale_details($store_id, $date);
				$sale['date'] = $date;

				$this->load->view('shanghu/sale_details', $sale);
			}
			else {
				header('location: /login');
			}
		}
		function logout() {
			delete_cookie('storeid');
			$this->session->unset_userdata('storeid');
			header('location: /');
		}
		function change_type_order($store_id) {
			if ($this->_check_valid($store_id)) {
				$this->_load_top('修改食物种类顺序');
				$this->_load_nav($store_id);
				if ( ($type_order = $this->input->post('type_order', TRUE)) &&
					 ($type_name = $this->input->post('type_name', TRUE)) ) {
					$this->shanghu_info->update_food_type_order($type_order, $type_name);
					$order['post'] = true;
				}
				$order['all_type'] = $this->shanghu_info->get_food_type_order($store_id);
				$order['store_id'] = $store_id;
				$this->load->view('shanghu/change_food_type_order', $order);
			}
			else {
				header('location: /login');
			}
		}

		function change_food_order($store_id, $food_type_id = 0, $img_or_not = 2) {
			if ($this->_check_valid($store_id)) {
				$this->_load_top('修改食物顺序');
				$this->_load_nav($store_id);
				$order['store_id'] = $store_id;
				if ($food_type_id == 0) {
					$order['all_type'] = $this->shanghu_info->get_food_type_order($store_id);
				}
				else {
					$order['food_type_id'] = $food_type_id;
					if ($img_or_not == 2) {
						$order['img_or_not'] = true;
					}
					else {
						$food_order = $this->input->post('food_order', TRUE);
						if ($food_order) {
							$this->shanghu_info->update_food_order($food_order);
							$order['post'] = true;
						}
						$order['img'] = $img_or_not;
						$order['all_food'] = $this->shanghu_info->get_all_food_with_type_id($food_type_id, $img_or_not);
					}
				}

				$this->load->view('shanghu/change_food_order', $order);
			}
			else {
				header('location: /login');
			}
		}
		function change_passwd($store_id) {
			if ($this->_check_valid($store_id)) {
				$this->_load_top('修改食物顺序');
				$this->_load_nav($store_id);
				$data= array();
				if ( ($passwd_1 = $this->input->post('passwd_1')) &&
					 ($passwd_2 = $this->input->post('passwd_2')) ) {
					if ($passwd_1 == $passwd_2) {
						$this->store_info->change_passwd($store_id, $passwd_1);
						$data['success'] = '修改成功';
					}
					else
						$data['error'] = '两次密码不一致';
				}
				$this->load->view('shanghu/change_passwd', $data);
			}
			else {
				header('location: /login');
			}
		}

		function cancel_sale($store_id) {
			if ($this->_check_valid($store_id)) {
				$this->_load_top('取消订单');
				$this->_load_nav($store_id);
				$sale_id = $this->input->post('sale_id', TRUE);
				$reason = $this->input->post('reason', TRUE);
				$cancel = array();
				if ($sale_id && $reason) {
					$cancel['success'] = false;
					$cancel['post'] = true;
					$q = $this->db->select('saleId')
								  ->where('storeId', $store_id)
								  ->where('saleId', $sale_id)
								  ->get('eachFoodSaleInfo');
					if ($q->num_rows() > 0) {
						$this->db->where('saleId', $sale_id);
						$data = array('cancel'=>'1',
									  'cancel_reason'=>$reason,
									  'cancel_post_time'=>date('Y-m-d H:i:s'));
						$this->db->update('eachFoodSaleInfo',$data);
						if ($this->db->affected_rows() > 0) {
							$cancel['success'] = true;
						}
						else
							$cancel['error'] = '提交失败';
					}
					else
						$cancel['error'] = '非法取消其他商家的订单';

				}
				$this->load->view('shanghu/cancel_sale', $cancel);
			}
			else {
				header('location: /login');
			}
		}
		function check_cancel_sale($store_id) {
			if ($this->_check_valid($store_id)) {
				$this->_load_top('查看正在取消的订单');
				$this->_load_nav($store_id);
				$q = $this->db->select('saleId')
							 ->where('storeId', $store_id)
							 ->where('cancel', '1')
							 ->get('eachFoodSaleInfo');
				$data['cancel_sale'] = $q->result_array();
				$this->load->view('shanghu/check_cancel_sale', $data);
			}
			else {
				header('location: /login');
			}
		}

	}