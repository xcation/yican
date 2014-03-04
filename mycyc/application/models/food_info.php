<?php
	class Food_info extends CI_Model {
		var $re;
		public function __construct() {
			$this->load->database();
		}
		
		function get_store_deliver($store_id) {
			$this->db->select('storeName, telephone, telephone_1, telephone_2');
			$this->db->where('storeId', $store_id);
			$query = $this->db->get('storeIntro');
			if ($query->num_rows() > 0)
				return $query->row_array();
		}
		function get_food_name_price($food_id) {
			// var_dump($food_id);
			$q = $this->db->select('foodName, price')
						  ->where('foodId', $food_id)
						  ->get('foodInfo');
			if ($q->num_rows() > 0)
				return $q->row_array();
			else
				return false;
		}
		function get_food_type($store_id) {
			$query = $this->db->select('foodTypeName, foodTypeId')
							  ->where('storeId', $store_id)
							  ->where('deleted', '0')
							  ->get('foodTypeInEachStore');
			return $query->result_array();
		}
		
		
		function get_food_info($store_id) {
			$food_type_id = $this->get_food_type($store_id);
			$food_info = array();
			$this->re = array();
			$select_option = "foodId, foodName, imgLoc, 
								price, 
								special, note, total_buyer_month, 
								total_score_month / total_score_num_month AS avg_score";
			foreach($food_type_id as $val) {
				$m_food_type_id = $val['foodTypeId'];

				$no_img_query = $this->db->select($select_option)
									  ->where('belongToWhichStore', $store_id)
									  ->where('foodTypeId', $m_food_type_id)
									  ->where('isAvailable', '1')
									  ->where('deleted', '0')
									  ->order_by('avg_score', 'desc')
						   			  ->get('foodInfo');

				if ($no_img_query->num_rows() > 0) {
					$food_info["{$m_food_type_id}"] = array();
					$food_info["{$m_food_type_id}"]['no_img'] = $no_img_query->result_array();
				
					$this->re[] = array("foodTypeName"=>$val['foodTypeName'],
								      "foodTypeId"=>$val['foodTypeId']);
				}
				
			}
			// var_dump($food_info);
			return $food_info;
		}

	}
