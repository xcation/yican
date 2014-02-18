<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * 接受用户POS的请求
	 */
	class Receive extends CI_Controller {
		public function __construct()
        {
            parent::__construct();
            $this->load->model('user_info');
        }

		public function index() {
			$phone = $_GET['m'];
			$content = $_GET['c'];
			if ($phone && $content) {
				$data = array('phone'=>$phone,
							  'content'=>$content,
							  'time'=>date('Y-m-d H:i:s', time()));
				$this->db->insert('sms_receive', $data);
			}
		}
	}
?>