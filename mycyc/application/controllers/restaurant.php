<?php 

class Restaurant extends CI_Controller{
	
	public function __construct() {
		parent::__construct();
		//$this->load->library('session');
		$this->load->helper('url');
		$this->load->model('store_info');
		$this->load->library('get_db_info');
		$this->load->library('pagination');
	}
	
	public function index($shortName, $block_num = 1){
		$taste=$this->input->get("taste");
		if(!$taste)
			$taste=0;
		$data['taste']=$taste;
		
		$university_id = $this->get_db_info->get_university_id_with_short_name($shortName);
		if (!$university_id) {
				header("Location: ".constant("mycycbase"));
				return;
		}
		
		//$data['store_info'] = $this->store_info->get_store_info($university_id);
		$data['store_type'] = $this->store_info->get_store_type();
		$data['college'] = $this->get_db_info->get_univeristy_full_with_short_name($shortName);
		$data['pageName'] = $shortName;
		$data['university_id'] = $university_id;
		 
		//pagination
		$config['base_url']=constant("mycycbase")."/restaurant/".$shortName."/{$block_num}";
		$config['total_rows']=$this->store_info->count_store_num($university_id, $block_num, $taste);
		$config['per_page']=10;
		$config['uri_segment']=4;
		$config['suffix']="?taste=$taste";
		$config['first_url']=$config['base_url']."/".$config['suffix'];
		$this->pagination->initialize($config);
		
		$page=($this->uri->segment(4))?$this->uri->segment(4):0;
		$data['store_info']=$this->store_info->get_store_info_limit($university_id, $block_num, $taste, $config['per_page'], $page);
		$data['links']=$this->pagination->create_links();
		
		$data['short_name'] = $shortName;
		$data['block_info'] = $this->store_info->get_block();
		$this->load->view('templates/header', $data);
		$this->load->view('restaurant/list', $data);
		$this->load->view('templates/footer', $data);
	}

	public function ajax_get_store_info($university_id, $block_num, $taste) {
		$data['store_info'] = $this->store_info->ajax_get_store_info($university_id, $block_num, $taste);
		$data['university_id'] = $university_id;
		$data['links'] ="";
		$this->load->view('restaurant/restaurant_body', $data);
	}
}

?>