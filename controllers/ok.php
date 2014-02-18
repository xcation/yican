<?php
	/**
	 * test for weixin
	 */
	class Ok extends CI_controller {
		function index() {
		$xml = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><FromUserName><![CDATA[FromUser]]></FromUserName><CreateTime>123456789</CreateTime><MsgType><![CDATA[event]]></MsgType><Event><![CDATA[unsubscribe]]></Event><EventKey><![CDATA[start_order]]></EventKey></xml>";

			$header[]="Content-Type: text/xml; charset=utf-8";
			$header[]="User-Agent: nginx/1.0.0";
			$header[]="Host: 127.0.0.1";
			$header[]="Accept: text/html, image/gif, image/jpeg, *; q=.2, */*; q=.2";
			$header[]="Connection: keep-alive";
			$header[]="Content-Length: ".strlen($xml);

			// $url = "http://{$_SERVER['HTTP_HOST']}".dirname($_SERVER['PHP_SELF']).‘/response.php’;
			$url = "http://localhost/wx";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$res = curl_exec($ch);
			curl_close($ch);

			header('Content-Type:text/html; charset=utf-8');
			echo ($res);
		}
	}
?>