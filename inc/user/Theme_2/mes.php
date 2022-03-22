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