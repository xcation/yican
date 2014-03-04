<?php
class Login extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model("user_info");
		$this->load->library("get_db_info");
	}
	
	public function index() {
		$user = $this->input->post('user');
		$passwd = $this->input->post('passwd');
		
		$data['hint']="";
		if( !$user || !$passwd ){
			$this->load->view('templates/header');
			$this->load->view('login/login_body', $data);
			$this->load->view('templates/footer');
		}
		else{
			if (!$this->user_info->user_login($user, $passwd)) {
				$data['hint']="对不起！用户名/密码错误";
				$this->load->view('templates/header');
				$this->load->view('login/login_body', $data);
				$this->load->view('templates/footer');
			}
			else {
				$cookie = array(
					'name' => 'user',
					'value' => $user,
					'expire' => '2592000',
					'prefix' => 'mycyc_'
				);
				$this->input->set_cookie($cookie);

				header("location: ".constant('mycycbase'));
			}
		}
	}
	
}

?>