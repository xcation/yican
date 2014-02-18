<?php
	/**
	 * 没有使用
	 */
	class Sms extends CI_Controller {
		public function __construct() {
			parent::__construct();
			$this->load->model('m_sms');
		}

		private function return_result($result) {
			echo json_encode(array("state"=>$result));
		}
		// 验证手机验证码
		function index() {
			header("content-type: application/json; charset=utf-8");
			if ($phone = $this->input->post('phone')) {
				if(preg_match("/^\d{11}$/",$phone)) {
					$this->return_result($this->m_sms->send_welcome($phone));
				}
				else
					$this->return_result('1'); // 错误
			}
			else
				$this->return_result('没有');
		}
	}
