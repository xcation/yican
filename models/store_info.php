<?php
	class Store_info extends CI_Model {
		public function __construct() {
			$this->load->database();
			$this->load->library('get_db_info');
			$this->load->library('state_name');
			$this->load->model('quick_find');
			$this->load->model('m_sms');
		}
		private function query_store_info($univ_id, &$row){
				$store_id = $row['storeId'];
				$query_2 = $this->db->query("SELECT storeTypeName
								  FROM storeType 
								  JOIN eachStoreType
								  ON storeType.storeTypeId=eachStoreType.storeTypeId
								  WHERE eachStoreType.storeId=$store_id");
				$count = 0;
				$each_store_type = "";

				foreach ($query_2->result() as $row_2) {
					if ($count++)
						$each_store_type .= ',' . $row_2->storeTypeName;
					else
						$each_store_type = $row_2->storeTypeName;
				}
				$row['each_store_type'] = $each_store_type;
				$row['state_choise'] = 'closed';

				if ($this->state_name->is_work_state($row['state']))
					$row['state_choise'] = 'open';
				$row['state'] = $this->state_name->get_state_name($row['state']);

				$q_3 = $this->db->select('delivery_cost')
								->where('storeId', $store_id)
								->where('belongTo', $univ_id)
								->get('storeLoc');
				$row['delivery_cost'] = $q_3->row_array()['delivery_cost'];
		}

		function get_store_top_info($university_id, $store_id) {
			$this->update_shanghu_state_one_univ($university_id);
			$q = $this->db->select('gonggao, location, briefIntroduction, 
									yinyeshijian, qisongjia, imgLoc')
						  ->from('storeIntro')
						  ->join('storeLoc', 'storeLoc.storeId=storeIntro.storeId')
						  ->where('storeIntro.storeId', $store_id)
						  ->where('storeLoc.belongTo', $university_id)
						  // 状态5表示退出ycyc
						  ->where('storeIntro.state !=', '5')
						  ->get();
			if ($q->num_rows() > 0)
				return $q->row_array();
		}

		function get_block() {
			$q =$this->db->select('block_num, block_name')
				   	     ->get('delivery_order');
			return $q->result_array();
		}
		function get_store_info($university_id) {
			// 先更新商户状态
			$this->update_shanghu_state_one_univ($university_id);

			$block = $this->get_block();
			$store_info = array();

			foreach ($block as $one_block) {
				$sql = "SELECT storeIntro.storeId, storeName, state, 
						total_buyer_month, 
						ROUND(total_score_month / total_score_num_month, 1) AS avg_score_month,
						imgLoc 
						FROM storeIntro
						JOIN storeLoc
						ON storeIntro.storeId=storeLoc.storeId
						WHERE storeLoc.belongTo=$university_id
						AND storeIntro.state!='5'
						AND (storeIntro.delivery_order & {$one_block['block_num']}) != 0
						ORDER BY storeIntro.state asc, store_order desc, total_buyer_month desc, avg_score_month desc";

				$store_info[$one_block['block_num']] = array();
				$query = $this->db->query($sql);
				foreach ($query->result_array() as $row) {
					$this->query_store_info($university_id, $row);
					$store_info[$one_block['block_num']][] = $row;
				}
			}
			// var_dump($store_info);
			return $store_info;
		}
		function get_store_type() {
			$this->db->select('storeTypeName');
			$this->db->select('storeTypeId');
			$query = $this->db->get('storeType');
			$store_type = array();
			foreach ($query->result_array() as $row)
				$store_type[] = $row;
			return $store_type;
		}

		////////////////////////////////////////////
		// 商户的状态
		function get_shanghu_state($store_id) {

			$now_state = $this->get_db_info->get_shanghu_now_state_with_id($store_id);
			if ($now_state == '4' || $now_state == '5') { // 休假状态
				return $now_state;
			}

			$q = $this->db->select('storeId')
						  ->where('unix_timestamp(user_in_charge_time) 
						  		 < unix_timestamp(curdate())')
						  ->where('storeId', $store_id)
						  ->get('storeIntro');
			// 有结果说明之前修改状态					  
			if ($q->num_rows() == 0) {
				// 系统会修改用户状态，太忙啊
				return $now_state;
			}
			// 使用默认的时间
			else {
				// ->where('curtime() BETWEEN openTime_1 AND closeTime_1')
				// 			  ->or_where('curtime() BETWEEN openTime_2 AND closeTime_2')
				// 			  ->where('storeId', $store_id)
				$q = $this->db->select('storeId, openTime_1, closeTime_1, openTime_2, closeTime_2')
						      ->where('storeId', $store_id)
							  ->get('storeIntro');
				$time = $q->row_array();
				$open_time_1 = strtotime($time['openTime_1']);
				$open_time_2 = strtotime($time['openTime_2']);
				$close_time_1 = strtotime($time['closeTime_1']);
				$close_time_2 = strtotime($time['closeTime_2']);

				$open = false;
				$curtime = strtotime(date('H:i:s'));

				if ($open_time_1 > $close_time_1) {
					if ($curtime >= $open_time_1 || $curtime <= $close_time_1)
						$open = true;
				}
				if ($open_time_2 > $close_time_2) {
					if ($curtime >= $open_time_2 || $curtime <= $close_time_2)
						$open = true;
				}
					// $close_time_2 = strtotime('+1 day', $close_time_2);

				if ( ( ($curtime >= $open_time_1) && ($curtime <= $close_time_1) ) ||
					 	  ( ($curtime >= $open_time_2) && ($curtime <= $close_time_2) ) ) {
							$open = true;
				}
				// 最初的默认设置为已打烊
				// if ($store_id == '3') {
				// 	echo $time['closeTime_2'];
				// 	var_dump($open_time_1);
				// 	var_dump($open_time_2);
				// 	var_dump($close_time_1);
				// 	var_dump($close_time_2);
				// 	var_dump($curtime);
				// var_dump($open);
				// }
				if ($open) {
					$q = $this->db->select('storeId')
								 ->where('unix_timestamp(now()) - unix_timestamp(pos_last_time) >', 10 * 60)
								 ->where('storeId', $store_id)
								 ->where('pos_error_send', '0')
								 ->get('storeIntro');
					if ($q->num_rows() > 0) {
						$this->m_sms->send_pos_error($store_id);
						$this->db->where('storeId', $store_id);
						$this->db->set('pos_error_send', '1');
						$this->db->update('storeIntro');
						$open = false;
					}
				}
				if ($open) {
					$this->set_state($store_id, '0');
					return '0'; // 返回工作中状态else {
				}
				else {
					$this->set_state($store_id, '2');
					return '2'; // 已打烊
				}
			}
		}

		function update_shanghu_state_one_univ($univ_id) {
			$q = $this->db->select('storeId')
						  ->where('belongTo', $univ_id)
						  ->get('storeLoc');
			foreach($q->result_array() as $row) {
				$this->get_shanghu_state($row['storeId']);
			}
			
		}

		function set_state($store_id, $state) {
			$this->db->where('storeId', $store_id);
			$this->db->update('storeIntro', array('state'=>$state));
		}
		///////////////////////////////////////////////////////

		function add_column($column, &$q) {
			if ($column)
				$q->where($column[0], $column[1]);
		}
		function ajax_get_store_info($order_type, $university_id, $open, $taste, $cost, $order_type) {
			$store_info = array();

			$this->update_shanghu_state_one_univ($university_id);
			
			$state = false;
			$delivery_cost = false;
			$store_type = false;
			$store_order_type = false;
			if ($open == '1') {
				$state = array();
				$state[] = "state = ";
				$state[] = '0';
			}
			if ($cost != 0) {
				$delivery_cost = array();

				$delivery_cost[] = "delivery_cost <=";
				$delivery_cost[] = $cost;
			}
			if ($taste > 0) {
				$store_type = array();
				$store_type[] = "storeTypeId =";
				$store_type[] = $taste;
			}
			if ($order_type != '2') {
				$store_order_type = array();

				$store_order_type[] = "delivery_order =";
				$store_order_type[] = "{$order_type}";
			}

			
			$q = $this->db->select('storeIntro.storeId AS storeId, 
								  storeName, state, location, 
								  briefIntroduction, qisongjia, yinyeshijian, 
								  gonggao, 
								  imgLoc,
								  total_buyer_month, total_score_month / total_score_num_month AS avg_score_month')
						  ->from('storeIntro')
						  ->where('delivery_order', $order_type)
						  ->where('state !=', '5')
						  ->join('storeLoc', 'storeLoc.storeId=storeIntro.storeId');
			if ($taste)
				$q->join('eachStoreType', 'eachStoreType.storeId=storeIntro.storeId');

			$q->where('storeLoc.belongTo', $university_id);

			$this->add_column($state, $q);
			$this->add_column($delivery_cost, $q);
			$this->add_column($store_type, $q);
			$this->add_column($store_order_type, $q);
			$q = $q->order_by('total_buyer_month', 'desc')->get();
			// var_dump($q->result_array());
			
			$store_info = array();
			$store_info[$order_type] = array();
			foreach ($q->result_array() as $row) {
				$this->query_store_info($university_id, $row);
				$store_info[$order_type][] = $row;
			}
			return $store_info;
		}
		function get_delivery_cost($university_id, $store_id)  {
			$q = $this->db->select('delivery_cost')
						  ->where('belongTo', $university_id)
						  ->where('storeId', $store_id)
						  ->get('storeLoc');
			if ($q->num_rows() > 0)
				return $q->row_array()['delivery_cost'];
		}

		function change_passwd($store_id, $passwd_1) {
			$this->db->set('loginPasswd', md5($passwd_1));
			$this->db->where('storeId', $store_id);
			$this->db->update('storeIntro');
		}

		function update_store_sale_order($sale_id, $store_id, $a) {
			$this->db->where('saleId', $sale_id);
			$this->db->update('eachFoodSaleInfo', 
							  array("validity"=>'1')); 
			$this->quick_find->is_tomorrow();
			$this->quick_find->is_next_month();
			$in_store_food = 0;
			foreach ($a as $val) {
				foreach ($val as $vval) {
					$f_num = $vval['food_num'];
					$in_store_food += $f_num;
					$this->db->set('total_buyer_month', "total_buyer_month + {$f_num}", FALSE);
					$this->db->set('total_buyer_today', "total_buyer_today + {$f_num}", FALSE);
					$this->db->where('foodId', $vval['food_id']);
					$this->db->update('foodInfo');
				}
			}
			$this->db->set('total_buyer_month', "total_buyer_month + {$in_store_food}", FALSE);
			$this->db->where('storeId', $store_id);
			$this->db->update('storeIntro');
		}
	}
