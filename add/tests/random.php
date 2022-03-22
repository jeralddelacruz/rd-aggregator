<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<a href="https://google.com">Google</a>
	<div id="result"></div>
	<script type="text/javascript">
		if(typeof(Storage) !== "undefined"){
			localStorage.setItem("name", "Itlog");
			document.getElementById("result").innerHTML = localStorage.getItem("name");
		}
		else{
			document.getElementById("result").innerHTML = "Sorry, your browser does not support Web Storage...";
		}

		window.onbeforeunload = function(){
			localStorage.clear();
			return "Cleared";
		}
	</script>
</body>
</html>