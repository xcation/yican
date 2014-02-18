<?php
	class Login extends CI_Controller {
		public function __construct() {
			parent::__construct();
			$this->load->helper('cookie');
			$this->load->model('user_info');
			$this->load->library('session');
			$this->load->library('header');
			$this->load->model('about_us_info');
		}

		private function _load_top() {
			$this->load->view('template/head', 
							  $this->header->set_header('登录 一餐易餐'));
			$userinfo = $this->user_info->get_login();
			$this->load->view('topbar', $userinfo);
			// $this->user_info->user_info;
			// $this->load->view('topbar_cart', $userInfo);
		}

		private function y_set_cookie($cookie_name, $cookie_value) {
			$cookie = array(
				    'name'   => $cookie_name,
				    'value'  => $cookie_value,
				    'expire' => COOKIE_TIME,
				    'path'   => '/',
				);
			$this->input->set_cookie($cookie);
		}
		private function m_set_cookie($stay_login, $cookie_name, $cookie_value) {
			if (@$stay_login) {
				$this->y_set_cookie($cookie_name, $cookie_value);
			}
		}
		function index($register = 0) {
			$post = $this->input->post(NULL, TRUE);
			$this->_load_top();
			$data = array();
			if ($post) {
				if (!$this->user_info->user_login($post['login_id'], $post['passwd'])) {
					$re = $this->user_info->store_login($post['login_id'], $post['passwd']);
					if (!$re['valid']) {
						$data['error'] = '用户名或密码错误';
					}
					else {//成功
						// var_dump($post);
						$this->m_set_cookie(@$post['stay_login'], 'storeid', $post['login_id']);
						// echo $re['store_id'];
						header('location: shanghu/'.$re['store_id']);
						return;
					}
				}
				else {
					$this->m_set_cookie(@$post['stay_login'], 'userid', $post['login_id']);
					header("location: /");
					return;
				}
			}
			$data['register'] = $register;
			$this->load->view('login', $data);
			$footer['footer'] = $this->about_us_info->get_footer();
			$this->load->view('template/footer_not_index', $footer);
		}
		function register() {
			$post = $this->input->post(NULL, TRUE);
			$this->_load_top();
			if ($post) {
				$msg = $this->user_info->insert_user($post);
				if ($msg['valid']) {
					$this->y_set_cookie('userid',$post['reg_login_id']);
				}
				$this->load->view('register_success', $msg);
			}
			else {
				$msg['register'] = 1;
				$this->load->view('login', $msg);
			}
		}

		function check_phone() {
			header("content-type: application/json; charset=utf-8");
			$result = false;
			if ($phone = $this->input->post('reg_login_id', TRUE)) {
				if ($this->user_info->check_phone($phone))
					$result = true;
			}
			echo json_encode($result);
		}
		function check_userid() {
			header("content-type: application/json; charset=utf-8");
			$result = false;
			if ($login_id = $this->input->post('reg_login_id', TRUE)) {
				if ($this->user_info->check_user_id($login_id))
					$result = true;
			}
			echo json_encode($result);
		}
		function check_email() {
			header("content-type: application/json; charset=utf-8");
			$result = false;
			if ($email = $this->input->post('reg_email', TRUE)) {
				if ($this->user_info->check_email($email))
					$result = true;
			}
			echo json_encode($result);
		}
		
		function logout() {
			delete_cookie('userid');
			$this->session->unset_userdata('userid');
			header('location: /');
		}
	}

	