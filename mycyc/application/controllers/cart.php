<?php
class Cart extends CI_Controller{

	function __construct(){
		parent::__construct();
		$this->load->helper('cookie');
		$this->load->model("food_info");
		$this->load->model("store_info");
		$this->load->model('m_sms');
		$this->load->model('user_info');
		$this->load->model('sale_info');
		$this->load->model('quick_find');
		$this->load->library("get_db_info");
	}

	private function query_food_info(&$data){
		$item_num = count($data['order']);
		for($i=0; $i<$item_num; $i++){
			$temp = $this->food_info->get_food_name_price($data['order'][$i]['foodId']);
			$data['order'][$i]['foodName'] = $temp['foodName'];
			// $data['order'][$i]['foodPrice'] = $temp['price'];
			$data['order'][$i]['amount'] = 1;
		}
	}

	public function index() {
		$json_order=$this->input->cookie('mycyc_order');
		$json_order_store=$this->input->cookie('mycyc_order_store');
		if(!$json_order || !$json_order_store){
			$this->load->view('templates/header');
			$this->load->view('cart/empty_body');
			$this->load->view('templates/footer');
		}
		else {
			$order_store=json_decode($json_order_store, true);
			$now_store = $this->input->post('now_store', true);
			$exit = 0;
			if ($now_store) {
				if ($order_store['storeId'] != $this->input->post('now_store', true)) {
					$data['return_university_id'] = $this->input->post('now_university', true);
					$data['return_store_id'] = $this->input->post('now_store', true);
					$exit = 1;
				}
			}
			if (!$exit) {
				// $order = array();
				$i = 0;
				$order=json_decode($json_order, true);
				$data['order'] = $order;
				// foreach ($json_order as $food_id) {
				// 	$order[$i]['foodId'] = $food_id;
				// 	$i++;
				// }
				// $data['order']=$order;
				// $json_order=json_encode($order);
				// $cookie = array(
				// 	'name' => 'mycyc_order',
				// 	'value' => $json_order,
				// 	'expire' => '414240'
				// );
				// $this->input->set_cookie($cookie);


				$data['university_id']=$order_store['universityId'];
				$data['store_id']=$order_store['storeId'];
				$data['store_name'] = $this->get_db_info->get_store_name($data['store_id']);
				$data['delivery_cost'] = $this->store_info->get_delivery_cost($data['university_id'], $data['store_id']);

				$this->query_food_info($data);

				if($amount = $this->input->post("amount")){
					$item_num = count($data['order']);
					foreach($amount as $amount_key => $amount_value){
						for($i=0; $i<$item_num; $i++){
							if($amount_key==$data['order'][$i]['foodId'])
								$data['order'][$i]['amount'] =  $amount_value;
						}
					}
					delete_cookie('mycyc_order');
					$cookie = array(
									'name' => 'order',
									'value' => json_encode($data['order']),
									'expire' => '2592000',
									'prefix' => 'mycyc_'
								);
					$this->input->set_cookie($cookie);
				}


				$user_login = $this->user_info->get_login();
				if ($user_login['login'])
					$data['all_address'] = $this->user_info->get_address($user_login['userName']);
			}
			$this->load->view('templates/header');
			$this->load->view('cart/body',$data);
			$this->load->view('templates/footer');
		}
	}

	public function delete($food_id){
		$json_order=$this->input->cookie('mycyc_order');
		$json_order_store=$this->input->cookie('mycyc_order_store');

		$order = json_decode($json_order, true);

		$item_num = count($order);
		for($i=0; $i<$item_num; $i++){
			if($order[$i]['foodId'] == $food_id){
				array_splice($order, $i, 1);
				break;
			}
		}

		$json_order=json_encode($order);
		$cookie = array(
			'name' => 'mycyc_order',
			'value' => $json_order,
			'expire' => '414240'
		);
		$this->input->set_cookie($cookie);

		$cookie = array(
			'name' => 'mycyc_order_store',
			'value' => $json_order_store,
			'expire' => '414240'
		);
		$this->input->set_cookie($cookie);

		header('location:'.constant('mycycbase').'/cart');
	}

