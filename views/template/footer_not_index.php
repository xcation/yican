<div class='footer_not_index'>
	<div class='footer-line-one'></div>
	<div class="footer-line-two">
		<div class='footer_container black_a'>
			<?php
				foreach ($footer as $row) { ?>
				<a href='<?=$row['link_href']?>'><?=$row['label_name']?></a>
			<?php
			} ?>
			<!-- <span class="bottom-logo"></span> -->
		</div>
		<!-- <div class='uki'>4 Uki</div> -->
	</div>
</div>
</body>
</html>