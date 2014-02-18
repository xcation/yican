<?php
	/**
	 * 一家商店的具体店面
	 */
	class Store extends CI_Controller {
		var $storeName;
		public function __construct() {
			parent::__construct();
			$this->load->library('session');
			$this->load->model('homePageSchoolInfo');
			$this->load->model('food_info');
			$this->load->model('store_info');
			$this->load->model('user_info');
			$this->load->library('get_db_info');
			$this->load->library('header');
			$this->load->library('state_name');
			$this->load->model('about_us_info');
		}

		private function _load_top($university_id, $store_id) {
			$userInfo = $this->user_info->get_login();
			$userInfo['university'] = $this->get_db_info->get_university_full_with_id($university_id);
			$userInfo['shortName'] = $this->get_db_info->get_university_short_with_id($university_id);
			if ($userInfo['university']) {
				$userInfo['storeName'] = $this->get_db_info->get_store_name($store_id);
				if (!$userInfo['storeName'])
					header("location: /errors/location");

				$this->load->view('template/head',
								  $this->header->set_header("欢迎来到{$userInfo['university']}——{$userInfo['storeName']}订餐，想叫外卖，上一餐易餐"));

				$this->storeName = $userInfo['storeName'];

				$userInfo['university_id'] = $university_id;
				$userInfo['store_id'] = $store_id;
				$userInfo['in_step'] = true;
				$userInfo['step_three'] = "active";
				$this->load->view('topbar', $userInfo);
			}
			else {
				header("location: /errors/location");
			}
		}

		public function index($university_id, $store_id) {
			$this->_load_top($university_id, $store_id);

			$data = $this->store_info->get_store_top_info($university_id, $store_id);
			if ($data) {
				$data['food_info'] = $this->food_info->get_food_info($store_id);
				$data['food_type'] = $this->food_info->re;
				$data['store_id'] = $store_id;
				$data['store_name'] = $this->storeName;
				$now_state = $this->store_info->get_shanghu_state($store_id);

				$data['state'] = $this->state_name->is_work_state($now_state);
				$data['delivery_cost'] = $this->store_info
											  ->get_delivery_cost($university_id, $store_id);
				$data['university_id'] = $university_id;
				$food_id = $this->input->post('food', TRUE);
				if ($food_id)
					$data['another_one'] = $food_id;

				$this->load->view('food_info', $data);
			}
			else {
				$error['error'] = '没有找到相应的店铺';
				$this->load->view('error', $error);
			}
			$footer['footer'] = $this->about_us_info->get_footer();
			$this->load->view('template/footer_not_index', $footer);

		}
	}