	public function check(){
		$info = $this->input->post("info");
		if (!$info)
			return;
		$user_login = $this->user_info->get_login();
		if ($user_login['login']) {
			$has_default_address = $this->input->post('has_default_address', TRUE);
			if ($has_default_address == 0) {
				if ($info['address'] == "" || $info['mobile'] == "" || strlen($info['mobile']) != 11) {
					$data['not_fill'] = 1;
					$this->load->view('templates/header');
					$this->load->view('cart/success',$data);
					$this->load->view('templates/footer');
					return;
				}
				else {
					$dat = array(
								"userId"=>$user_login['userName'],
								"userPos"=>$info['address'],
								"userPhone_main"=>$info['mobile'],
								"userPhone_short"=>$info['short_mobile']
						   );
					$this->db->insert('userPosition', $dat);
				}
			}
			else {
				$pos = $this->input->post('choosen_address', TRUE);
				$this->db->where('id', $pos);
				$this->db->where('userId', $user_login['userName']);
				$this->db->set('is_default', '1');
				$this->db->update('userPosition');
				$q = $this->db->select('userPos, userPhone_main, userPhone_short')
							 ->where('id', $pos)
							 ->get('userPosition');
				$info['address'] = $q->row_array()['userPos'];
				$info['mobile'] = $q->row_array()['userPhone_main'];
				$info['short_mobile'] = $q->row_array()['userPhone_short'];
			}
		}
		// var_dump($info);
		if ($info['address'] == "" || $info['mobile'] == "" || strlen($info['mobile']) != 11) {
			$data['not_fill'] = 1;
			$this->load->view('templates/header');
			$this->load->view('cart/success',$data);
			$this->load->view('templates/footer');
			return;
		}
		$buyerId = $info['mobile'];
		$cookie_id = $this->input->cookie('mycyc_user', TRUE);
		if ($cookie_id)
			$buyerId = $cookie_id;
		$store_id = $info['store_id'];
		$sale_info = array(
			"buyerId"=>$buyerId,
			"storeId"=>$store_id,
			"university_id"=>$info['university_id'],
			"createTime"=>date("Y-m-d H:i:s"),
			"createDate"=>date("Y-m-d"),
			"user_addr"=>$info['address'],
			"user_l_tel"=>$info['mobile'],
			"user_s_tel"=>$info['short_mobile'],
			"taste"=>$info['ps'],
			"validity"=>'0'
		);


		$succeeded=$this->db->insert('eachFoodSaleInfo', $sale_info);
		$re = array();
		$re['state'] = 1;
		if($succeeded){
			$sale_id = $this->db->insert_id();

			$a = array();
			if($amount = $this->input->post("amount")){
				$food_price = $this->input->post('food_price');
				$food_name = $this->input->post('food_name');
				$i = 0;
				$a[0] = array();
				foreach($amount as $amount_key => $amount_value) {
					$data = array(
								 'saleId'=>$sale_id,
								 'foodId'=>$amount_key,
								 'price'=>$food_price[$i],
								 'num'=>$amount_value,
								 'kuang'=>'0');
					$a[0][] = array(
								   'food_name'=>$food_name[$i],
								   'food_price'=>$food_price[$i],
								   'food_num'=>$amount_value,
								   'food_id'=>$amount_key);
					$this->db->insert('multiFoodSaleInfo', $data);
					$i++;
				}
			}

			$num_ = $this->m_sms->get_sale_num_today($store_id);
			$d = date("m.d");
			$dd = explode('.', $d);
			$len = 4 - strlen($num_);
			$n = $store_id.$dd[0].$dd[1];
			while ($len > 0) {
				$n .= '0';
				$len--;
			}
			$n .= $num_;
			// 发给商家短信，或者pos
			$q = $this->db->select('pos_valid')
						  ->where('storeId', $store_id)
						  ->get('storeIntro');
			$pos_valid = $q->row_array()['pos_valid'];
			// if (!$pos_valid) // 发送短信
			{
				$store_deliver = $this->food_info->get_store_deliver($store_id);
				$loc = array('addr'=>$info['address'],
					         'l_tel'=>$info['mobile'],
					         's_tel'=>$info['short_mobile']);

				if (!$this->m_sms->send_order($sale_id,
											 $n,
											 $store_deliver,
											 $info['ps'],
											 $a,
											 $loc)) {
					// 修改商户状态为太忙碌
					$this->store_info->set_state($store_id, '3');
					if ($re['state'] == 1) {
						$re['error'] = "{$store['store_name']}"; // 商家信息错误
						$re['state'] = 0;
					}
					else
						$re['error'] .= "、{$store['store_name']}";
				}
				else {
					$this->store_info->update_store_sale_order($sale_id, $store_id, $a);
					// 表示发送了
					$this->sale_info->update_pos_send($sale_id);
					// 商户根据confirm time判断是否为新订单，如果还没有confirm则confirmtime为null
				}
				// 如果没有登录，则更新history_order

			}

			$history_order = array();
			$temp = json_decode($this->input->cookie('mycyc_history_order'), true);
			if($temp)
				$history_order = $temp;
			// var_dump($history_order);
			$history_order[] = $sale_id;
			$cookie = array(
				'name' => 'history_order',
				'value' => json_encode($history_order),
				'expire' => '2592000',
				'prefix' => 'mycyc_'
			);
			// var_dump($history_order);
			$this->input->set_cookie($cookie);
			delete_cookie("mycyc_order");
			delete_cookie("mycyc_order_store");
			$data['sale_id'] = $sale_id;
			$data['re'] = $re;
			$this->load->view('templates/header');
			$this->load->view('cart/success',$data);
			$this->load->view('templates/footer');
		}
	}

