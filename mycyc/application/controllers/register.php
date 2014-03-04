<?php
	class Register extends CI_Controller {
		public function __construct() {
			parent::__construct();
			$this->load->helper('cookie');
			$this->load->model('user_info');
			$this->load->library('session');
		}

		function index() {
			$post = $this->input->post(NULL, TRUE);
			$this->load->view('templates/header');
			if ($post) {
				$msg = $this->user_info->insert_user($post);
				if ($msg['valid']) {
					$cookie = array(
						'name' => 'user',
						'value' => $post['reg_login_id'],
						'expire' => '2592000',
						'prefix' => 'mycyc_'
					);
					$this->input->set_cookie($cookie);
				}
				$this->load->view('login/register_success', $msg);
			}
			else 
				$this->load->view('login/register');
			$this->load->view('templates/footer');

		}
	}