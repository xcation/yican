<?php
	/**
	 * 当出现错误时跳回
	 */
	class Errors extends CI_Controller {
		public function location() {
			echo "您输入的地址有误，马上跳回主页";
			sleep(1);
			header("/1");
		}
		public function index() {
			echo "sdf";
		}
	}