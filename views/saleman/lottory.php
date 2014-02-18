<script type="text/javascript" src="/js/rooter/jQueryRotate.2.2.js"></script>
<script type="text/javascript" src="/js/rooter/jquery.easing.min.js"></script>
<script type="text/javascript">
$(function(){
	 $("#startbtn").click(function(){
		lottery();
	});
});
function lottery(){
	$.ajax({
		type: 'POST',
		url: '/saleman/lottory',
		dataType: 'json',
		cache: false,
		data:{again: 1, posibility: <?php if (@$first_posibility) echo $first_posibility; else echo -1; ?>},
		error: function(){
			alert('出错了！');
			return false;
		},
		success:function(json){
			var e = json.err;
			if (e == 1) {
				alert("未输入抽奖概率");
				return;
			}
			$("#startbtn").unbind('click').css("cursor","default");
			var a = json.angle;
			var p = json.prize;
			$("#startbtn").rotate({
				duration:3000,
				angle: 0,
            	animateTo:1800+a,
				easing: $.easing.easeOutSine,
				callback: function(){
					var con = confirm('恭喜你，中得'+p+'\n还要再来一次吗？');
					if(con){
						lottery();
					}else{
						return false;
					}
				}
			});
		}
	});
}
</script>
<h4>抽奖啦！</h4>
<h6>注：输入概率后方可开始抽奖, 二三四等奖为参赛奖，五六七为谢谢参与,概率分别为除去一等奖后的平均</h6>
<form action='/saleman/lottory' method='post'>
	请输入一等奖的概率:<input type='text' name='first_posibility'/>
	<input type='submit' value='提交'/>
</form>
<h6>现在的一等奖的概率: <?php if (@$first_posibility) echo $first_posibility; else echo "未设置！"; ?></h6>
<div class="demo" style="width: 417px;height: 417px;position: relative;margin: 50px auto;">
	<div id="disk" style="width: 417px;height: 417px;background: url(/img/rooter/disk.jpg) no-repeat;"></div>
	<div id="start" style="width: 163px;height: 320px;position: absolute;top: 46px;left: 130px;">
		<img src="/img/rooter/start.png" id="startbtn" style="cursor: pointer; -webkit-transform: rotate(68deg);">
	</div>
</div>