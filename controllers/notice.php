<?php
	class Notice extends CI_Controller {
		public function __construct() {
			parent::__construct();
			$this->load->model('user_info');
			$this->load->library('header');
			$this->load->model('about_us_info');
		}

		private function _load_top() {
			$userInfo = array();
			$head_info = $this->header->set_header("叫外卖，上一餐易餐");
			$this->load->view('template/head', $head_info);

			$userInfo = $this->user_info->get_login();
			$userInfo['in_step'] = true; // ??ʾ?ڶ??͹?????
			$userInfo['step_two'] = "active"; // ???͵ڶ???
			$this->load->view('topbar', $userInfo);
		}
		public function index($date) {
			$footer['footer'] = $this->about_us_info->get_footer();
			$fp = fopen(LOC_PREFIC."/text/".$date, "r");
			if (NULL == $fp) {
				echo "error page";
				return;
			}
			$content = "";
			while (!feof($fp))
				$content .= fread($fp, 512);
			fclose($fp);
			$this->_load_top();
			$data['content'] = $content;
			$this->load->view('notice',$data);
			$this->load->view('template/footer_not_index', $footer);
		}
	}