<?php
	class search_info extends CI_Model {
		
		function __construct() {
			$ci =& get_instance();
			$ci->load->model('quick_find');
			$ci->load->model('store_info');
			$this->load->library('get_db_info');
			$this->load->library('state_name');
			$this->load->database();

		}
		function key($univ, $key) {
			$this->quick_find->is_tomorrow();
			$this->quick_find->is_next_month();
			$this->store_info->update_shanghu_state_one_univ($univ);
			$q = $this->db->select("storeIntro.storeId AS store_id,
								    storeIntro.storeName AS store_name,
								    state,
								    total_buyer_month,
								    (total_score_month / total_score_num_month) AS avg_score_month,
								    storeLoc.delivery_cost AS delivery_cost")
						  ->from('storeIntro')
						  ->join('storeLoc', 'storeIntro.storeId = storeLoc.storeId')
						  ->where('storeLoc.belongTo', $univ)
						  ->where('storeIntro.state !=', '5')
						  ->like('storeName', "{$key}")
						  ->order_by('total_buyer_month', 'desc')
						  ->order_by('avg_score_month', 'desc')
						  ->limit(5)
						  ->get();
			$re['restaurant'] = null;
			if ($q->num_rows() > 0) {
				$re['restaurant'] = $q->result_array();
				foreach($re['restaurant'] as &$row) {
					$row['state'] = $this->state_name->get_state_name($row['state']);
				}

			}
			$re['food'] = null;
			$q = $this->db->select('foodId as food_id,
								  foodName as food_name,
								  belongToWhichStore as store_id,
								  storeName as store_name,
								  price,
								  foodInfo.total_buyer_month,
								  foodInfo.total_score_month / foodInfo.total_score_num_month AS avg_score_month')
						->from('foodInfo')
						->join('storeIntro', 'foodInfo.belongToWhichStore=storeIntro.storeId')
						->like('foodName', "{$key}")
						->where('isAvailable', '1')
						->where('deleted', '0')
						->where('storeIntro.state !=', '5')
						->order_by('store_id', 'desc')
						->order_by('avg_score_month', 'desc')
						->get();
			if ($q->num_rows() > 0) {
				$re['food'] = $q->result_array();
			}
			return $re;
		}
	}