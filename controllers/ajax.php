<?php
	/**
	 * 未完成
	 * 用来响应ajax请求，包括网吧入口搜索
	 */
	class Ajax extends CI_Controller {
		public function __construct() {
			parent::__construct();
			$this->ciber_region_code = 4;
		}
		public ciber_autocomplete() {

			// prevent direct access
			$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
			if(!$isAjax) {
				$user_error = 'Access denied - not an AJAX request...';
				trigger_error($user_error, E_USER_ERROR);
			}

			// get what user typed in autocomplete input
			$term = trim($_GET['term']);

			$a_json = array();
			$a_json_row = array();

			$a_json_invalid = array(array("id" => "#", "value" => $term, "label" => "Only letters and digits are permitted..."));
			$json_invalid = json_encode($a_json_invalid);

			// replace multiple spaces with one
			$term = preg_replace('/\s+/', ' ', $term);

			// SECURITY HOLE ***************************************************************
			// allow space, any unicode letter and digit, underscore and dash
			if(preg_match("/[^\040\pL\pN_-]/u", $term)) {
				print $json_invalid;
				exit;
			}
			// *****************************************************************************
			//

			$q = $this->db->select('schoolFullName, schoolId, imgLoc, schoolShortName');
			$parts = explode(' ', $term);
			$p = count($parts);



			if (preg_match("/[a-zA-Z0-9]+/", $term)) {
				for($i = 0; $i < $p; $i++) {
					$q = $q->like('schoolPinYin', $parts[$i]);
					// ' AND post_title LIKE ' . "'%" . $conn->real_escape_string() . "%'";
				}
			} else {
				$q = $q->like('schoolFullName', $term);
			}
		    $q = $q->where('region_code', $this->ciber_region_code)
		     	   ->get('schoolInfo');
		    foreach ($q->result_array() as $row) {

		    }

			// $rs = $conn->query($sql);
			// if($rs === false) {
			// 	$user_error = 'Wrong SQL: ' . $sql . 'Error: ' . $conn->errno . ' ' . $conn->error;
			// 	trigger_error($user_error, E_USER_ERROR);
			// }

			// while($row = $rs->fetch_assoc()) {
			// 	$a_json_row["id"] = $row['url'];
			// 	$a_json_row["value"] = $row['post_title'];
			// 	$a_json_row["label"] = $row['post_title'];
			// 	array_push($a_json, $a_json_row);
			// }

			// highlight search results
			// $a_json = apply_highlight($a_json, $parts);

			$json = json_encode($a_json);
			echo $json;
		}
?>