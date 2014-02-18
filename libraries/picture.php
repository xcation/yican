<?php
	class Picture {
		function delete_store_pic($fullpath) {
			if (file_exists($fullpath))
				unlink($fullpath);
		}
		function upload_img($file_type) {
			$re['state'] = false;
			if (count($_FILES) == 0)
				$re['error'] = "空图片文件";
			foreach ($_FILES as $key=>$row) {
				$picname = $row['name'];
				$type = strstr($picname, '.');
				if ($type != '.gif' && 
					$type != '.jpg' &&
					$type != '.png') {

					$re['error']="图片格式不对";
				}
				else {
					$rand = rand(100, 999);
        			$pics_loc = date("YmdHis") . $rand . $type;
					move_uploaded_file($row['tmp_name'], LOC_PREFIC."/img/{$file_type}/".$pics_loc);
					$re['state'] = true;
					$re['error'] = $pics_loc;
				}
			}
			return $re;
		}

		function compress($src_img, $exten, $dst_w = 170, $dst_h = 120) {
			list($src_w,$src_h)=@getimagesize($src_img);  // 获取原图尺寸

			$dst_scale = $dst_h/$dst_w; //目标图像长宽比
			$src_scale = $src_h/$src_w; // 原图长宽比
			if($src_scale>=$dst_scale){  // 过高
				$w = intval($src_w);
				$h = intval($dst_scale*$w);
				$x = 0;
				$y = ($src_h - $h)/3;
			}
			else{ // 过宽
				$h = intval($src_h);
				$w = intval($h/$dst_scale);
				$x = ($src_w - $w)/2;
				$y = 0;
			}
			// echo $exten;
			switch($exten) {
				case '.jpg':
				case '.jpeg':
					$source=imagecreatefromjpeg($src_img);
					break;
				case '.png':
					$source=imagecreatefrompng($src_img);
					break;
				case '.gif':
					$source=imagecreatefromgif($src_img);
					break;
				default:
					return;
			}
			$croped=imagecreatetruecolor($w, $h);
			imagecopy($croped,$source,0,0,$x,$y,$src_w,$src_h);
			$scale = $dst_w/$w;
			$target = imagecreatetruecolor($dst_w, $dst_h);
			$final_w = intval($w*$scale);
			$final_h = intval($h*$scale);
			imagecopyresampled($target,$croped,0,0,0,0,$final_w,$final_h,$w,$h);
			$timestamp = time();
			switch($exten) {
				case '.jpg':
				case '.jpeg':
					imagejpeg($target, $src_img, 80);
					break;
				case '.png':
					imagepng($target, $src_img, 8);
					break;
				case '.gif':
					imagegif($target, $src_img, 80);
					break;
				default:
					return;
			}
			imagedestroy($target);
		}

		function gen_sale_count_pic($x, $y, $y_name, $title) {
			require_once (LOC_PREFIC.'/jpgraph3/jpgraph.php');
			require_once (LOC_PREFIC.'/jpgraph3/jpgraph_line.php');
			require_once (LOC_PREFIC."/jpgraph3/jpgraph_error.php");
			$a = $x;
						
			$max = -1;
			$min = 99999;
			foreach ($y as $row) {
				$datay[] = $row[$y_name]; //填充的数据
				if ($row[$y_name] > $max)
					$max = $row[$y_name];
				if ($row[$y_name] < $min)
					$min = $row[$y_name];
			}


			// $diff = $max - $min;
			// $diff /= 10;
			// for ($i = $min; $i <= $max; $i += $diff)
			// 	$y_tick[] = $i;

			$this->delete_store_pic($title['path']);

			$graph = new Graph(800,200,"auto");
			$graph->img->SetMargin(35,35,35,35);    
			$graph->img->SetAntiAliasing();
			$graph->SetScale("textlin");
			$graph->SetShadow();
			$graph->title->Set($title['title']);
			$graph->xaxis->title->Set($title['x_note']);
			$graph->xaxis->title->SetFont(FF_SIMSUN,FS_BOLD);
			$graph->yaxis->title->Set($title['y_note']);
			$graph->SetMarginColor("lightblue");
			$graph->yaxis->title->SetFont(FF_SIMSUN,FS_BOLD);
			$graph->title->SetFont(FF_SIMSUN,FS_BOLD);
			$graph->xaxis->SetPos("min");
			$graph->yaxis->HideZeroLabel();
			$graph->ygrid->SetFill(true,'#EFEFEF@0.5','#BBCCFF@0.5');

			$graph->xaxis->SetTickLabels($a);
			// $graph->yaxis->SetTickLabels($y_tick);
			$graph->xaxis->SetFont(FF_SIMSUN); 
			$graph->yscale->SetGrace(20);        
			$p1 = new LinePlot($datay);
			$p1->mark->SetType(MARK_FILLEDCIRCLE);
			$p1->mark->SetFillColor("red");
			$p1->mark->SetWidth(4);
			$p1->SetColor("blue");
			$p1->SetCenter();
			$graph->Add($p1);
			$p1->value->Show();
			$graph->Stroke($title['path']);
			// var_dump($y);
		}


	}
?>