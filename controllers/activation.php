<?php
	/**
	 * 未完成
	 * 用于帐号的激活, 包括邮箱验证等
	 *
	 */
	class Activation extends CI_Controller {
		function __construct() {
			parent::__construct();
			$this->load->model('user_info');
			$this->load->library('header');
			$this->load->library('auth');
		}
		function _load_top() {
			$this->load->view('template/head',
							  $this->header->set_header('激活 一餐易餐'));
			$userinfo = $this->user_info->get_login();
			$this->load->view('topbar', $userinfo);
		}
		function index() {
			$code = @$_GET ['code'];
			$email = $this->auth->authcode( $code, 'DECODE', 'watermelon' );
			$user = $this->user_info->find_user_by_email ( $email );//  对数据库 查找操作，视情况而定
			$this->_load_top();
			if (!$user) {
			    $data['error'] = "非法操作";
			}
			$this->load->view('activation',$data);
			$this->load->view('template/footer_not_index');
		}
	}