jQuery(document).ready(function($){
    let button = '';
    let heading = '';
    let body = '';
    var styles = JSON.parse($('input[name=exitpopData]').val());

    $.exitIntent('enable');
    $(document).bind('exitintent', function() {
        if(Cookies.get('exit-pop-expire') !== 'true'){
            $('#exitpop').modal();
        }
    }); 
    
    $("#exitpop").on("hidden.bs.modal", function () {
        Cookies.set('exit-pop-expire', true, { expires: 0.8 });
    });

    $('#exitpop .btn-text').css({
        'background-color': styles.bcolor,
        'color': styles.b_tcolor,
    });
    $('#exitpop strong.heading').css({
        'font-family' : styles.headfont,
        'font-size' : styles.headsize,
        'color': styles.headcolor
    });
    $('#exitpop p.body').css({
        'font-family' : styles.bodyfont,
        'font-size' : styles.bodysize,
        'color': styles.bodycolor
    });
});