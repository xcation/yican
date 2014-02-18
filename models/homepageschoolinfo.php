<?php
	class Homepageschoolinfo extends CI_Model {

		public function __construct() {
			$this->load->database();
			// $this->SCHOOL_1 = 3;
			// $this->SCHOOL_2 = 4;
			// $this->SCHOOL_3 = 5;
			// $this->REGION_ 1 =

		}
		function get_univ_info() {
			$q = $this->db->select('region_id, imgLoc')
						  ->get('region');

			// $query = $this->db->select("schoolShortName AS univ_short_name,
			// 							imgLoc")
			// 				  ->where('imgLoc is not null')
			// 				  ->get('schoolInfo');
			return $q->result_array();
		}
	}
?>