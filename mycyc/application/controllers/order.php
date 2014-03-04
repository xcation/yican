<?php
class Order extends CI_Controller{

	function __construct(){
		parent::__construct();
		$this->load->model("food_info");
		$this->load->model('user_info');
		$this->load->model("management_info");
		$this->load->library("get_db_info");
	}
	
	public function index() {
		$user_info = $this->user_info->get_login();
		$data['login'] = $user_info['login'];
		$data['food_order'] = $this->management_info->order($user_info['login'], @$user_info['userName']);
		$this->load->view('templates/header');
		$this->load->view('order/order', $data);
		$this->load->view('templates/footer');
	}
	
}
?>