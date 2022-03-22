<?php
	if($_POST["submit"]){
		$image_url = $_POST["image_url"];

		function cURL_scrape_image($image_url){
			header("Content-Type: image/jpeg");

			$url = $image_url;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
			$res = curl_exec($ch);
			$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
			curl_close($ch) ;
			
			echo $res;
		}

		cURL_scrape_image($image_url);
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Scrape</title>

	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<!-- Bootstrap CDNs -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

	<!-- Other CDNs -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/v4-shims.css">
</head>
<body>
	<form method="POST" enctype="multipart/form-data">
		<div class="container mt-5">
			<div class="row">
				<div class="col-md-6 offset-md-3">
					<div class="form-group">
						<label for="image_url">Place an image URL</label>
						<input class="form-control mb-3" type="url" name="image_url" placeholder="Place an image URL here..." />

						<input class="btn btn-primary btn-block" type="submit" name="submit" value="Submit" />
					</div>
				</div>
			</div>
		</div>
	</form>
</body>
</html>