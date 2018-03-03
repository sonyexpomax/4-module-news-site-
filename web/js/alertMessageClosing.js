var alertMessage = {

    clicked: false,
    checkElementsForClicking: function() {
        var dont_confirm_leave = 0;
        var leave_message = 'Вы действительно хотите покинуть сайт?';
        function showMessage(e) {
            if (!clicked) {
                if (dont_confirm_leave!==1) {
                    if(!e) e = window.event;
                    // IE
                    e.cancelBubble = true;
                    e.returnValue = leave_message;
                    //Firefox.
                    if (e.stopPropagation) {
                        e.stopPropagation();
                        e.preventDefault();
                    }
                    // Chrome , Safari
                    return leave_message;
                }
            }
        }
        window.onbeforeunload = showMessage;

        $(document).bind('keypress', function(e) {
            if (e.keyCode == 116 || e.keyCode == 84){
                clicked = true;
            }
        });

        $("a").bind("click", function() {
            clicked = true;
        });

        $("form").bind("submit", function() {
            clicked = true;
        });

        $("input[type=submit]").bind("click", function() {
            clicked = true;
        });
    }
};

$(document).ready(function() {
    alertMessage.checkElementsForClicking();
});