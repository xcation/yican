<?php
	class Rooter_info extends CI_Model {
		function __construct() {
			$this->load->database();
			$this->load->library('time');
			$this->load->library('pin');
			$this->REGION_ROOT = '4';
		}
		function check_id_valid($select_item, $column_name, $column_value, $table_name) {
			$q = $this->db->select($select_item)
						  ->where($column_name, $column_value)
						  ->get($table_name);
			if ($q->num_rows() > 0)
				return false;
			return true;
		}
		function check_store_name($store_name) {
			return $this->check_id_valid('storeId', 'storeName', $store_name, 'storeIntro');
		}
		function check_store_login_id($login_id) {
			if ($this->check_id_valid('storeId', 'loginId', $login_id, 'storeIntro'))
				return $this->check_id_valid('userId', 'userId', $login_id, 'userInfo');
			return false;
		}

		function new_store($store_name,
						   $store_loc,
						   $store_img,
						   $store_login_id,
						   $store_passwd,
						   $store_tel,
						   $store_tel_2,
						   $store_tel_3,
						   $store_delivery_cost,
						   $university_id,
						   $start_hour,
						   $start_minite,
						   $end_hour,
						   $end_minite,
						   $deliver_order,
						   $max_order,
						   $store_type) {
			$re['state'] = 0;
			if (!$this->check_store_name($store_name)) {
				$re['error'] = '商店名重复';
				return $re;
			}
			if (!$this->check_store_login_id($store_login_id)) {
				$re['error'] = '用户名重复';
				return $re;
			}

			if ($store_tel_2 == "")
				$store_tel_2 = null;
			if ($store_tel_3 == "")
				$store_tel_3 = null;
			foreach($start_hour as $key=>$hour) {
				$openTime[] = $start_hour[$key].':'.$start_minite[$key].':'.'00';
				if ($end_hour[$key] == "00" && $end_minite[$key] =="00")
					$closeTime[] = '23:59:59';
				else
					$closeTime[] = $end_hour[$key].':'.$end_minite[$key].':'.'00';
			}

			// $this->time->add_hour($openTime[0], $closeTime[0]);
			// $this->time->add_hour($openTime[1], $closeTime[1]);
			$data = array(
						"storeName"=>$store_name,
						"location"=>$store_loc,
						"imgLoc"=>$store_img,
						"loginId"=>$store_login_id,
						"loginPassWd"=>md5($store_passwd),
						"telephone"=>$store_tel,
						"telephone_1"=>$store_tel_2,
						"telephone_2"=>$store_tel_3,
						"createTime"=>date("Y-m-d"),
						"user_in_charge_time"=>date("Y-m-d H:i:s"),
						"openTime_1"=>$openTime[0],
						"closeTime_1"=>$closeTime[0],
						"openTime_2"=>$openTime[1],
						"closeTime_2"=>$closeTime[1],
						"delivery_order"=>$deliver_order,
						"max_order"=>$max_order
					);
			$this->db->insert('storeIntro', $data);
			$store_id = $this->db->insert_id();

			$data = array("storeId"=>$store_id);
			foreach ($university_id as $key=>$u_id) {
				$data['belongTo'] = $u_id;
				$data['delivery_cost'] = $store_delivery_cost[$key];
				$this->db->insert('storeLoc', $data);
			}

			$data = array("storeId"=>$store_id);
			foreach($store_type as $key=>$s_type) {
				$data["storeTypeId"] = $s_type;
				$this->db->insert('eachStoreType', $data);
			}
			$re['state'] = 1;
			return $re;
		}
		function new_store_type($store_type) {
			$data = array(
						"storeTypeName"=>$store_type
					);
			$this->db->insert('storeType', $data);
		}
		function get_all_university() {
			$q = $this->db->select('schoolId, schoolFullName')
						  ->get('schoolInfo');
			return $q->result_array();
		}
		function get_all_university_in_region($region_id)
		{
			$q = $this->db->select('schoolId, schoolFullName')
						  ->from('schoolInfo')
						  ->join('region', 'region.region_id = schoolInfo.region_code')
						  ->where('region.region_id', $region_id)
						  ->get();
			return $q->result_array();
		}
		function get_all_store_type() {
			$q = $this->db->select('storeTypeName, storeTypeId')
						  ->get('storeType');
			return $q->result_array();
		}
		function new_univ($univ_type, $imgLoc) {
			$data = array(
						'schoolFullName'=>$univ_type['univ_full_name'],
						'schoolShortName'=>$univ_type['univ_short_name'],
						'imgLoc'=>$imgLoc
					);
			$this->db->insert('schoolInfo', $data);
		}

		function new_univ_one_region($region_id, $univ_type, $imgLoc) {
			$data = array(
						'schoolFullName'=>$univ_type['univ_full_name'],
						'schoolShortName'=>$univ_type['univ_short_name'],
						'imgLoc'=>$imgLoc,
						'region_code'=>$region_id
					);
			$this->db->insert('schoolInfo', $data);
		}

		function region_valid($user_id, $region_name) {
			$re = array();
			$re['valid'] = 1;
			$q = $this->db->select('region_id')
						  ->where('region_name', $region_name)
						  ->get('region');
			if ($q->num_rows() > 0) {
				$re['valid'] = 0;
				$re['msg'] = '区域名称重复';
				return $re;
			}
			$q = $this->db->select('mana_id')
						  ->where('mana_id', $user_id)
						  ->get('management');
			if ($q->num_rows() > 0) {
				$re['valid'] = 0;
				$re['msg'] = '用户名重复';
			}
			return $re;

		}
		function new_region($post, $img_loc) {
			$pin = new Pin();
			$this->db->trans_start();
				$data = array('region_name'=>$post['region_name'],
				              'region_pinyin'=>$pin->Pinyin($post['region_name'], 'UTF-8'),
				              'imgLoc'=>$img_loc);
				$this->db->insert('region', $data);
				$region_id = $this->db->insert_id();
				$data = array('mana_id'=>$post['userid'],
				              'mana_pass'=>md5($post['password']),
				              'mana_prio'=>$this->REGION_ROOT,
				              'mana_region'=>$region_id
				              );
				$this->db->insert('management', $data);
			$this->db->trans_complete();
		}
		function get_all_univ_type() {
			$q = $this->db->select('schoolFullName AS univ_full_name')
						  ->get('schoolInfo');
			return $q->result_array();
		}

		function get_all_univ_type_one_region($region_id) {
			$q = $this->db->select('schoolFullName AS univ_full_name')
						  ->from('schoolInfo')
						  ->join('region', 'region.region_id = schoolInfo.region_code')
						  ->where('region.region_id', $region_id)
						  ->get();
			return $q->result_array();
		}

		function get_all_store_one_univ($univ) {
			$q = $this->db->select('storeName AS store_name,
									storeIntro.storeId AS store_id,
									state')
						  ->from('storeIntro')
						  ->join('storeLoc', 'storeLoc.storeId=storeIntro.storeId')
						  ->where('storeLoc.belongTo', $univ)
						  ->order_by('storeLoc.store_order', 'desc')
						  ->get();
			return $q->result_array();
		}

		function get_all_store_one_univ_order_type($univ) {
			$re = array();
			$q = $this->db->select('storeName AS store_name,
									storeIntro.storeId AS store_id,
									state')
						  ->from('storeIntro')
						  ->join('storeLoc', 'storeLoc.storeId=storeIntro.storeId')
						  ->where('storeLoc.belongTo', $univ)
						  ->where('delivery_order', '0')
						  ->order_by('storeLoc.store_order', 'desc')
						  ->get();
			$re[] = $q->result_array();
			$q = $this->db->select('storeName AS store_name,
									storeIntro.storeId AS store_id,
									state')
						  ->from('storeIntro')
						  ->join('storeLoc', 'storeLoc.storeId=storeIntro.storeId')
						  ->where('storeLoc.belongTo', $univ)
						  ->where('delivery_order', '1')
						  ->order_by('storeLoc.store_order', 'desc')
						  ->get();
			$re[] = $q->result_array();
			return $re;
		}
		function delete_store($store_id, $state) {
			$this->db->set('state', $state);
			$this->db->where('storeId', $store_id);
			$this->db->update('storeIntro');
		}
		private function _update_store_order($univ, $deliver_order) {
			$count = count($deliver_order) - 1;
			// var_dump($deliver_order);
			for ($i = 0; $count >= 0; $count--, $i++) {
				$order = array('store_order'=>$count);
				$this->db->where('storeId', $deliver_order[$i]);
				$this->db->where('belongTo', $univ);
				$this->db->update('storeLoc', $order);
			}
		}
		function update_store_order($univ, $waimai, $yuding) {
			$this->_update_store_order($univ, $waimai);
			$this->_update_store_order($univ, $yuding);
		}
	}