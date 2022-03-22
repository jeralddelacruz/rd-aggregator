
<!-- From original -->
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script src="../js/jquery-ui-timepicker-addon.min.js"></script>
<script src="../assets/js/bootstrap-checkbox-radio-switch.js"></script>
<script src="../assets/js/bootstrap-notify.js"></script>
<script src="../js/fancybox/jquery.fancybox.js"></script>
<script src="../js/spectrum.js"></script>

<!-- Bootstrap JS-->
<script src="../themes/vendor/bootstrap-4.1/popper.min.js"></script>
<script src="../themes/vendor/bootstrap-4.1/bootstrap.min.js"></script>


<!-- Vendor JS-->
<script src="../themes/vendor/animsition/animsition.min.js"></script>
<script src="../themes/vendor/circle-progress/circle-progress.min.js"></script>
<script src="../themes/vendor/perfect-scrollbar/perfect-scrollbar.js"></script>

<!-- Main JS-->
<script src="../themes/js/main.js"></script>
<?php if(isset($UserID)) : ?>
<script>
    jQuery(document).ready(function($){
        $('[data-toggle="tooltip"]').tooltip();

        function get_mes(){
            $.ajax({
                url: "/inc/user/Theme_1/mes.php",
                type: "POST",
                data: {"id":<?php echo $UserID;?>},
                cache: false,
                success: function(response){
                    res=response.split("|");
                    var mes_num=res[0];
                    var mes_body=res[1];
                    $(".mes_num").text(mes_num);
                    $(".mes_body").html(mes_body);
                    if(mes_num>0){
                        $(".mes_num").removeClass("d-none");
                    }
                    else{
                        $(".mes_num").addClass("d-none");
                    }
                    setTimeout(get_mes,30000);
                }
            });
        }
        get_mes();
    });
</script>
<?php endif; ?>