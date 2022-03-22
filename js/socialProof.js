var socialProofs = JSON.parse($('input[name=socialProofData]').val());

function preview(data, product) {
    var template = '';
    template += '<div data-notify="container" style="background: white; box-shadow:0 1px 5px #a2a2a2;" class="col-xs-11 col-sm-3 sp-container-hits alert alert-{0}" role="alert">';
    template += '   <button type="button" aria-hidden="true" class="close" data-notify="dismiss" style="top: unset;">×</button>';
    template += '   <img data-notify="icon" class="pull-left sp-img" style="max-width: 120px; margin-right: 7px; min-height: 120px; max-height: 120px;">';
    template += '   <span data-notify="title">{1}</span>';
    template += '   <span data-notify="message" class="msg">{2}</span>';
    template += '   <a href="{3}" target="{4}" data-notify="url"></a>';
    template += '</div>';

    var title = data.title.length > 63 ? data.title.substring(0, 60) + '...' : data.title;

    var notify = $.notify({
        icon: `/upload/scp/${data.image}`,
        title: '<h5><strong>' + title + '</strong></h5>',
        message: '<small>' + data.scp_content + '</small>',
        url: data.scp_link,
        target: '_blank'
    },{
        placement: {
            from: "bottom",
            align: "left"
        },
        type: 'minimalist',
        delay: data.scp_time * 1000,
        icon_type: 'image',
        template: template,
    });  

    return notify;
}

$(window).on('load', function(){
    $.each(socialProofs, function (index, proof) {
        var interval = ((parseInt(proof.scp_diff) + parseInt(proof.scp_time)) * 1000) * 2;

        setTimeout(function(){
            console.log(interval)
            preview(proof, proof.product)
        }, interval); 
    });
})