<?php
	class Header {
		public function set_header(
						    $title = '一餐易餐',
							$keyword = '一餐易餐 yicanyican 宁波大学生外卖服务',
						    $description = 'yicanyican.com提供优质的外卖信息',
						    $css = '/css/2013112502.css',
						    $base = '/',
						    $icon_loc = '/img/yi.ico') {

			$headInfo['css'] = $css;
			$headInfo['icon_loc'] = $icon_loc;
			$headInfo['keyword'] = $keyword;
			$headInfo['description'] = $description;
			$headInfo['title'] = $title;
			return $headInfo;
		}
	}
?>
