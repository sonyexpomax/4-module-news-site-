

$( document ).ready(function() {
    
    var timer  =null;
    var $searchInput = $( ".search_input" );
    var $tips = $(".tips");
    
    $searchInput.keyup(function() {
        if($searchInput.val()){
            clearTimeout(timer);
            timer = setTimeout(function () {
                requestTip($searchInput.val());
            }, 500);
        }
    });

    function requestTip(searchInput) {
        var url = '/search/' + searchInput;

        $.get( url, function( data ) {
            if (data.length > 0) {
                $tips.text('');
                if (data.length > 1) {
                    data.forEach(function (tag) {
                        $tips.append(linksCreator(tag.tag_id, tag.tag_name));
                    });
                }
                else {
                    $searchInput.append(linksCreator(data[0].tag_id, data[0].tag_name));
                }
                $tips.fadeIn(400);
            }
            else {
                $tips.text('');
                $tips.append('<p>Ничего не найдено</p>');
                //  document.querySelector(".tips").style.display = '';
                $tips.fadeIn(400);
            }
        })
    }

    $searchInput.focusout(function() {
        $tips.fadeOut(400);
    });

    $searchInput.focusin(function() {
        $tips.fadeIn(400);
    });
});
