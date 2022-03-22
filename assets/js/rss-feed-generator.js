function getFeed(user_id, content_id, feedURL, db_prefix){
    var fetchFeed = 5;
    var apiKey = "yn2ss3ukow2vi8pzvgqq9xmau5j8yx2o6stctm0l";
    if(feedURL == ""){
        alert("Please Enter Feed URL");
    }else if(fetchFeed == ""){
        alert("Please Enter Number of Feed to Fetch");
    }else{
        $.ajax({
                url: 'https://api.rss2json.com/v1/api.json',
                method: 'GET',
                dataType: 'json',
                data: {
                    rss_url: feedURL,
                    api_key: apiKey, // put your api key here
                    count: fetchFeed
                }
        }).done(function (response) {
            console.log( response )
            if(response.status != 'ok'){ throw response.message; }
        
            console.log('====== ' + response.feed.title + ' ======');
        
            for(var i in response.items){
                var item = response.items[i];
                $.ajax({
    				url: "../../inc/user/save_rss.php",
    				type: "POST",
    				data: {
    					user_id             : user_id,
    					content_id          : content_id,
    					news_link           : item.link,
    					news_image          : response.feed.image,
    					news_thumbnail      : item.thumbnail,
    					news_published_date : item.pubDate,
    					news_title          : item.title,
    					news_author         : item.author,
    					news_content        : item.content,
    					news_description    : item.description,
    					db_prefix           : db_prefix				
    				},
    				cache: false,
    				success: function(dataResult){
    				    console.log( dataResult )
    				},
    				error: function(XMLHttpRequest, textStatus, errorThrown) { 
                        console.log("Status: " + textStatus); alert("Error: " + errorThrown); 
                    }  
    			});
                console.log(item.title);
            }
        });
    }
}

function download( campaignPath, filename ){
    alert("test");
    $.ajax({
		url: "../../inc/user/download_zip.php",
		type: "POST",
		data: {
			campaignPath : campaignPath,
			filename     : filename
		},
		cache: false,
		success: function(dataResult){
		    var dataResult = JSON.parse(dataResult);
		    console.log(dataResult)
			if(dataResult.statusCode==200){
			   // alert(dataResult); 		
			   //window.location = "index.php?cmd=content"
			}
			else if(dataResult.statusCode==201){
			   //alert(dataResult);
			}
		}
	});
}