<?php
	class Management_info extends CI_Model {
		public function __construct() {
			$ci =& get_instance();
			$ci->load->model('quick_find');
			$this->load->database();
			$this->load->library('session');
		}
		private function _query_sale_id(&$sale_info) {
			if (count($sale_info) == 0)
				return;
			foreach ($sale_info as &$sale) {
				$sale['food_one_sale'] = array();
				$q_2 = $this->db->select('multiFoodSaleInfo.foodId AS foodId, 
										  multiFoodSaleInfo.price AS price, 
										  num,
										  foodInfo.foodName AS foodName,
									      score AS taste_score, 
									      score_comment AS taste_comment,
									      scoreTime')
								->from('multiFoodSaleInfo')
								->join('foodInfo', 'foodInfo.foodId=multiFoodSaleInfo.foodId')
								->where('multiFoodSaleInfo.saleId', $sale['saleId'])
								->get();
				$sale['food_one_sale'] = $q_2->result_array();
			}		
		}
		public function order($login, $user_id) {
			$sale_info = array();
			$sale_id = array();
			$select_option = "saleId, eachFoodSaleInfo.storeId AS storeId, 
						    user_addr, user_l_tel,
						    user_s_tel, taste,
						    university_id,
						    eachFoodSaleInfo.createTime AS createTime, 
						    storeIntro.storeName AS storeName,
						    storeIntro.contact_phone AS contact_phone,
						    score,
						    validity,
						    judgement AS delivery_comment";
			if (!$login) {
				$cookie_sale_info = $this->input->cookie('mycyc_history_order', TRUE);
				if ($cookie_sale_info) {
					$cookie_sale_info = json_decode($cookie_sale_info, TRUE);
					$num = count($cookie_sale_info);
					for ($i = $num - 1; $i >= 0; --$i) {
						$val = $cookie_sale_info[$i];
						$q = $this->db->select($select_option)
								  ->from('eachFoodSaleInfo')
								  ->join('storeIntro', 'eachFoodSaleInfo.storeId=storeIntro.storeId')
								  ->where('saleId', $val)
								  ->order_by('eachFoodSaleInfo.createTime', 'desc')
								  ->get();
						if ($q->num_rows() > 0)
							$sale_info[] = $q->row_array();
					}
				}
				else
					$sale_info['state'] = false;
			}
			else {
				// 所有的订单
				$q = $this->db->select($select_option)
							  ->from('eachFoodSaleInfo')
							  ->join('storeIntro', 'eachFoodSaleInfo.storeId=storeIntro.storeId')
							  ->where("unix_timestamp(eachFoodSaleInfo.createTime) >=", "unix_timestamp(concat(date_format(LAST_DAY(now()),'%Y-%m-'),'01'))")
							  ->where('buyerId', $user_id)
							  ->order_by('eachFoodSaleInfo.createTime', 'desc')
							  ->get();
				if ($q->num_rows() > 0)
					$sale_info = $q->result_array();
				
			}
			if (!isset($sale_info['state'])) 
				$this->_query_sale_id($sale_info);
			return $sale_info;
		}
		function store_comment($store_id, $sale_id, $score, $comment) {
			$this->db->where('saleId', $sale_id);
			$data = array(
						"judgeTime"=>date('Y-m-d H:i:s'),
						"score"=>$score,
						"judgement"=>$comment
					);
			$this->db->update('eachFoodSaleInfo', $data);

			$this->quick_find->is_next_month();
			$this->update_score('storeId', $store_id, 'storeIntro', $score);
		}
		function food_comment($food_id, $sale_id, $score, $comment) {
			$this->db->where('saleId', $sale_id);
			$this->db->where('foodId', $food_id);
			$data = array(
						"scoreTime"=>date('Y-m-d H:i:s'),
						"score"=>$score,
						"score_comment"=>$comment
					);
			var_dump($data);
			$this->db->update('multiFoodSaleInfo', $data);

			$this->quick_find->is_next_month();
			$this->update_score('foodId', $food_id, 'foodInfo', $score);
		}
		function update_score($column, $value, $table_name, $score) {
			$this->db->set('total_score_num_month', "total_score_num_month + 1", FALSE);
			$this->db->set('total_score_month', "total_score_month + {$score}", FALSE);
			$this->db->where($column, $value);

			$this->db->update($table_name);
		}
	}
