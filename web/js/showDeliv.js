$(document).ready(function(){
    var $banner =
        "<div class='banner'>" +
            "<span id='banner-close' class='fa fa-times'> Закрыть</span>" +
            "<h4>Подписаться на новостную рассылку</h4>" +
            "<input type='email' name='email' placeholder='Введите ваш email ...' />" +
            "<input type='text' name='full-name' placeholder='Введите ваше полное имя ...' />" +
            "<button class='btn btn-danger btn-md'>Отправить</button>"
        "</div>";

    function showBanner(){
        setTimeout(function () {
            $('body').append($banner).show().fadeIn(1000);
        }, 15000);
    }

    if ( !getCookie('isBannerShow') ){
        setCookie('isBannerShow', true);
        showBanner();
    }

    $('body').on('click', '#banner-close', function (event) {
        event.preventDefault();
        $(this).closest('.banner').fadeOut(500).remove();
    });
});