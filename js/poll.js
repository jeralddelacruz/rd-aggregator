jQuery(function($){
$("#vote_btn").click(function(){
	var ans=$(".ans:checked").val();

	if(ans){
		$.ajax({
			url: "poll.php",
			type: "GET",
			data: {"ans": ans},
			cache: false,
			success: function(response){
				$("#poll_vote").hide();
				$("#poll_res").fadeIn(600);
				$("#poll").append(response);
			}
		});
	}
	else{
		alert("Please choose an Answer!");
	}

	return false;
});
});