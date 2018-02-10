

$( ".get-other-pages" ).click(function(event) {
    event.preventDefault();
    var $getOtherPagesElem = $( ".get-other-pages" );

    var total_pages = $getOtherPagesElem.data( "total" );
    var curent_page = $getOtherPagesElem.data( "current" );

    for(var i = (curent_page + 1); i <= total_pages; i++){
        $( ".active-page" ).after('' +
            '<li class="page-item">' +
            '<a class="page-link" href="{{ path(\'news_list\',{\'category_id\':category_id, \'page_number\':\'page=1\'})}}">{{ i }}</a>' +
            '</li>');
    }

});

