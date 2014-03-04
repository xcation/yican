<?php
	class Quick_find extends CI_Model {
		var $max_recursive_time = 10; //最多递归调用的次数
		// 总共是20张，其中10张是昨日销量榜，10张随机产生
		function __construct() {
			$this->load->database();
		}
		function quick_find_food_info($university_id, $shortName) {
			
			if ($this->is_tomorrow()) {
				$this->db->cache_delete('/university', $shortName);
			}
		
			$this->db->cache_on();

			$select_option = 'foodInfo.foodId AS food_id,
							  foodInfo.foodName AS food_name,
							  foodInfo.imgLoc AS food_img_src,
							  foodInfo.total_buyer_yesterday AS daily_sale,
							  foodInfo.belongToWhichStore AS store_id,
							  price,
							  storeIntro.storeName AS storeName,
							  foodInfo.total_score_month / foodInfo.total_score_num_month 
							  AS avg_score_month';

			$q_4 = $this->db->select($select_option)
							->from('foodInfo')
							->join('storeIntro', 'foodInfo.belongToWhichStore=storeIntro.storeId')
							->join('storeLoc', 'storeLoc.storeId=storeIntro.storeId')
							->where('foodInfo.imgLoc is not null')
							->where('storeLoc.belongTo', $university_id)
							->where('storeIntro.state !=', '5')
							->order_by('avg_score_month', 'desc')
							->limit(TOP_PIC_NUM)
							->get();
			$re = array();
			$re = $q_4->result_array();

			$chosen_food = array();
			foreach($re as $val)
				$chosen_food[] = $val['food_id'];

			$this->db->cache_off();
			

			// 找到作弊的图片
			$q = $this->db->select('random_left')
					  ->get('quick_find');
			$random_left = $q->row_array()['random_left'];

			//暂时不实现cheat的功能
			/*
			$cheat_left = RANDOM_PIC_TOTAL_NUM - $random_left;
			if ( $cheat_left > 0 ) {
				$q = $this->db->select('foodId')
							 ->limit($cheat_left)
							 ->get('quick_find_cheat_food');
				foreach($q->result_array() as $row) {
					$f_id = $row['foodId'];
					$chosen_food[] = $f_id;
					$this->select_food($select_option, $f_id, $re);
				}
			}
			*/
			// 得到食物的数量
			$q = $this->db->select('foodId')
						  ->from('foodInfo')
						  ->join('storeIntro', 'storeIntro.storeId=foodInfo.belongToWhichStore')
						  ->join('storeLoc', 'storeLoc.storeId=storeIntro.storeId')
						  ->where('storeLoc.belongTo', $university_id)
						  ->where('storeIntro.state !=', '5')
						  ->where('foodInfo.imgLoc is not null')
						  ->get();
			$result_food_id = $q->result_array();

			$total_food_id = count($result_food_id);
			// var_dump($result_food_id);
			if ($total_food_id > 0) {
				for($i = 0; $i < $random_left; $i++) {
					$f_id = $this->get_not_chosen_food(0, $total_food_id - 1, $result_food_id, $chosen_food);
					$this->select_food($select_option, $f_id, $re);
				}
			}
			return $re;
		}

		// 直接根据foodid选食物信息
		function select_food($select_option, $f_id, &$re) {
			$q_5 = $this->db->select($select_option)
							 ->from('foodInfo')
							 ->join('storeIntro', 'foodInfo.belongToWhichStore=storeIntro.storeId')
							 ->where('foodId', $f_id)
							 ->get();
			if ($q_5->num_rows() > 0)
				$re[] = $q_5->row_array();
		}
		function get_not_chosen_food($time, $total, &$result_food_id, &$chosen_food) {

			$num = mt_rand(0, $total);
			if ($time == $this->max_recursive_time) // 最多调用十次
				return $result_food_id[$num]['foodId'];
			if (in_array($result_food_id[$num]['foodId'], $chosen_food))
				return $this->get_not_chosen_food(++$time, $total, $result_food_id, $chosen_food);
			$chosen_food[] = $result_food_id[$num]['foodId'];
			return $result_food_id[$num]['foodId'];
		}

		// 
		function is_next_month() {
			$q = $this->db->select('next_month_time')
						 ->where('unix_timestamp(next_month_time) < unix_timestamp(curdate())')
						 ->get('quick_find');
			// 新的一个月开始
			if ($q->num_rows() > 0) {
				$this->update_month_time();
				$this->update_food_buyer_score_month();
			}
			return false;
		}

		function is_tomorrow() {
			$q = $this->db->select('tomorrow_time')
						 ->where('unix_timestamp(tomorrow_time) < unix_timestamp(now())')
						 ->get('quick_find');
			// 新的一天开始
			if ($q->num_rows() > 0) {
				$this->update_daily_time();
				$this->update_food_buyer_daily();
				return true;
			}
			return false;
		}

		function update_food_buyer_score_month() {
			$data = array('total_buyer_month'=>0,
						  'total_score_month'=>0,
						  'total_score_num_month'=>0);
			$this->db->update('foodInfo');

			// 这是更新商户的月销量
			$data = array('total_buyer_month'=>0,
						  'total_score_month'=>0,
						  'total_score_num_month'=>0);
			$this->db->update('storeIntro');
				
		}
		function update_month_time() {
			$this->db->set('next_month_time', 'date_add(curdate()-day(curdate())+1,interval 1 month)', FALSE);
			$this->db->update('quick_find');
		}

		function update_food_buyer_daily() {
			$this->db->set('total_buyer_yesterday', 'total_buyer_today', FALSE);
			$this->db->update('foodInfo');
			$this->db->set('total_buyer_today', 0);
			$this->db->update('foodInfo');
		}
		
		function update_daily_time() {
			$this->db->set('tomorrow_time', "DATE_ADD(curdate(), INTERVAL 1 DAY)", FALSE);
			$this->db->update('quick_find');
		}
	}