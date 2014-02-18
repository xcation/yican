<?php
	/**
	 * 搜索功能
	 */
	class Search extends CI_Controller {
		public function __construct() {
			parent::__construct();
			// $this->load->library('session');
			// $this->load->model('homePageSchoolInfo');
			// $this->load->model('food_info');
			$this->load->model('search_info');
			$this->load->model('user_info');
			$this->load->library('get_db_info');
			$this->load->library('header');
			$this->load->model('about_us_info');
			// $this->load->library('state_name');
		}
		public function _load_top() {
			$this->load->view('template/head',
							  $this->header->set_header('搜索餐厅，食物等等'));
			$userinfo = $this->user_info->get_login();
			$this->load->view('topbar', $userinfo);
		}
		public function index() {
			$this->_load_top();
			$univ = $this->input->cookie('location' ,TRUE);
			if (!$univ)
				$data['error'] = '没有找到相应的大学，请返回主页选择大学后继续操作';
			else {
				$univ_id = $this->get_db_info->get_university_id_with_short_name($univ);
				$keyword = $this->input->post('keyword', TRUE);
				if ($keyword) {
					$data = $this->search_info->key($univ_id, $keyword);
					$data['university_id'] = $univ_id;
					$data['university_full'] = $this->get_db_info->get_univeristy_full_with_short_name($univ);
				}
				else
					$data['error'] = '您的输入为空，请输入搜索关键字后继续';
			}

			$this->load->view("search", $data);
			$footer['footer'] = $this->about_us_info->get_footer();
			$this->load->view('template/footer_not_index', $footer);

		}
		// public function auto_complete($keyword) {

		// }
	}