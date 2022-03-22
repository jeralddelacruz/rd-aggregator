<script>
    jQuery(document).ready(function($){
        $('[data-toggle="tooltip"]').tooltip();
        let themeId = "<?php echo $THEME[0][0]; ?>";

        function get_mes(){
            $.ajax({
                url: "mes.php",
                type: "POST",
                data: {"id":<?php echo $UserID;?>, "theme": themeId},
                cache: false,
                success: function(response){
                    res=response.split("|");
                    
                    var mes_num=res[0];
                    var mes_body= themeId == 2 ? `<i class="zmdi zmdi-notifications"></i>
                                    <div class="notifi-dropdown js-dropdown">
                                        <div class="notifi__title">
                                            <p>Notifications</p>
                                        </div>
                                        
                                        ${res[1]}
                                    </div>` : res[1];

                    $("#mes_num").text(mes_num);
                    $("#mes_body").html(mes_body);
                    
                    if(mes_num>0){
                        $('#mes_body').addClass('has-noti');
                        $("#mes_num").removeClass("hide");
                    }
                    else{
                        $('#mes_body').removeClass('has-noti');
                        $("#mes_num").addClass("hide");
                    }
                    
                    setTimeout(get_mes,30000);
                }
            });
        }
        get_mes();
    });
</script>