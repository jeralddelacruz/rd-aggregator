
			<div class="row">
				<div class="col-md-12 text-center"><?php echo get_ad(3);?></div>
			</div>
		</div>
		</div>
		<footer class="footer">
		<div class="container-fluid">
<?php
$res=$DB->query("select * from $dbprefix"."page where page_bmenu='1' and page_pack like '%;".$_SESSION["PackID"].";%' order by page_order");
if(sizeof($res)){
?>
			<nav class="pull-left">
			<ul>
<?php
	foreach($res as $row){
?>
				<li><a href="index.php?cmd=page&id=<?php echo $row["page_id"];?>"><?php echo $row["page_title"];?></a></li>
<?php
	}
?>
			</ul>
			</nav>
<?php
}
?>
			<p class="copyright pull-right">&copy; <?php echo date("Y")." ".$WEBSITE["sitename"];?>. All rights reserved.</p>
		</div>
		</footer>
	</div>
</div>
<script>
jQuery(document).ready(function($){
	$('[data-toggle="tooltip"]').tooltip();

	function get_mes(){
		$.ajax({
			url: "mes.php",
			type: "POST",
			data: {"id":<?php echo $UserID;?>},
			cache: false,
			success: function(response){
				res=response.split("|");
				var mes_num=res[0];
				var mes_body=res[1];
				$("#mes_num").text(mes_num);
				$("#mes_body").html(mes_body);
				if(mes_num>0){
					$("#mes_num").removeClass("hide");
				}
				else{
					$("#mes_num").addClass("hide");
				}
				setTimeout(get_mes,30000);
			}
		});
	}
	get_mes();
});
</script>
</body>
</html>