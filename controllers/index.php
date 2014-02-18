<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Index extends CI_Controller {

		public function __construct()
        {
            parent::__construct();
            $this->load->library('session');
            $this->load->library('header');
            $this->load->model('user_info');
			$this->load->model('homepageschoolinfo');
			$this->NU_REGION = '2';
        }

		public function index() {
			if ($loc = $this->input->cookie('location', TRUE)) {
				header("location: university/$loc");
			}
			else
				$this->showLoc();
		}

		function showLoc() {
			//  title, keyword, description

			$this->load->view('template/head',
							  $this->header->set_header('想叫外卖 一餐易餐'));

			// $school['schoolInfo'] = $this->homepageschoolinfo->get_univ_info();
			$q = $this->db->select('region_id, imgLoc')
						  ->get('region');
			$school = array();
			$region['region_info'] = array();
			foreach ($q->result_array() as $row) {
				if ($row['region_id'] == $this->NU_REGION) {
					$q_1 = $this->db->select("schoolShortName AS univ_short_name,
										      imgLoc")
							   		->where('imgLoc is not null')
							   		->where('region_code', $row['region_id'])
							   		->get('schoolInfo');
					$school = $q_1->result_array();
				} else if ($row['region_id'] != '1') {
					$school[] = array('imgLoc'=>$row['imgLoc'],
					                  'region_input'=>1,
					                  'region_id'=>$row['region_id']);
					$q_2 = $this->db->select('schoolShortName AS univ_short_name,
					                          schoolFullName as univ_full_name,
					                   		  imgLoc')
									->where('region_code', $row['region_id'])
							   		->where('imgLoc is not null')
									->get('schoolInfo');
					$q_2_res['region_detail'] = $q_2->result_array();
					$q_2_res['region_id'] = $row['region_id'];
					$region['region_info'][] = $q_2_res;
				}
			}
			// var_dump($school);
			$region['schoolInfo'] = $school;
			$data = $this->user_info->get_login();
			$data['in_step'] = true;
			$data['step_one'] = "active";
			$data['search_not_avai'] = true;
			$this->load->view('topbar', $data);
			$this->load->view('homepage', $region);
			$this->load->view('/template/footer');
		}

	}
?>