	public function modify_addr($modi_pos=0) {
		$user_login = $this->user_info->get_login();
		if ($user_login['login']) {
			$userid = $user_login['userName'];
			$post = $this->input->post(NULL, TRUE);
			$data['modi_pos'] = $modi_pos;
			if ($post) {
				$this->db->where('id', $post['modi_pos']);
				$this->db->where('userId', $userid);
				$this->db->set("userPos", $post['addr']);
				$this->db->set("userPhone_main", $post['long_tel']);
				$this->db->set("userPhone_short", $post['short_tel']);
				$this->db->update('userPosition');
				if ($this->db->affected_rows() > 0)
					$data['modi_res'] = 1;
				else
					$data['modi_res'] = -1;
				var_dump($data);
			}
			else {
				$q = $this->db->select('userPos as addr,
									   userPhone_main as long_tel,
									   userPhone_short as short_tel')
							 ->where('id', $modi_pos)
							 ->where('userId', $userid)
							 ->get('userPosition');
				if ($q->num_rows() == 0)
					return;
				$data['addr'] = $q->row_array()['addr'];
				$data['long_tel'] = $q->row_array()['long_tel'];
				$data['short_tel'] = $q->row_array()['short_tel'];
			}
			$this->load->view('templates/header');
			$this->load->view('cart/modify_addr', $data);
			$this->load->view('templates/footer');
		}
	}

	public function delete_addr($modi_pos) {
		$user_login = $this->user_info->get_login();
		if ($user_login['login']) {
			$userid = $user_login['userName'];
			$this->db->where('id', $modi_pos);
			$this->db->where('userId', $userid);
			$this->db->delete('userPosition');
			$data['delete'] = 1;
			$data['is_deleted'] = $this->db->affected_rows();
			$this->load->view('templates/header');
			$this->load->view('cart/modify_addr', $data);
			$this->load->view('templates/footer');
		}
	}

	public function new_addr() {
		$user_login = $this->user_info->get_login();
		if ($user_login['login']) {
			$userid = $user_login['userName'];
			$post = $this->input->post(null, true);
			$data['new_addr'] = 1;
			if ($post) {
				$tmp = array('userPos'=>$this->input->post('addr', TRUE),
							  'userPhone_main'=>$this->input->post('long_tel', TRUE),
							  'userPhone_short'=>$this->input->post('short_tel', TRUE),
							  'userId'=>$userid);
				$this->db->insert('userPosition', $tmp);
				if ($this->db->affected_rows() > 0)
					$data['new_res'] = 1;
				else
					$data['new_res'] = -1;
			}
			// var_dump($data);
			$this->load->view('templates/header');
			$this->load->view('cart/modify_addr', $data);
			$this->load->view('templates/footer');
		}
	}

}

?>