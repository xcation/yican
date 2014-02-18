<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * about界面
	 *
	 * @deprecated
	 * @see 			 controller/notice
	 * @version 		 1.0
	 */
	class About extends CI_Controller {

		public function __construct()
        {
            parent::__construct();
   //          $this->load->library('session');
            $this->load->library('header');
   //          $this->load->model('user_info');
			// $this->load->model('homepageschoolinfo');

        }

		public function index() {
			$this->contact_us();
		}
		public function contact() {
			$this->load->view('template/head',
							  $this->header->set_header('关于一餐易餐 联系方式'));
			$data['search_not_avai'] = true;
			$this->load->view('topbar', $data);
			$about['contact'] = true;
			$this->load->view('about/service', $about);

			$this->load->view('template/footer_not_index');

		}
		public function service() {
			$this->load->view('template/head',
							  $this->header->set_header('关于一餐易餐 服务条款'));
			$data['search_not_avai'] = true;
			$this->load->view('topbar', $data);
			$about['service'] = true;
			$this->load->view('about/service', $about);
			$this->load->view('template/footer_not_index');

		}

	}
?>
