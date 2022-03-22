
			</div>
		</div>
	</div>
</div>
	<footer>
		Copyright &copy;<?php echo date("Y")." ".$WEBSITE["sitename"];?>. All rights reserved.
	</footer>
<script>
jQuery(function($){
	$(".help").tooltip({tooltipClass:"tooltip_l",position:{my:"left top+25",at:"left bottom"},track:true,show:{effect:"slideDown"},content:function(){return this.getAttribute("title");}});
	$(".tip").tooltip({tooltipClass:"tooltip_l",position:{my:"left+15 bottom",at:"right bottom"},track:true,show:{effect:"slideDown"},content:function(){return this.getAttribute("title");}});
	$(".color").spectrum({showInitial:true,showInput:true,preferredFormat:"hex"});
});
</script>

<!-- BOOSTRAP JS CDN FOR FUTURE UI UPGRADE -->
<!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script> -->
</body>
</html>