$(document).ready(function () {

    $( ".form-control-file" ).after('<button class="btn btn-sm btn-success" id="js-add-file">Добавить файл</button>');

    $('body').on('click', '#js-add-file', function (event) {
        event.preventDefault();

        $newFileInput =
            '<div class="file-load-block">' +
            '<input type="file" id="news_form_images" name="news_form[images][]" required="required" multiple="multiple" class="form-control-file file-load-text">' +
            '<button class="btn btn-danger btn-sm file-load-close" title="Удалить">X</button>' +
            '</div>';
        $('#js-add-file').before($newFileInput);
    });

    $('body').on('click', '.file-load-close', function (event) {
        event.preventDefault();
        $(this).closest($('.file-load-block')).fadeOut(400).remove();
    });

});