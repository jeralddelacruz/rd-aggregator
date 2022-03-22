const $ = jQuery;

$(document).ready(function(){
    setTimeout(function(e){
        document.querySelector('.popup-container').classList.remove('hide');
    }, 10000) 
    
    
    document.querySelector('.close-icon').addEventListener('click', function(){
        document.querySelector('.popup-container').classList.add('hide');
    });
    
    document.querySelector('#btn-no').addEventListener('click', function(){
        document.querySelector('.popup-container').classList.add('hide');
    });
    
    document.querySelector('#btn-yes').addEventListener('click', function(){
        document.querySelector('.second-page').classList.remove('hide');
        document.querySelector('.first-page').classList.add('hide');
    });
    
    
});