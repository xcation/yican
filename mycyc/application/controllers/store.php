<?php

	class Store extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->helper('cookie');
			$this->load->model('food_info');
			$this->load->model("store_info");
			$this->load->library("get_db_info");
			$this->load->library("state_name");
		}
		
		public function index($university_id, $store_id, $old_university, $old_store){
			$data = $this->store_info->get_store_top_info($university_id, $store_id);
			$data['food_info'] = $this->food_info->get_food_info($store_id);
			$data['food_type'] = $this->food_info->re;
			$data['store_id'] = $store_id;
			$data['store_name'] = $this->get_db_info->get_store_name($store_id);
			$now_state = $this->store_info->get_shanghu_state($store_id);

			$data['state'] = $this->state_name->is_work_state($now_state);
			$data['delivery_cost'] = $this->store_info
										  ->get_delivery_cost($university_id, $store_id);
			$data['university_id'] = $university_id;
			
			$data['order_store']=json_decode($this->input->cookie('mycyc_order_store'), true);
			if($data['order_store'])
				$data['other_store_name']=$this->get_db_info->get_store_name($data['order_store']['storeId']);

			$data['another_one'] = $this->input->post('food', TRUE);
			$data['old_university'] = @$old_university;
			$data['old_store'] = @$old_store;
			if (@$old_store) {
				$data['old_store_name'] = $this->get_db_info->get_store_name($old_store);
			}
			$this->load->view('templates/header');
			$this->load->view('store/food_list', $data);
			$this->load->view('templates/footer');
		}
		
		public function new_food($university_id, $store_id, $food_id) {

			if (@$university_id && @$store_id && @$food_id) {
				$order = json_decode($this->input->cookie('mycyc_order', TRUE));
				$order_store = json_decode($this->input->cookie('mycyc_order_store'), TRUE);
				$cookie = array();
				if (!$order_store) {
					$cookie = array('name'=>'order_store',
									'value'=>json_encode(array('universityId'=>$university_id,
															   'storeId'=>$store_id)),
									'expire'=>'259200',
									'prefix'=>'mycyc_');
					$this->input->set_cookie($cookie);
				}
				else if ($order_store['storeId'] != $store_id) {
					header("location: ".constant('mycycbase')."/store/{$university_id}/{$store_id}/{$order_store['universityId']}/{$order_store['storeId']}");
					return;
				}
				if(!$order) {
					$tmp = $this->food_info->get_food_name_price($food_id);
					$order[0] = array('foodId'=>$food_id,
								   'foodName'=>$tmp['foodName'],
								   'foodPrice'=>$tmp['price'],
								   'amount'=>1);
				}
				else {
					var_dump($order);
					foreach ($order as $row) {
						if ($row->foodId == $food_id) {
							$find_ = 1;
							break;
						}
					}
					if (@$find_)
						;
					else {
						$tmp = $this->food_info->get_food_name_price($food_id);
						$new = array('foodId'=>$food_id,
									   'foodName'=>$tmp['foodName'],
									   'foodPrice'=>$tmp['price'],
									   'amount'=>1);
						array_push($order, $new);
					}
				}
				$cookie = array(
								'name' => 'order',
								'value' => json_encode($order),
								'expire' => '2592000',
								'prefix' => 'mycyc_'
							);
				$this->input->set_cookie($cookie);
				header("location: ".constant('mycycbase')."/store/{$university_id}/{$store_id}");
			}
			else
				return;
		}

		public function reset_order($university_id, $store_id) {
			delete_cookie('mycyc_order');
			// var_dump('location:'.constant('mycycbase')."/{$university_id}/{$store_id}");
			header('location:'.constant('mycycbase')."/store/{$university_id}/{$store_id}");
		}

		public function reset_all($university_id, $store_id) {
			delete_cookie('mycyc_order');
			delete_cookie('mycyc_order_store');
			$href = constant('mycycbase')."/store/{$university_id}/{$store_id}";
			$data['href'] = $href;
			$this->load->view("store/reset_header", $data);
		}
		public function info($university_id, $store_id){
			$data = $this->store_info->get_store_top_info($university_id, $store_id);
			$data['food_info'] = $this->food_info->get_food_info($store_id);
			$data['food_type'] = $this->food_info->re;
			$data['store_id'] = $store_id;
			$data['store_name'] = $this->get_db_info->get_store_name($store_id);
			$now_state = $this->store_info->get_shanghu_state($store_id);

			$data['state'] = $this->state_name->is_work_state($now_state);
			$data['delivery_cost'] = $this->store_info
										  ->get_delivery_cost($university_id, $store_id);
			$data['university_id'] = $university_id;
			
			$this->load->view('templates/header');
			$this->load->view('store/store_info', $data);
			$this->load->view('templates/footer');
		}
	
	}
?>