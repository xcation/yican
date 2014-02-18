<?php
	/**
	 * 用户管理界面
	 */
	class Management extends CI_Controller {
		var $login;
		var $user_id;
		public function __construct() {
			parent::__construct();
			$this->load->library('header');
			$this->load->helper('cookie');
			$this->load->model('management_info');
			$this->load->model('user_info');
			$this->load->model('m_sms');
			$this->load->model('about_us_info');

		}
		function _load_top() {
			$this->load->view('template/head',
							  $this->header->set_header("个人管理 想叫外卖，上一餐易餐"));
			$userinfo = $this->user_info->get_login();
			$this->login = $userinfo['login'];
			$this->user_id = @$userinfo['userName'];
			$this->load->view('topbar', $userinfo);
		}
		public function index() {
			$this->order();
		}
		public function order() {
			$this->_load_top();
			$data['food_order'] = $this->management_info->order($this->login, $this->user_id);
			$data['login'] = $this->login;
			$this->load->view('management/order', $data);
		}
		public function success() {
			$this->_load_top();
			$this->load->view('management/success');
			$footer['footer'] = $this->about_us_info->get_footer();
			$this->load->view('template/footer_not_index', $footer);
		}
		public function error($err) {
			if (!isset($err))
				header('location: /');
			$this->_load_top();
			$data['error'] = urldecode($err);
			$this->load->view('management/success', $data);
			$footer['footer'] = $this->about_us_info->get_footer();
			$this->load->view('template/footer_not_index', $footer);
		}

		public function store_comment($store_id, $sale_id, $score, $comment = "") {
			header("content-type: application/json; charset=utf-8");
			$comment = urldecode($comment);
			$this->management_info->store_comment($store_id, $sale_id, $score, $comment);
			echo json_encode(array("state"=>1));
		}
		public function food_comment($sale_id, $food_id, $score, $comment = "") {
			header("content-type: application/json; charset=utf-8");
			$comment = urldecode($comment);
			$this->management_info->food_comment($sale_id, $food_id, $score, $comment);
			echo json_encode(array("state"=>1));
		}

		public function urgent($sale_id) {
			header("content-type: application/json; charset=utf-8");
			$q = $this->db->select('saleId, urgent_time, first_urgent_time')
			              ->where('validity', '1')
			              ->where('saleId', $sale_id)
			              // 非常不完备
			              ->where('unix_timestamp(now()) - unix_timestamp(createTime) between 600 and 10800')
			              ->get('eachFoodSaleInfo');
			if ($q->num_rows() > 0) {
				$now = date('Y-m-d H:i:s');
				$first_urgent_time = $q->row_array()['first_urgent_time'];
				if (!$first_urgent_time)
					$this->db->set('first_urgent_time', $now);
				else if ($strtotime($now) - strtotime($first_urgent_time) < 600) {
					echo json_encode(array('state'=>3));
					return;
				}
				$this->db->set('urgent', '1');
				$this->db->set('urgent_time', $now);
				$this->db->where('saleId', $sale_id);
				$this->db->update('eachFoodSaleInfo');
				$this->m_sms->send_urgent($sale_id);
				if ($this->db->affected_rows() > 0)
					echo json_encode(array('state'=>1));
				else
					echo json_encode(array('state'=>0));
			}
			else
				echo json_encode(array('state'=>2)); //提交无效订单
		}

		public function pei($sale_id) {
			header("content-type: application/json; charset=utf-8");
			$q = $this->db->select('saleId, pei_time')
			              ->where('validity', '1')
			              ->where('saleId', $sale_id)
			              ->where('unix_timestamp(now()) - unix_timestamp(createTime) between 2400 and 10800')
			              ->get('eachFoodSaleInfo');
			if ($q->num_rows() > 0) {
				$now = date('Y-m-d H:i:s');
				$last_pei_time = $q->row_array()['pei_time'];
				if (strtotime($now) - strtotime($first_urgent_time) < 600)
					echo json_encode(array('state'=>3));
				else {
					$this->db->set('pei', '1');
					$this->db->set('pei_time', date('Y-m-d H:i:s'));
					$this->db->where('saleId', $sale_id);
					$this->db->update('eachFoodSaleInfo');

					$this->m_sms->send_pei($sale_id);
					if ($this->db->affected_rows() > 0)
						echo json_encode(array('state'=>1));
					else
						echo json_encode(array('state'=>0));
				}
			}
			else
				echo json_encode(array('state'=>2)); //提交无效订单
		}
	}