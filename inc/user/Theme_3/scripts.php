
<!-- From original -->
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script src="../js/jquery-ui-timepicker-addon.min.js"></script>
<script src="../assets/js/bootstrap-checkbox-radio-switch.js"></script>
<script src="../assets/js/bootstrap-notify.js"></script>
<script src="../js/fancybox/jquery.fancybox.js"></script>
<script src="../js/spectrum.js"></script>

<!-- Bootstrap JS -->
<script src="../themes/vendor/bootstrap-4.1/popper.min.js"></script>
<script src="../themes/vendor/bootstrap-4.1/bootstrap.min.js"></script>

<!-- Vendor JS -->
<script src="../themes/vendor/animsition/animsition.min.js"></script>
<script src="../themes/vendor/circle-progress/circle-progress.min.js"></script>
<script src="../themes/vendor/perfect-scrollbar/perfect-scrollbar.js"></script>

<!-- Main JS-->
<script src="../themes/js/main.js"></script>

<!-- Custom JS-->
<script>
    $( document ).ready(function() {
        $('table').removeClass('table-striped').addClass('table-borderless table-data3');

        if ($('.page-content .alert').length >= 1) {
            let alert_status = null;
            $.each($('.page-content .alert').attr('class').split(" "), function( key, value ) {
                if (value == 'alert-success' || value == 'alert-danger') {
                    alert_status = value;
                    return false;
                }
            });
            
            if (alert_status != null) {
                let message_response = '';
                if ($('.page-content .alert').text().split("•").length != 1) {
                    $.each($('.page-content .alert').text().split("•"), function( key, value ) {
                        if (value != '') message_response += '• '+ value + '<br/>';
                    });
                } else {
                    message_response = $('.page-content .alert').text();
                }

                $('#theme-3-alert .alert').addClass('au-' + alert_status);

                if (alert_status == 'alert-success') {
                    $('#theme-3-alert .zmdi').first().addClass('zmdi-check-circle');
                } else {
                    $('#theme-3-alert .zmdi').first().hide();
                }

                $('#theme-3-alert span.content').html(message_response);
                $('#theme-3-alert').css('display', 'block');
            } else {
                $('#theme-3-alert').css('display', 'none');
            }
        }
    });
</script>

<script>
    $(document).ready(function($) {
        $('[data-toggle="tooltip"]').tooltip();
        function get_mes() {
            $.ajax({
                url: "mes.php",
                type: "POST",
                data: {
                    "id": <?= $UserID ?>
                },
                cache: false,
                success: function(response) {
                    res = response.split("|");
                    var mes_num = res[0];
                    $("#mes_num").text(mes_num);

                    if (mes_num <= 0)
                        $('head').append('<style type="text/css">.has-noti > i:after{background:none;}</style>');

                    setTimeout(get_mes, 30000);
                }
            });
        }

        get_mes();
    });
</script>