var reading = {

    readingNowLast: 0,
    totalRead: parseInt($('#total-read').html()),
    currentTotalRead: parseInt($('#total-read').html()),

    getRandomReadingNow: function (min, max) {
        return Math.floor(Math.random() * (max - min)) + min;
    },

    generateReadingNow: function () {
        var readingNow = reading.getRandomReadingNow(0, 4);
        console.log(readingNow);
        console.log(reading.readingNowLast);
        console.log(reading.totalRead );

        if (readingNow > reading.readingNowLast) {
            reading.currentTotalRead += ((readingNow - reading.readingNowLast));
            $('#total-read')
                .removeClass('fa-book')
                .text('')
                .addClass('fa-spinner')
                .addClass('fa-spin');
            reading.updateTotalRead((readingNow - reading.readingNowLast));
        }

        reading.readingNowLast = readingNow;
        document.querySelector('#reading-now').textContent = readingNow;
    }
};
