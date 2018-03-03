var commenting = {
    requestComments: function (url) {
        $.getJSON(url, function (result) {
            var data = result;
            for (var i = 0; i < data.length; i++) {
                commenting.createCommentBlock(data[i].user, data[i].date, data[i].text, data[i].plus, data[i].minus, data[i].id, data[i].parent, data[i].hasOpinion, false);
            }

            $(".comment-waiting")
                .removeClass('fa-spin')
                .removeClass('fa-spinner')
                .fadeOut(500).remove();
        });
    },

    createCommentBlock: function (nameComment, dateComment, textComment, plus, minus, commentId, parentId, hasOpinion, isNew) {
        var commentHtml = '';

        if (!parentId) {
            commentHtml += '<div class="comment-wrap" id="comment-' + commentId + '">';
        }
        else {
            var parentWidth = $("#comment-" + parentId).width();
            commentHtml += '<div class="comment-wrap" id="comment-' + commentId + '" style="width:' + (parentWidth * 0.95) + 'px">';
        }

        commentHtml +=
            '<div class="photo">' +
            '<i class="fa fa-comment-o fa-3x"></i>' +
            '</div>' +
            '<div class="comment-block main-comment">' +
            '<p class="comment-header">' + nameComment + '</p>';


        if (isNew) {
            commentHtml += '<button class="btn btn-info btn-sm float-right" id="comment-edit">Сохранить<i class="fa fa-pencil" id="button-edit-comment"></i></button>';
        }

        if ($('#logout').data('user_name') && !isNew) {
            commentHtml += '<button class="btn btn-warning btn-sm float-right" id="comment-add-parent">Прокомментировать <i class="fa fa-comment" id="button-icon-parent"></i></button>';
        }

        if (isNew) {
            commentHtml += '<p class="comment-text" contenteditable="true">' + textComment + '</p>';
        }
        else {
            commentHtml += '<p class="comment-text">' + textComment + '</p>';
        }
        commentHtml += '<div class="bottom-comment">' +
            '<div class="comment-date">' + dateComment + '</div>';

        if ($('#logout').data('user_name')) {
            if (!hasOpinion) {
                commentHtml +=
                    '<ul class="comment-actions">' +
                    '<li class="complain js-plus"><i class="fa fa-hand-o-up fa-2x"></i><span> ' + plus + '</span></li>' +
                    '<li class="reply js-minus"><i class="fa fa-hand-o-down fa-2x"></i><span> ' + minus + '</span></li>' +
                    '</ul>';
            }
            else {
                commentHtml +=
                    '<ul class="comment-actions">' +
                    '<li class="complain" title="Вы уже высказали свое мнение по этому комментарию"><i class="fa fa-get-pocket fa-2x"></i><span></span></li>' +
                    '<li class="complain"><i class="fa fa-hand-o-up fa-2x"></i><span> ' + plus + '</span></li>' +
                    '<li class="reply"><i class="fa fa-hand-o-down fa-2x"></i><span> ' + minus + '</span></li>' +
                    '</ul>';
            }
        }
        else {
            commentHtml +=
                '<ul class="comment-actions">' +
                '<li class="complain" title="Комментировать могут только зарегестрированные пользователи"><i class="fa fa-ban fa-2x"></i><span></span></li>' +
                '<li class="complain"><i class="fa fa-hand-o-up fa-2x"></i><span> ' + plus + '</span></li>' +
                '<li class="reply"><i class="fa fa-hand-o-down fa-2x"></i><span> ' + minus + '</span></li>' +
                '</ul>';
        }

        commentHtml +=
            '</div>' +
            '</div>' +
            '</div>';

        if (isNew) {
            $('.message').after(commentHtml).fadeIn(1000);
        }
        else {
            if (!parentId) {
                $(".comment-ajax").before(commentHtml).fadeIn(200);
            }
            else {
                $("#comment-" + parentId).after(commentHtml).fadeIn(200);
            }
        }
    },

    addComment: function (text, newsId, parentId, $button, isNew) {
        if (!parentId) {
            parentId = 0;
        }

        $.get('/add_comment/' + newsId + '/' + parseInt(parentId), {request: text})
            .done(function (data) {
                $button
                    .removeClass('fa-spinner')
                    .removeClass('fa-spin')
                    .addClass('fa-paper-plane');
                $('#new-comment').val('');

                if (parentId) {
                    commenting.commentFieldBackNormal();
                }

                if ($('#js-header').data('category') != 4) {
                    var d = new Date();
                    var dd = d.getHours() + ':' + d.getMinutes() + ' ' + d.getDay() + '/' + d.getMonth() + '/' + d.getFullYear();
                   commenting.createCommentBlock($('#logout').data('user_name'), dd, text, 0, 0, data, parentId, null, isNew);
                }
                else {
                    var tempP = '<p id="text-moderator" style="color: #830f16;font-weight: 600;font-size: 18px">    Комментарий сохранен и будет добавлен после одобрения модератора!</p>';
                    $('.comments-title').after(tempP);

                    setTimeout(function () {
                        $('#text-moderator').fadeOut(400).remove();
                    }, 5000);
                }

            });
    },

    updateComment: function ($button, $textarea, commentId) {

        var text = $textarea.text();
        $.get('/update_text/' + commentId, {request: text},
            function () {
                $button.find('i')
                    .removeClass('fa-spinner')
                    .removeClass('fa-spin')
                    .addClass('fa-check');
                $button.fadeOut(1000);
                $textarea.attr('contenteditable', 'false');
            }
        );
    },

    updatePlus: function (plusId, $elementForChange) {
        $.ajax({
            type: "GET",
            url: '/update_plus/' + plusId,
            success: function () {
                $elementForChange
                    .removeClass('fa-spinner')
                    .removeClass('fa-spin')
                    .addClass('fa-hand-rock-o');
                $elementForChange.attr('title', 'Вы уже высказали свое мнение по этому комментарию');
                var $span = $elementForChange.siblings();
                oldPlus = parseInt($span.html());
                $span.html(oldPlus + 1);

                $span.closest('li').removeClass('js-plus');
                $span.closest('li').siblings().removeClass('js-minus');

            }
        });
    },

    updateMinus: function(minusId, $elementForChange) {
        $.ajax({
            type: "GET",
            url: '/update_minus/' + minusId,
            success: function () {
                $elementForChange
                    .removeClass('fa-spinner')
                    .removeClass('fa-spin')
                    .addClass('fa-hand-rock-o');
                $elementForChange.attr('title', 'Вы уже высказали свое мнение по этому комментарию');
                var $span = $elementForChange.siblings();
                oldMinus = parseInt($span.html());
                $span.html(oldMinus + 1);
                $span.closest('li').removeClass('js-minus');
                $span.closest('li').siblings().removeClass('js-plus');
            }
        });
    },

    commentFieldBackNormal: function () {

        $('#new-comment-child').closest('.sub-comment').find('.main-comment').animate({
            width: "10%", marginLeft: 0, queue: false
        }, 300);

        $('#new-comment-child').closest('.comment-wrap').find('.main-comment').animate({
            width: "100%", queue: false
        }, 300);

        $('#new-comment-child').closest('.sub-comment').remove();
    }
};

    $( document ).ready(function() {
        $('body').on('click', '#comment-add', function (event) {
            event.preventDefault();

            var text = $('#new-comment').val();
            if (text !== '') {
                var newsId = $('.comments-all').data('news_id');

                $('#button-icon')
                    .removeClass('fa-paper-plane')
                    .addClass('fa-spinner')
                    .addClass('fa-spin');
                commenting.addComment(text, newsId, null, $('#button-icon'), true);

                setTimeout(function () {
                    $mainComment = $('#comment-edit').closest('.main-comment');
                    $mainComment.find('#comment-edit').fadeOut(300).remove();
                    $mainComment.find('.comment-text').attr('contenteditable', false);
                }, 60000);
            }
        });

        $('body').on('click', '#comment-add-parent', function (event) {
            event.preventDefault();

            var $commentBlock = $(this).closest('.comment-block');
            var $commentWrap = $(this).closest('.comment-wrap');

            $commentBlock.animate({
                width: "50%"
            }, 500);

            $commentBlock.after('' +
                '<div class="comment-block sub-comment">' +
                '   <form action="">' +
                '       <textarea name="" id="new-comment-child" cols="30" rows="10" placeholder="Add comment..." class="comment-textarea"></textarea>' +
                '       <button class="btn btn-success btn-sm float-right" id="comment-add-child">ОК <i class="fa fa-paper-plane" id="button-icon-child"></i></button>' +
                '       <button class="btn btn-danger btn-sm float-right" id="comment-cancel-child">Отмена</button>' +
                '   </form>' +
                ' </div>');

        });

        $('body').on('click', '#comment-cancel-child', function (event) {
            event.preventDefault();
            commenting.commentFieldBackNormal();

        });

        $('body').on('click', '#comment-add-child', function (event) {
            event.preventDefault();

            var text = $('#new-comment-child').val();
            var parentId = $(this).closest('.comment-wrap').attr('id').slice(8);
            var newsId = $('.comments-all').data('news_id');

            if (text !== '') {
                $('#button-icon-child')
                    .removeClass('fa-paper-plane')
                    .addClass('fa-spinner')
                    .addClass('fa-spin');
                commenting.addComment(text, newsId, parentId, $('#button-icon-child'), true);


                setTimeout(function () {
                    $mainComment = $('#comment-edit').closest('.main-comment');
                    $mainComment.find('#comment-edit').fadeOut(300).remove();
                    $mainComment.find('.comment-text').attr('contenteditable', false);

                }, 5000);
            }
        });

        $('body').on('click', '.js-plus', function (event) {
            event.preventDefault();

            $(this).find('i')
                .removeClass('fa-hand-o-up')
                .addClass('fa-spinner')
                .addClass('fa-spin');

            commenting.updatePlus($(this).closest('.comment-wrap').attr('id').slice(8), $(this).find('i'));
        });

        $('body').on('click', '.js-minus', function (event) {
            event.preventDefault();

            $(this).find('i')
                .removeClass('fa-hand-o-down')
                .addClass('fa-spinner')
                .addClass('fa-spin');

            commenting.updateMinus($(this).closest('.comment-wrap').attr('id').slice(8), $(this).find('i'));
        });

        $('body').on('click', '#comment-edit', function (event) {
            event.preventDefault();

            var $commentWrap = $(this).closest('.comment-wrap');
            var commentId = $commentWrap.attr('id').slice(8);
            var $textArea = $commentWrap.find('.comment-text');

            $(this).find('i')
                .removeClass('fa-pencil')
                .addClass('fa-spinner')
                .addClass('fa-spin');

            commenting.updateComment($(this), $textArea, commentId);
        });
    });
