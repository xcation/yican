<?php
	class Shanghu_info extends CI_Model {
		function __construct() {
			$this->load->database();
			$this->load->library('time');
		}
		function food_type($store_id) {
			$type_order = $this->get_food_type_order($store_id);
			$data = array();
			foreach ($type_order as $row) {
				$food_type_id = $row['food_type_id'];
				$query_2 = $this->db->select('foodId, foodName, imgLoc, isAvailable, price, special, note')
								 ->where('belongToWhichStore', $store_id)
								 ->where('foodTypeId', $food_type_id)
								 ->where('deleted', '0')
								 ->order_by('isAvailable', "desc")
								 ->order_by('food_arrange_order', 'desc')
								 ->get('foodInfo');
				$food_in = array();							 
				foreach ($query_2->result_array() as $row_2) 
					$food_in[] = $row_2;

				$data[] = array(
							"food_type_id"=>$food_type_id,
							"food_type_name"=>$row['food_type_name'],
							"food_in"=>$food_in
						  );
			}
			return $data;
		}
		function add_food_img($id, $pics) {
			$data = array('imgLoc'=>$pics);
			$this->db->where('foodId', $id);
			$this->db->update('foodInfo',$data);
		}
		function add_dish($store_id, $food_type, $name, $price, $note, $avail) {
			$data = array(
						"foodName"=>$name,
						"belongToWhichStore"=>$store_id,
						"foodTypeId"=>$food_type,
						"price"=>$price,
						"note"=>$note,
						"isAvailable"=>$avail
					);
			// echo var_dump($data);
			$this->db->insert('foodInfo', $data);
			return $this->db->insert_id();
		}
		function add_food_type($store_id, $val) {
			$data = array(
						"storeId"=>$store_id,
						"foodTypeName"=>$val
					);
			$this->db->insert('foodTypeInEachStore', $data);
			return $this->db->insert_id();
		}
		function check_new_sale($store_id) {
			$q = $this->db->select('saleId, user_addr, user_l_tel, user_s_tel, taste')
						 ->where('storeId', $store_id)
						 ->where(array('confirmTime' => NULL))
						 ->where('validity', '1')
						 ->get('eachFoodSaleInfo');
			$num = $q->num_rows();
			$state = 0;
			if ($num > 0)
				$state = 1;
			$sale_info = $q->result_array();
			foreach($sale_info as &$one_sale) {
				$q_2 = $this->db->select('multiFoodSaleInfo.foodId AS foodId, 
								   foodName, 
								   multiFoodSaleInfo.price AS price, 
								   num')
								 ->from('multiFoodSaleInfo')
								 ->join('foodInfo', 'foodInfo.foodId=multiFoodSaleInfo.foodId')
								 ->where('saleId', $one_sale['saleId'])
								 ->get();
				$one_sale['one_sale'] = $q_2->result_array();
			}
			$h = "";
			foreach ($sale_info as $sales) {
				$h .= "<div class='one_sale'>".
						"<div class='sale_info_title'>".
							"订单号：<span>{$sales['saleId']}</span>&nbsp;".
							"送货地址：<span>{$sales['user_addr']}</span>&nbsp;".
							"联系电话：<span>{$sales['user_l_tel']}&nbsp;{$sales['user_s_tel']}</span>".
						"</div>";
				$sum = 0;
				foreach($sales['one_sale'] as $food) {
					$sum += $food['price'] * $food['num'];
					$h .= "<div class='sale_info_food'>".
						   		"<span>{$food['foodName']}</span>".
						   		"<span>（￥{$food['price']}）</span>".
						   		"<span>x{$food['num']}</span>".
						   "</div>";
				}
				$h.=	"<div>总计：{$sum}".
							"<span class='confirm_received'>".
								"<button class='received' sale='{$sales['saleId']}'>".
									"确认收到订单".
								"</button>".
							"</span>".
							"<span class='r_loading'>".
								"<img src=''/>".
							"</span>".
							"<span class='warning'>".
							"</span>".
						"</div>".
					"</div>";
			}
			// echo $h;
			header("content-type: application/json; charset=utf-8");
			return json_encode(array(
									"state"=>$state,
									"num"=>$num,
									"html"=>$h));
		}

		// 
		function get_all_store_info($store_id) {
			$q = $this->db->select('storeName AS store_name,
									storeId AS store_id,
								    location AS store_loc,
								    imgLoc AS store_img,
								    telephone AS store_tel_1,
								    telephone_1 AS store_tel_2,
								    telephone_2 AS store_tel_3,
								    openTime_1,
								    closeTime_1,
								    openTime_2,
								    closeTime_2,
								    max_order,
								    delivery_order AS order_type,
								    briefIntroduction AS brief_intro,
								    qisongjia AS deliver_note,
								    yinyeshijian AS open_time_note,
								    gonggao,
								    contact_phone AS contact_phone_note
								    ')
						  ->where('storeId', $store_id)
						  ->get('storeIntro');
			// echo $openTime_1;
			if ($q->num_rows() > 0) {
				$data['store_info']	= $q->row_array();
				$s_1 = explode(':', $data['store_info']['openTime_1']);
				$s_2 = explode(':', $data['store_info']['openTime_2']);
				$e_1 = explode(':', $data['store_info']['closeTime_1']);
				$e_2 = explode(':', $data['store_info']['closeTime_2']);
				$data['store_info']['start_hour'] = array(@$s_1[0], @$s_2[0]);
				$data['store_info']['start_minite'] = array(@$s_1[1], @$s_2[1]);
				$data['store_info']['end_hour'] = array(@$e_1[0], @$e_2[0]);
				$data['store_info']['end_minite'] = array(@$e_1[1], @$e_2[1]);
			}
			$q_2 = $this->db->select('schoolId AS schoolId,
									  schoolFullName AS schoolFullName')
							->get('schoolInfo');
			$all_univ = $q_2->result_array();

			$q_3 = $this->db->select('belongTo AS schoolId,
									  delivery_cost')
							->where('storeId', $store_id)
							->get('storeLoc');
			$store_univ = $q_3->result_array();

			foreach($all_univ as &$univ) {
				$univ['checked'] = false;
				foreach($store_univ as $m_univ) {
					if ($univ['schoolId'] == $m_univ['schoolId']) {
						$univ['checked'] = true;
						$univ['delivery_cost'] = $m_univ['delivery_cost'];
					}
				}
			}

			$q_4 = $this->db->select('storeType.storeTypeName AS store_type_name,
									  storeType.storeTypeId AS store_type_id')
							->get('storeType');
			$all_type = $q_4->result_array();
			$q_5 = $this->db->select('storeTypeId AS store_type_id')
							->where('storeId', $store_id)
							->get('eachStoreType');
			$store_type = $q_5->result_array();
			foreach($all_type as &$s_type) {
				$s_type['checked'] = false;
				foreach($store_type as $m_type) {
					if ($s_type['store_type_id'] == $m_type['store_type_id']) {
						$s_type['checked'] = true;
					}
				}
			}
			$data['univ_info'] = $all_univ;
			$data['store_type_info'] = $all_type;
			// $data['store_info']
			return $data;

		}
		
		function confirm_store_info($store_id, $post, $pics_loc) {
			if ($post['store_tel_2'] == "")
				$post['store_tel_2'] = null;
			if ($post['store_tel_2'] == "")
				$post['store_tel_3'] = null;
			foreach($post['start_hour'] as $key=>$hour) {
				$openTime[] = $post['start_hour'][$key].':'.$post['start_minite'][$key].':'.'00';
				if ($post['end_hour'][$key] == "00" && $post['end_minite'][$key] =="00")
					$closeTime[] = '23:59:59';
				else
					$closeTime[] = $post['end_hour'][$key].':'.$post['end_minite'][$key].':'.'00';
			}
			// $this->time->add_hour($openTime[0], $closeTime[0]);
			// $this->time->add_hour($openTime[1], $closeTime[1]);
			// var_dump($post);
			$data = array(
						"storeName"=>$post['store_name'],
						"location"=>$post['store_loc'],
						"delivery_order"=>$post['deliver_order'],
						"telephone"=>$post['store_tel_1'],
						"telephone_1"=>$post['store_tel_2'],
						"telephone_2"=>$post['store_tel_3'],
						"openTime_1"=>$openTime[0],
						"closeTime_1"=>$closeTime[0],
						"openTime_2"=>$openTime[1],
						"closeTime_2"=>$closeTime[1],
						"gonggao"=>$post['gonggao'],
						"briefIntroduction"=>$post['brief_intro'],
						// "user_in_charge_time"=>date("Y-m-d H:i:s"),
						"qisongjia"=>$post['deliver_note'],
						"yinyeshijian"=>$post['open_time_note'],
						"contact_phone"=>$post['contact_phone_note'],
						"max_order"=>$post['max_order']
					);
			if ($pics_loc)
				$data['imgLoc'] = $pics_loc;
			$this->db->where('storeId', $store_id);
			$this->db->update('storeIntro', $data);
			// 先删除原先university的记录
			$this->db->where('storeId', $store_id);
			$this->db->delete('storeLoc');
			// 然后添加每个大学的起送价
			$data = array("storeId"=>$store_id);
			foreach ($post['university_id'] as $key=>$u_id) {
				// echo "asffs".$key."</br>";
				// echo "adsfa".$post['delivery_cost'][$key];
				$data['belongTo'] = $u_id;
				$data['delivery_cost'] = $post['delivery_cost'][$key];
				$this->db->insert('storeLoc', $data);
			}
			// 同样
			$this->db->where('storeId', $store_id);
			$this->db->delete('eachStoreType');

			$data = array("storeId"=>$store_id);
			foreach($post['store_type'] as $key=>$s_type) {
				$data["storeTypeId"] = $s_type;
				$this->db->insert('eachStoreType', $data);
			}
		}

		function sale_info($store_id) {
			// 这个月
			$q = $this->db->select('DISTINCT(createDate) as createDate')
						  ->where('storeId', $store_id)
						  ->where('unix_timestamp(createTime) BETWEEN 
						  		   unix_timestamp(DATE_SUB(curdate(), INTERVAL day(curdate())-1 DAY)) AND 
						  		   unix_timestamp(now())')
						  ->order_by('createDate', 'desc')
						  // ->limit(1)
						  ->get('eachFoodSaleInfo');
			$re = $q->result_array();
			foreach ($re as &$row) {
				$q = $this->db->select('saleId')
							  ->where('createDate', $row['createDate'])
							  ->where('storeId', $store_id)
							  ->where('validity', '1')
							  ->order_by('createTime', 'desc')
							  ->get('eachFoodSaleInfo');
				$t = $row['createDate'];
				$row['createDate'] = array();
				$row['createDate']['createDate'] = $t;
				$row['createDate']['saleId'] = $q->result_array();

				foreach($row['createDate']['saleId'] as &$row_2) {
					$q = $this->db->select('foodInfo.foodId as foodId,
											foodInfo.foodName as foodName,
								   			multiFoodSaleInfo.price as price,
								   			multiFoodSaleInfo.num as num')
								  ->from('multiFoodSaleInfo')
								  ->join('foodInfo', 'multiFoodSaleInfo.foodId = foodInfo.foodId')
								  ->where('saleId', $row_2['saleId'])
								  ->get();
					$t = $row_2['saleId'];
					$row_2['saleId'] = array();
					$row_2['saleId']['saleId'] = $t;
					$row_2['saleId']['foodInfo'] = $q->result_array();
				}
			}
			// print_r($re);
			return $re;
		}
		function get_month_0($store_id) {
			$q = $this->db->select('DISTINCT(createDate) as createDate')
						  ->where('storeId', $store_id)
						  ->where('unix_timestamp(createTime) BETWEEN 
						  		   unix_timestamp(DATE_SUB(curdate(), INTERVAL day(curdate())-1 DAY)) AND 
						  		   unix_timestamp(now())')
						  ->order_by('createDate', 'desc')
						  // ->limit(1)
						  ->get('eachFoodSaleInfo');
			return $q->result_array();
		}

		function get_month_1($store_id, $one) {
			$q = $this->db->select('DISTINCT(createDate) as createDate')
						  ->where('storeId', $store_id)
						  ->where("unix_timestamp(createTime) BETWEEN 
						  		   unix_timestamp(DATE_SUB(curdate()-day(curdate())+1, INTERVAL {$one} MONTH))
						  		   AND 
						  		   unix_timestamp(last_day(DATE_SUB(curdate()-day(curdate())+1, INTERVAL {$one} MONTH)))")
						  ->order_by('createDate', 'desc')
						  // ->limit(1)
						  ->get('eachFoodSaleInfo');
			return $q->result_array();
		}
		// $month == 0 =>这个月
		// == 1 => 前一个月
		// == 2 => 前前
		function sale_money($store_id, $month) {
			$re = array();
			switch ($month) {
				case '0':
					$re = $this->get_month_0($store_id);
					break;
				case '1':
				case '2':
					$re = $this->get_month_1($store_id, $month);
					break;
				default:
					$re['error'] = true;
					return $re;
			}
			// var_dump($re);
			foreach ($re as &$row) {
				$q = $this->db->select('saleId')
							  ->where('createDate', $row['createDate'])
							  ->where('storeId', $store_id)
							  ->where('validity', '1')
							  ->order_by('createTime', 'desc')
							  ->get('eachFoodSaleInfo');
				$t = $row['createDate'];
				$row['createDate'] = array();
				$row['createDate']['createDate'] = $t;
				$row['createDate']['saleId'] = $q->result_array();

				$money = 0;
				foreach($row['createDate']['saleId'] as &$row_2) {
					$q = $this->db->select('foodInfo.foodId as foodId,
											foodInfo.foodName as foodName,
								   			multiFoodSaleInfo.price as price,
								   			multiFoodSaleInfo.num as num')
								  ->from('multiFoodSaleInfo')
								  ->join('foodInfo', 'multiFoodSaleInfo.foodId = foodInfo.foodId')
								  ->where('saleId', $row_2['saleId'])
								  ->get();
					$food_info = $q->result_array();
					foreach ($food_info as $f) {
						$money += $f['price'] * $f['num'];
					}
				}
				$row['createDate']['money'] = $money;
			}
			// print_r($re);
			return $re;
		}
		function sale_details($store_id, $date) {
			$q = $this->db->select('saleId, user_addr, user_l_tel, user_s_tel')
						  ->where('createDate', $date)
						  ->where('storeId', $store_id)
						  ->where('validity', '1')
						  ->order_by('createTime', 'desc')
						  ->get('eachFoodSaleInfo');
			$re = $q->result_array();

			$details = array();
			foreach($re as $row_2) {
				
				$q = $this->db->select('foodInfo.foodId as foodId,
										foodInfo.foodName as foodName,
							   			multiFoodSaleInfo.price as price,
							   			multiFoodSaleInfo.num as num')
							  ->from('multiFoodSaleInfo')
							  ->join('foodInfo', 'multiFoodSaleInfo.foodId = foodInfo.foodId')
							  ->where('saleId', $row_2['saleId'])
							  ->get();
				$food_info = $q->result_array();
				$money = 0;
				foreach ($food_info as $f) {
					$money += $f['price'] * $f['num'];
				}
				$details[] = array('saleId'=>$row_2['saleId'],
								   'user_addr'=>$row_2['user_addr'],
								   'user_l_tel'=>$row_2['user_l_tel'],
								   'user_s_tel'=>$row_2['user_s_tel'],
								   'sale_money'=>$money,
								   'foodInfo'=>$food_info);
			}
			return $details;
		}
		function get_food_type_order($store_id) {
			$q = $this->db->select('foodTypeId as food_type_id,
								 foodTypeName as food_type_name,
								type_arrange_order as arrange_order')
						  ->from('foodTypeInEachStore')
						  ->where('storeId', $store_id)
						  ->where('deleted', '0')
						  ->order_by('type_arrange_order', 'desc')
						  ->get();
			return $q->result_array();
		}

		function update_food_type_order($type_order, $type_name) {
			$count = count($type_order) - 1;
			for($i = 0; $count >= 0; $count--, $i++) {
				$order = array(
							'type_arrange_order'=>$count,
							'foodTypeName'=>$type_name[$i]);
				$this->db->where('foodTypeId', $type_order[$i]);
				$this->db->update('foodTypeInEachStore', $order);
			}
		}

		function get_all_food_with_type_id($food_type_id, $img_or_not) {
			if ($img_or_not) {
				$q = $this->db->select('foodId as food_id,
									    foodName as food_name')
							  ->where('foodTypeId', $food_type_id)
							  ->where('deleted', 0)
							  ->where('imgLoc is not null')
							  ->order_by('food_arrange_order', 'desc')
							  ->get('foodInfo');
			}
			else {
				$q = $this->db->select('foodId as food_id,
									    foodName as food_name')
							  ->where('foodTypeId', $food_type_id)
							  ->where('deleted', '0')
							  ->where('imgLoc is null')
							  ->order_by('food_arrange_order', 'desc')
							  ->get('foodInfo');
			}
			return $q->result_array();

		}

		function update_food_order($food_order) {
			$count = count($food_order) - 1;
			for($i = 0; $count >= 0; $count--, $i++) {
				$order = array(
							'food_arrange_order'=>$count);
				$this->db->where('foodId', $food_order[$i]);
				$this->db->update('foodInfo', $order);
			}
		}
	}