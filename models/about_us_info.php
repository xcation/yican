<?php
	class About_us_info extends CI_Model {
		public function __construct() {
			$this->load->database();
		}

		function get_announce() {
			$q = $this->db->select('announce_id,
								    announce_content,
								    createTime')
					      ->get('announcement');
			return $q->result_array(); 
		}

		function get_footer() {
			$q = $this->db->select('orders, label_name, link_href')
						  ->order_by('orders', 'asc')
						  ->get('footer_note');
			return $q->result_array();
		}
	}
	
