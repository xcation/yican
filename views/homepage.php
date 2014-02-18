<div class="main">
	<div class="head_container">
		<table class="region">
			<tbody>
				<tr>
				<?php
					foreach ($schoolInfo as $school_row) {
							$univ_short_name = @$school_row['univ_short_name']; ?>
							<td class="university">
								<a class='univ_hover' <?php
									if (@$school_row['region_input']) ;
									else
										echo "href='university/{$univ_short_name}'"; ?>>
									<img src="/img/region/<?=$school_row['imgLoc']?>" class="hover
									<?php if (@$school_row['region_input']) {
										echo 'region_input';?>"
									<?php
										echo 'region="'. $school_row['region_id']. '"'; } ?> <?php if (@$school_row['region_input']) {
											;
										} else { ?>
											id="<?=$school_row['univ_short_name'] ?>" onclick="$.switchLoc(this)"
										<?php } ?> />
								</a>
							</td>
				<?php  } ?>

				</tr>
			</tbody>
		</table>
	</div>
	<div id='ciber-search' style="display: none">
		<div class="ciber-wrapper">
			<header class="map-header">
				<a id="em_close" class="ciber-back" title="返回"></a>
				<div id="em_searchWrapper" class="search-wrapper">
					<input type="text" id="net-search" class="ui-autocomplete-input ciber-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" placeholder="输入学校，网吧名称确定位置">

					<!-- <input id="em_searchInput" class="search-input ui-autocomplete-input ui-autocomplete-loading"  type="text" > -->
						<!-- <span role="status" aria-live="polite" class="ui-helper-hidden-accessible">10 results are available, use up and down arrow keys to navigate.</span> -->
						<!-- <a id="em_searchButton" class="ciber-search-btn"></a> -->
				</div>
			</header>
		</div>
	</div>

</div>

	<script type="text/javascript">
		$.switchLoc=function(obj){
			$.cookie('location', obj.id, {path:'/', expire: 30});
			window.location.href='/university/'+obj.id;
			// alert(obj.id);
		}
		$.switchLocU=function(obj){
			$.cookie('location', $(obj).attr('univ'), {path:'/', expire: 30});
			window.location.href='/university/'+obj.id;
			// alert(obj.id);
		}

	</script>

<script src="/js/jquery.ui.core.js"></script>
<script src="/js/jquery.ui.widget.js"></script>
<script src="/js/jquery.ui.position.js"></script>
<script src="/js/jquery.ui.autocomplete.js"></script>
<script type="text/javascript">
$('.region_input').click(function() {
	var t = $(this).attr('region');
	$('#ciber-search').css("display", "block");
	$('#net-search').attr('region', t);
})
$('#em_close').click(function() {
	$('#ciber-search').css("display", "none");
})
$(function() {
	var data = new Array();
	<?php
	foreach (@$region_info as $row) {
		echo "data[".$row['region_id'] .']=[';

		foreach ($row['region_detail'] as $key => $region_detail) {
			if ($key > 0)
				echo ",\n";
			$full_name = $region_detail['univ_full_name'];
			$short_name = $region_detail['univ_short_name'];
			$imgLoc = $region_detail['imgLoc'];
			echo '{"full_name":"' . $full_name .'", "short_name":"' . $short_name. '", "imgLoc": "'.$imgLoc.'"}';
		}
		echo "];";
	} ?>
	$( "#net-search" ).autocomplete(
	{
		source: function(req, add) {
			var t = $('#net-search').attr('region');
			add(data[t]);
		},
		select: function( event, ui ) {
			$( "#net-search" ).val( ui.item.full_name + " / " + ui.item.short_name + " / ");
			return false;
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li></li>" )
			.data( "item.autocomplete", item )
			.append( "<a onclick='$.switchLocU(this)' univ='" + item.short_name + "'><strong>" + item.full_name + "</strong> / " + "<img src='/img/univ/"+item.imgLoc+"' style='float: right; height: 28px;'></img></a>" )
			.appendTo( ul );
		};
	});
		// {"label":"Arwen", "actor":"Liv Tyler"},
		// {"label":"Bilbo Baggins", "actor":"Ian Holm"},
		// {"label":"Boromir", "actor":"Sean Bean"},
		// {"label":"Frodo Baggins", "actor":"Elijah Wood"},
		// {"label":"Gandalf", "actor":"Ian McKellen"},
		// {"label":"Gimli", "actor":"John Rhys-Davies"},
		// {"label":"Gollum", "actor":"Andy Serkis"},
		// {"label":"Legolas", "actor":"Orlando Bloom"},
		// {"label":"Meriadoc Merry Brandybuck", "actor":"Dominic Monaghan"},
		// {"label":"Peregrin Pippin Took", "actor":"Billy Boyd"},
		// {"label":"Samwise Gamgee", "actor":"Sean Astin"}
		// ];
	// $(function() {
	//     $("#net-search").autocomplete({
	//         source: "/ajax/ciber_autocomplete",
	//         minLength: 1,
	//         select: function(event, ui) {
	//             var url = ui.item.id;
	//             if(url != '#') {
	//                 location.href = '/blog/' + url;
	//             }
	//         },
	//  
	// //         html: true, // optional (jquery.ui.autocomplete.html.js required)
	//       // optional (if other layers overlap autocomplete list)
	//         open: function(event, ui) {
	//             $(".ui-autocomplete").css("z-index", 1000);
	//         }
	//     });
	//  
	// });

</script>

