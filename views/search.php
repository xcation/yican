<?php
function save_digits($num) {
	echo sprintf("%.1f", $num);
}
?>
<div class='main'>
	<div class='thin_container black blank'>
		<?php
		if (@$error) { ?>
			<div class='blank'>
				<?=$error?>
			</div>
		<?php
		} 
		else { ?>
			<div class='search_res'>在<?=$university_full?>找到的结果</div>
			<?php
			if ($restaurant) { ?>
				<div>
					<div class='search_res_t'>找到的餐厅</div>
					<?php
					foreach($restaurant as $rest) { ?>
						<div class='res'>
							<span class='black_a search_name'>
								<a href="/store/<?=$university_id?>/<?=$rest['store_id']?>"><?=$rest['store_name']?>
								</a>
							</span>
							<span class='search_wid'><?=$rest['state']?></span>
							<span class='search_wid'>月销量：<?=$rest['total_buyer_month']?></span>
							<span class='search_wid'>评价：<?php save_digits($rest['avg_score_month']); ?></span>
							<span class='search_wid'>起送价：<?=$rest['delivery_cost']?></span>
						</div>
					<?php
					} ?>
				</div>
			<?php
			}
			else if ($food) { ?>
				<div>
					<div class='search_res_t'>找到的美食</div>
					<?php
					$store_one = -1;
					foreach($food as $fo) { 
						if ($fo['store_id'] != $store_one) {
							$store_one = $fo['store_id'];
						?>
							<h4>在<?=$fo['store_name']?>找到的食物：</h4>
						<?php 
						} ?>
						<div class='res'>
							<span class='black_a search_name'>
								<a href="/store/<?=$university_id?>/<?=$fo['store_id']?>/#food-<?=$fo['food_id']?>">
									<?=$fo['food_name']?>
								</a>
							</span>
								<span class='search_wid'>价格：<?=$fo['price']?></span>
								<span class='search_wid'>月销量：<?=$fo['total_buyer_month']?></span>
								<span class='search_wid'>评价：<?php save_digits($fo['avg_score_month']); ?></span>
						</div>
					<?php
					}  ?>
				</div>
		<?php
			}
			else { ?>
				<div>没有相应的结果</div>
			<?php
			}
		} ?>

	</div>
</div>