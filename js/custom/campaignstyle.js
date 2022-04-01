// $("#post-column").change(function(){
    //     let selected = $(this).val()
    //     let news_classname = $(".news-column").attr("class");
    //     let classname = news_classname.replace("news-column pb-4", "");
    //     $(".news-column").removeClass(classname);
    //     $(".news-column").addClass("col-md-"+selected);
    // })
    
    // HIDE AND SHOW OF BUTTON LOAD MORE
    $("#load-more").change(function(){
        let selected = $(this).val()
        if( selected === "true" ) {
            $(".load-more-container button").removeClass("d-none");
        } else {
            $(".load-more-container button").addClass("d-none");
        }
    })
    
    // HIDE AND SHOW CUSTOM FILTER AT THE TOP
    $("#show-custom-filter").change(function(){
        let selected = $(this).val()
        if( selected === "true" ) {
            $(".filter").removeClass("d-none");
        } else {
            $(".filter").addClass("d-none");
        }
    })
    
    // HIDE AND SHOW NETWORK FILTER AT THE TOP
    $("#show-network-filter").change(function(){
        let selected = $(this).val()
        if( selected === "true" ) {
            $(".networks").removeClass("d-none");
        } else {
            $(".networks").addClass("d-none");
        }
    })
    
    // ACTION FOR APPLYING THE TEMPLATE
    $("#faq4 .btn-apply").on('click', function(){
        // alert("Template applied");
    })
    
    // CONVERTING THE DATE AND TIME
    $(".news-author-container .date-posted").map(function(index, item) {
        item.innerText = moment(item.innerText).fromNow();
    });
    

    let editModal = $("#edit-modal");
    let editForm = $("#edit-form");
    let alertModal = $("#ajax-alert");
    let alertTimeout = null;
    
    // Fields
    const authorField = editModal.find("#name");
    const titleField = editModal.find("#title");
    const descriptionField = editModal.find("#description");
    
    // Preview Card
    const newsImage = editModal.find(".news-image-container img");
    const newsTitle = editModal.find(".news-heading-container h5");
    const newsDescription = editModal.find(".news-detail-container p");
    const authorImage = editModal.find(".news-author-container .autor-name img");
    const authorName = editModal.find(".news-author-container .autor-name span");
    const newsDate = editModal.find(".news-author-container .date-posted");
    
    const fieldArray = [
        authorField,
        titleField,
        descriptionField
    ];
    
    const previewArray= [
        authorName,
        newsTitle,
        newsDescription
    ];
    
    fieldArray.map((field, index) => {
       field.on('keyup', function () {
           previewArray[index].text(field.val());
       }) 
    });
    
    function editNews(news) {

        let data = JSON.parse(news.getAttribute('data-json'));
        
        if (!data || !data.news_id) {
            return false;
        }
        
        editModal.find("#news_id").val(data.news_id);
        authorField.val(data.news_author);
        titleField.val(data.news_title);
        descriptionField.val(data.news_description);
        
        newsImage.attr("src", data.post_image);
        newsTitle.text(data.news_title);
        newsDescription.text(data.news_description);
        authorImage.attr("src", data.user_image);
        authorName.text(data.news_author);
        newsDate.text(moment(data.created_at).fromNow());
        editModal.modal().show();
    }
    
    function loadMore(news) {
        // alert("Under Development");
        // return false;
    }
    
    function showAlert(alert, message, success = true) {
        alertModal.removeClass('alert-danger alert-success');
        alertModal.find('.ajax-alert-message').text(message);
        
        if (success) {
            alertModal.addClass('alert-success'); 
        } else {
            alertModal.addClass('alert-danger'); 
        }
        
        alert.addClass('show');
        
        alertTimeout = setTimeout(function() {
            alert.removeClass('show');
        }, 8000);
    }
    

    function updateData(news) {
        let newsContainer = $('#news-' + news.news_id);
        
        if (newsContainer) {
            let upload_dir = "<?php echo $upload_dir; ?>";
            
            if (news.post_image) {
                newsContainer.find('.news-image-container img').attr("src", upload_dir + '/images/' + news.post_image);
            }
            
            if (news.user_image) {
                newsContainer.find('.news-author-container .autor-name img').attr("src", upload_dir + '/avatar/' + news.user_image);
            }
            
            newsContainer.find('.news-heading-container h5').text(news.title);
            newsContainer.find('.news-detail-container p').text(news.description);
            newsContainer.find('.news-author-container .autor-name span').text(news.name);
        }
    }
    
    // ACTION FOR CLOSE ALERT MODAL
    alertModal.find('.close').on('click', function () {
        if (alertTimeout) {
            clearTimeout(alertTimeout);
        }
        alertModal.removeClass('show'); 
    });
    
    // ACTION FOR CLOSING THE MODAL
    editModal.on('hidden.bs.modal', function () {
        editModal.find("#news_id").val("");
        editModal.find("#name").val("");
        editModal.find("#description").val("");
        editModal.find("#title").val("");
    });
    
    // ACTION FOR SUBMITING MODAL
    editModal.on('submit', function (evt) {
        evt.preventDefault();
        
        let formData = new FormData();
        formData.append('action', "edit");
        formData.append('user_id', editModal.find("#user_id").val());
        formData.append('news_id', editModal.find("#news_id").val());
        formData.append('news_author', editModal.find("#name").val());
        formData.append('news_title', editModal.find("#title").val());
        formData.append('news_description', editModal.find("#description").val());
        formData.append('user_image', editModal.find("#user_image").get(0).files[0]);
        formData.append('image', editModal.find("#image").get(0).files[0]);
        formData.append('video_url', editModal.find("#video_url").val());
        
        $.ajax({
            url: "/api/news.php",
            method: "POST",
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                editModal.modal('hide');
                showAlert(alertModal, response.message, response.success);
                updateData(response.data);
            },
            error: function (response) {
                showAlert(alertModal, response.responseJSON.message, response.responseJSON.success);
            }
        });
    });
    
    // ACTION FOR CHANGING STATUS
    $(".btn-status").on('click', function (evt) {
        evt.preventDefault();
        
        const news_id = $(this).data("news-id");
        const action = $(this).data("action");
        
        var container = $($(this).parent().parent()[0]);
        var rejectBtn = $(this).next();
        var rejectBtn1 = $(this).prev();
        var currentBtn = $(this);
        
        $.ajax({
            url: "/api/news_status.php",
            method: "POST",
            dataType: "json",
            data: {
                action: action,
                news_id
            },
            success: function (response) {
                if( response.success ) {
                    if( action == "approved" ) {
                        container.removeClass("blur rejected-border")
                        rejectBtn.removeClass("reject-color")
                        currentBtn.addClass("approve-color")
                    } else if( action == "rejected" ) {
                        container.addClass("blur rejected-border")
                        currentBtn.addClass("reject-color")
                        currentBtn.prev().removeClass("approve-color")
                    } else if( action == "pin" ) {
                        window.location.reload();
                    }
                }
                // TO DO - Update Success
                console.log(response);
            },
            error: function (err) {
                console.log(err)
            }
        });
    });
    
    window.onload = function(){
        // FOR USER AVATAR
		var user_image = document.getElementById("user_image");
		var user_image_preview = document.getElementById("user-image-preview");

		function readFile1(input){

			if(input.files && input.files[0]){
				var file_reader = new FileReader();

				file_reader.onload = function(e){
					user_image_preview.src = e.target.result;
				}

				file_reader.readAsDataURL(input.files[0]);
			}
		}

		user_image.oninput = function(){
			readFile1(this);
		}
		
        // FOR NEWS CONTENT IMAGE
        var news_image = document.getElementById("image");
		var news_image_preview = document.getElementById("news-image-preview");

		function readFile2(input){

			if(input.files && input.files[0]){
				var file_reader = new FileReader();

				file_reader.onload = function(e){
					news_image_preview.src = e.target.result;
				}

				file_reader.readAsDataURL(input.files[0]);
			}
		}

		news_image.oninput = function(){
			readFile2(this);
		}
	}

    var textColor;
    var defaultTextColor = "#000000";

    window.addEventListener("load", startTextChange, false);

    function startTextChange() {
        textColor = document.querySelector("#textColor");
        textColor.value = defaultTextColor;
        textColor.addEventListener("change", updateTextColor, false);
        textColor.select();
    }

    function updateTextColor(event) {
        document.querySelectorAll("p").forEach(function(p) {
            p.style.color = event.target.value;
        });
        document.querySelectorAll("h5").forEach(function(h5) {
            h5.style.color = event.target.value;
        });
    }

    var borderColor;
    var defaultBorderColor = "#000000";

    window.addEventListener("load", startBorderChange, false);

    function startBorderChange() {
        borderColor = document.querySelector("#borderColor");
        borderColor.value = defaultBorderColor;
        borderColor.addEventListener("change", updateBorderColor, false);
        borderColor.select();
    }

    function updateBorderColor(event) {
        document.querySelectorAll(".news-container").forEach(function(newscontainer) {
            newscontainer.style.borderColor = event.target.value;
        });
    }

    var bgColor;
    var defaultBgColor = "#000000";

    window.addEventListener("load", startBgChange, false);

    function startBgChange() {
        bgColor = document.querySelector("#bgColor");
        bgColor.value = defaultBgColor;
        bgColor.addEventListener("change", updateBgColor, false);
        bgColor.select();
    }

    function updateBgColor(event) {
        document.querySelectorAll(".news-container").forEach(function(newscontainer) {
            newscontainer.style.backgroundColor = event.target.value;
        });
    }

    var feedColor;
    var defaultFeedColor = "#000000";

    window.addEventListener("load", startFeedChange, false);

    function startFeedChange() {
        feedColor = document.querySelector("#feedColor");
        feedColor.value = defaultFeedColor;
        feedColor.addEventListener("change", updateFeedColor, false);
        feedColor.select();
    }

    function updateFeedColor(event) {
        var cardBody = document.querySelector("#cardBody");

        if (cardBody) {
            cardBody.style.backgroundColor = event.target.value;
        }
    }

    function resetColors() {
        var defaultBgColor = "#000000";

        document.querySelectorAll("p").forEach(function(p) {
            p.style.color = '';
        });
        document.querySelectorAll("h5").forEach(function(h5) {
            h5.style.color = '';
        });
        document.querySelectorAll(".news-container").forEach(function(newscontainer) {
            newscontainer.style.backgroundColor = '';
            newscontainer.style.borderColor = '';
        });
        var cardBody = document.querySelector("#cardBody");

        if (cardBody) {
            cardBody.style.backgroundColor = '';
        }

        document.querySelector("#textColor").value = defaultBgColor;
        document.querySelector("#borderColor").value = defaultBgColor;
        document.querySelector("#bgColor").value = defaultBgColor;
        document.querySelector("#feedColor").value = defaultBgColor;
    }

    // <option value="12">1</option>
    // <option value="6">2</option>
    // <option value="4">3</option>
    // <option value="3">4</option>
    // <option value="2">6</option>
    // <option value="1">12</option>

    // document.addEventListener("change", () => {
    //     var c = document.getElementById("post-column");
    //     var pCol = c.value;

    //     console.log(typeof pCol);

    //     switch (pCol) {
    //         case '12': {
    //             document.querySelectorAll(".selectPostCol").forEach(function(selectPostCol) {
    //                 selectPostCol.classList.remove('col-md-1');
    //                 selectPostCol.classList.remove('col-md-2');
    //                 selectPostCol.classList.remove('col-md-3');
    //                 selectPostCol.classList.remove('col-md-4');
    //                 selectPostCol.classList.remove('col-md-6');
    //                 selectPostCol.classList.remove('col-md-12');
    //                 selectPostCol.classList.add('col-md-12');
    //                 console.log(selectPostCol.getAttribute("class"));
    //             });
    //         }
    //             break;
    //         case '6': {
    //             document.querySelectorAll(".selectPostCol").forEach(function(selectPostCol) {
    //                 selectPostCol.classList.remove('col-md-1');
    //                 selectPostCol.classList.remove('col-md-2');
    //                 selectPostCol.classList.remove('col-md-3');
    //                 selectPostCol.classList.remove('col-md-4');
    //                 selectPostCol.classList.remove('col-md-6');
    //                 selectPostCol.classList.remove('col-md-12');
    //                 selectPostCol.classList.add('col-md-6');
    //                 console.log(selectPostCol.getAttribute("class"));
    //             });
    //         }   
    //             break;         
    //         case '4': {
    //             document.querySelectorAll(".selectPostCol").forEach(function(selectPostCol) {
    //                 selectPostCol.classList.remove('col-md-1');
    //                 selectPostCol.classList.remove('col-md-2');
    //                 selectPostCol.classList.remove('col-md-3');
    //                 selectPostCol.classList.remove('col-md-4');
    //                 selectPostCol.classList.remove('col-md-6');
    //                 selectPostCol.classList.remove('col-md-12');
    //                 selectPostCol.classList.add('col-md-4');
    //                 console.log(selectPostCol.getAttribute("class"));
    //             });
    //         }     
    //             break;       
    //         case '3': {
    //             document.querySelectorAll(".selectPostCol").forEach(function(selectPostCol) {
    //                 selectPostCol.classList.remove('col-md-1');
    //                 selectPostCol.classList.remove('col-md-2');
    //                 selectPostCol.classList.remove('col-md-3');
    //                 selectPostCol.classList.remove('col-md-4');
    //                 selectPostCol.classList.remove('col-md-6');
    //                 selectPostCol.classList.remove('col-md-12');
    //                 selectPostCol.classList.add('col-md-3');
    //                 console.log(selectPostCol.getAttribute("class"));
    //             });
    //         }
    //             break;
    //         case '2': {
    //             document.querySelectorAll(".selectPostCol").forEach(function(selectPostCol) {
    //                 selectPostCol.classList.remove('col-md-1');
    //                 selectPostCol.classList.remove('col-md-2');
    //                 selectPostCol.classList.remove('col-md-3');
    //                 selectPostCol.classList.remove('col-md-4');
    //                 selectPostCol.classList.remove('col-md-6');
    //                 selectPostCol.classList.remove('col-md-12');
    //                 selectPostCol.classList.add('col-md-2');
    //                 console.log(selectPostCol.getAttribute("class"));
    //             });
    //         }
    //             break;
    //         case '1': {
    //             document.querySelectorAll(".selectPostCol").forEach(function(selectPostCol) {
    //                 selectPostCol.classList.remove('col-md-1');
    //                 selectPostCol.classList.remove('col-md-2');
    //                 selectPostCol.classList.remove('col-md-3');
    //                 selectPostCol.classList.remove('col-md-4');
    //                 selectPostCol.classList.remove('col-md-6');
    //                 selectPostCol.classList.remove('col-md-12');
    //                 selectPostCol.classList.add('col-md-1');
    //                 console.log(selectPostCol.getAttribute("class"));
    //             });
    //         }
    //             break;
        
    //         default: 
    //             break;
    //     }
    // });