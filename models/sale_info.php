<?php
	class Sale_info extends CI_Model {
		function __construct() {
			$this->load->database();
			$this->load->library('time');
		}
		function update_pos_send($sale_id) {
			$this->db->where('saleId', $sale_id);
			$this->db->set('pos_send', '1');
			$this->db->set('validity', '1');
			$this->db->set('pos_send_time', date('Y-m-d H:i:s', time()));
			$this->db->update('eachFoodSaleInfo');
		}
	}