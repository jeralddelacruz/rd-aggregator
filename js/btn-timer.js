let time = parseInt($('input[name=timer]').val()) * 1000;

function showBtns() {
    $('.timer-section').fadeIn()
}

$('.timer-section').hide()
setTimeout(() => {
    showBtns();
}, time);