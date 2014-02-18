<?php
	/**
	 * 一个大学或者网吧的具体内容
	 */
	class University extends CI_Controller {

		public function __construct() {
			parent::__construct();
			$this->load->library('session');
			$this->load->model('homePageSchoolInfo');
			$this->load->model('store_info');
			$this->load->model('quick_find');
			$this->load->model('user_info');
			$this->load->model('about_us_info');
			$this->load->library('header');
			$this->load->library('get_db_info');
		}

		private function _load_top($shortName) {
			$userInfo = array();
			$univ_full = $this->get_db_info->get_univeristy_full_with_short_name($shortName);
			if ($univ_full) {
				// keyword, description, title
				$head_info = $this->header->set_header("到{$univ_full}"."叫外卖，上一餐易餐");
				$this->load->view('template/head', $head_info);


				$userInfo = $this->user_info->get_login();
				$userInfo['university'] = $univ_full;
				$userInfo['storeName'] = "";
				$userInfo['shortName'] = $shortName;
				$userInfo['in_step'] = true; // ??ʾ?ڶ??͹?????
				$userInfo['step_two'] = "active"; // ???͵ڶ???
				$this->load->view('topbar', $userInfo);
			}
		}

		private function _load_store_info($university_id, $shortName ) {
			$data['store_info'] = $this->store_info->get_store_info($university_id);
			// 得到所有的种类，比如奶茶等等
			$data['store_type'] = $this->store_info->get_store_type();
			$data['university_id'] = $university_id;
			$data['img'] = $this->quick_find->quick_find_food_info($university_id, $shortName);

			$data['block_info'] = $this->store_info->get_block();

			$data['announce'] = $this->about_us_info->get_announce();
			$this->load->view('store_info', $data);
		}
		public function index($shortName) {

			$university_id = $this->get_db_info->get_university_id_with_short_name($shortName);

			if (!$university_id) {
				header("location: /");
				return;
			}
			$expire_time = 30*24*60*60;
			$cookie = array(
			    'name'   => 'location',
			    'value'  => $shortName,
			    'expire' => COOKIE_TIME,
			    'path'   => '/'
			);
			$this->input->set_cookie($cookie);

			$this->_load_top($shortName);


			$this->_load_store_info($university_id, $shortName);
			$footer['footer'] = $this->about_us_info->get_footer();
			$this->load->view('template/footer_not_index', $footer);


		}
		public function ajax_get_store_info($order_type, $university_id, $open, $taste, $cost) {
			$data['store_info'] = $this->store_info->ajax_get_store_info($order_type, $university_id, $open, $taste, $cost, $order_type);
			$data['university_id'] = $university_id;
			$data['i'] = $order_type;
			$this->load->view('restaurant_body', $data);
		}

	}