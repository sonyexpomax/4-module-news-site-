var readingNowLast = 0;
var totalRead = parseInt($('#total-read').html());
var currentTotalRead = totalRead;

function getRandomReadingNow(min, max) {
    return Math.floor(Math.random() * (max - min)) + min;
}

function generateReadingNow(){
    var readingNow = getRandomReadingNow(0,4);

    if(readingNow > readingNowLast){
        currentTotalRead += ((readingNow - readingNowLast));
        $('#total-read')
            .removeClass('fa-book')
            .text('')
            .addClass('fa-spinner')
            .addClass('fa-spin');
        updateTotalRead((readingNow - readingNowLast));
    }

    readingNowLast = readingNow;
    document.querySelector('#reading-now').textContent = readingNow;
}
