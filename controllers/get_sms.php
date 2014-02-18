<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * 没有使用
	 * 短信宝接受用户短信回执
	 */
	class Get_sms extends CI_Controller {
		public function __construct()
        {
            parent::__construct();
            $this->load->library('get_db_info');
            $this->load->model('m_sms');
            $this->load->model('store_info');
            $this->load->model('sale_info');
        }

		public function index() {

			$usr_get = $_GET['usr'];
			$ord_get = $_GET['ord'];
			$sgn     = $_GET['sgn'];
			$q = $this->db->select('storeId')
						  ->where('IMEI', $usr_get)
						  ->where('pos_valid', '1')
				 	   	  ->get('storeIntro');
			if ($q->num_rows() <= 0)
				return;
			$store_id = $q->row_array()['storeId'];
			// 更新pos连续时间
			$this->db->where('storeId', $store_id);
			$this->db->set('pos_last_time', time());
			$this->db->update('storeIntro');
			//
			$q = $this->db->select('eachFoodSaleInfo.saleId as saleId,
								    createTime,
								    user_addr,
								    user_l_tel,
								    user_s_tel,
								    taste')
						  ->from('eachFoodSaleInfo')
						  ->join('multiFoodSaleInfo', 'eachFoodSaleInfo.saleId = multiFoodSaleInfo.saleId')
						  ->where('pos_send', '0')
						  ->where('unix_timestamp(createTime) >= unix_timestamp(curdate())')
						  ->where('eachFoodSaleInfo.storeId', $store_id)
						  ->order_by('createTime', 'asc')
						  ->limit(1)
						  ->get();
			if ($q->num_rows() <= 0)
				return;

			$sale_id = $q->row_array()['saleId'];
			$dom = new DomDocument("1.0", "GB2312");
			$r = $dom->createElement('r');
			$dom->appendchild($r);

			$num_today = $this->m_sms->get_sale_num_today($store_id) + 1;
			$store_name = $this->get_db_info->get_store_name($store_id);
			$content = "{$store_name}\n".
					   "今日第{$num_today}单：\n".
					   "订单号：{$sale_id}\n".
					   "订单时间：{$q->row_array()['createTime']}\n";
			$a = array();
			$k_m = $this->db->select('max(kuang) as kuang_max')
						     ->where('saleId', $sale_id)
						     ->get('multiFoodSaleInfo');
			$k_m = $k_m->row_array()['kuang_max'];
			for ( $i = 0; $i <= $k_m; $i++ ) {
				$sale_details = $this->db->select('foodInfo.foodId as food_id,
												   foodInfo.foodName as food_name,
												   multiFoodSaleInfo.price as food_price,
												   multiFoodSaleInfo.num as food_num')
										 ->from('multiFoodSaleInfo')
										 ->join('foodInfo', 'foodInfo.foodId = multiFoodSaleInfo.foodId')
										 ->where('saleId', $sale_id)
										 ->where('kuang', $i)
										 ->get();
				$a[] = $sale_details->result_array();
			}
			$sum = 0;
			foreach ($a as $key => $b) {
				$key_1 = $key + 1;
				$content .= "第{$key_1}框:\n";
				foreach ($b as $c) {
					$s = sprintf("%10s%5s%5s\n", $c['food_name'], "￥".$c['food_price'], $c['food_num']."份");
					$content .= $s;
					$sum += $c['food_num'] * $c['food_price'];
				}
				$content .= "\n";
			}

			$content .= "地址：".$q->row_array()['user_addr']."\n".
						"长号：".$q->row_array()['user_l_tel'].
						" 短号：".$q->row_array()['user_s_tel']."\n".
			            "总计：". $sum." 口味：".$q->row_array()['taste'];
			$data = array(
						'id'=>$sale_id,
						'time'=>date('Y-m-d H:i:s', time()),
						'content'=>$content,
						'setting'=>'101:6|105:0|');
			foreach ($data as $key => $val) {
	            $key_d = $dom->createElement($key);
	            $r->appendchild($key_d);

	            $text = $dom->createTextNode($val);
	            $key_d->appendchild($text);
			}
			echo $dom->saveXML();

			$this->store_info->update_store_sale_order($sale_id, $store_id, $a);
			$this->sale_info->update_pos_send($sale_id);
		}
	}
?>