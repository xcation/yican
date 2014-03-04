<?php
	class User_info extends CI_Model {
		var $userInfo;
		public function __construct() {
			$this->load->database();
			$this->load->library('session');
		}

		function find_user_by_email($email) {
			$q = $this->db->select('userId')
						  ->where('email', $email)
						  ->where('valid', '0')
						  ->get('userInfo');
			if ($q->num_rows() > 0) {
				$this->db->where('userId', $q->row_array['userId']);
				$this->db->update('userInfo', array('valid'=>'1'));
				return true;
			}
			return false;
		}
		function get_address($userid) {
			$q = $this->db->select('id, userPos, userPhone_main, userPhone_short, is_default')
						 ->where('userId', $userid)
						 ->order_by('is_default', 'desc')
						 ->get('userPosition');
			return $q->result_array();
		}
		function get_recent() {
			if ($this->input->cookie('addr', TRUE)) {
				$recent_loc['addr'] = $this->input->cookie('addr', TRUE);
				$recent_loc['l_tel'] = $this->input->cookie('l_tel', TRUE);
				$recent_loc['s_tel'] = $this->input->cookie('s_tel', TRUE);
				return $recent_loc;
			}
			else {
				if ($this->userInfo['login']) {
					$this->db->select("userPos, userPhone_main, userPhone_short");
					$this->db->where('userId', $this->userInfo['userName']);
					$this->db->order_by('last_use_time', 'desc');
					$this->db->limit(1);
					$query = $this->db->get('userPosition');
					if ($query->num_rows() > 0)
						return $query->row_array();
				}
			}
		}

		function get_login() {
			$this->userInfo['login'] = false;
			$userid = $this->session->userdata('userid');
			if ($userid) {
				$this->userInfo['userName'] = $userid;
				$this->userInfo['login'] = true;
			}
			else {
				$userid = $this->input->cookie('mycyc_user');
				if ($userid) {
					$this->userInfo['userName'] = $userid;
					$this->userInfo['login'] = true;
				}
			}
			// var_dump($this->userInfo);
			// echo "das";
			return $this->userInfo;
		}
		function return_check() {
			$query = $this->db->get('userInfo');
			if ($query->num_rows() > 0)
				return false;
			else
				return true;
		}
		function check_user_id($user_id) {
			// $q = $this->db->select('userId')
			// 			  ->where("( (valid = '1') OR 
			// 							( unix_timestamp(createTime) > unix_timestamp(DATE_SUB(now(), INTERVAL 1 DAY)) ) ) 
			// 			  			AND (userId = '{$user_id}')")
			// 			  ->get('userInfo');
			$q = $this->db->select('userId')
						  ->where('userId', $user_id)
						  ->get('userInfo');
			if ($q->num_rows() > 0)
				return false;
			$q = $this->db->select('storeId')
						  ->where('loginId', $user_id)
						  ->get('storeIntro');
			if ($q->num_rows() > 0)
				return false;
			return true;
		}
		function check_email($email) {
			$q = $this->db->select('userId')
						  ->where("(email = '{$email}') AND 
						  		  (valid = '1' OR 
						  		  		unix_timestamp(createTime) > unix_timestamp(DATE_SUB(now(), INTERVAL 1 DAY)))")
						  ->get('userInfo');
			if ($q->num_rows() > 0)
				return false;
			else
				return true;
		}
		function check_phone($phone) {
			if (strlen($phone) != 11)
				return false;
			$this->db->select('userId');
			$this->db->where('telephone', $phone);
			$query = $this->db->get('userInfo');
			if ($query->num_rows() > 0)
				return false;
			else
				return true;
		}
		function insert_one_user($post) {
			$data = array(
						"userId"=>$post['reg_login_id'],
						"userPasswd"=>md5($post['reg_passwd']),
						// "email"=>$post['reg_email']
					);
			$this->db->insert('userInfo', $data);
		}
		function insert_user($post) {
			$msg = array();
			$valid = false;
			$msg['error'] = "非法操作";
			if (
				   isset($post['reg_passwd']) 
				&& isset($post['reg_passwd_con']) 
				&& isset($post['reg_login_id']) 
				// && isset($post['reg_email'])
				) {
				if ($post['reg_passwd']==$post['reg_passwd_con']) {
					$len = strlen($post['reg_login_id']);
					if ($len > 0 && $len <= 30) { // 检查user_id是否已经注册
						if ($this->check_user_id($post['reg_login_id'])) {
							$valid = true;
						}
						else
							$msg['error'] = '用户名已经被注册';
					}
					else
						$msg['error'] = '用户名长度为6~30位';
				}
			}
			if ($valid)
				$this->insert_one_user($post);
			$msg['valid'] = $valid;
			return $msg;
		}
		function _login_with($with, $login_id, $passwd, &$username) {
			$query = $this->db->select('userId')
							  ->where($with, $login_id)
							  ->where('userPasswd', md5($passwd))
							  ->get('userInfo');
			if ($query->num_rows() > 0) {
				$username=$query->row_array()['userId'];
				return true;
			}
			return false;
		}
		function user_login($login_id, $passwd) {
			$username=$login_id;
			$valid = true;
			if ($login_id != "" && $passwd != "") {
				if (!$this->_login_with('userId', $login_id, $passwd, $username)) {
					// if (!$this->_login_with('telephone', $login_id, $passwd, $username))
						$valid = false;
				}
			}
			else
				$valid = false;

			if ($valid) {
				$this->session->unset_userdata('userid');
				$this->session->set_userdata("userid", $username);
			}
			return $valid;
			
		}
		function store_login($id, $ps) {
			$q = $this->db->select('storeId')
						  ->where('loginId', $id)
						  ->where('loginPasswd', md5($ps))
						  ->get('storeIntro');
			$re = array();
			$re['valid'] = false;

			if ($q->num_rows() > 0) {
				$re['valid'] = true;
				$re['store_id'] = $q->row_array()['storeId'];
				$this->session->unset_userdata('storeid');
				$this->session->set_userdata("storeid", $re['store_id']);
			}
			return $re;
		}

	}
