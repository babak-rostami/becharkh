$(document).ready(function () {
    $('#btn-android').on('click', function () {
        $('#android-instructions').show();
        $('#iphone-instructions').hide();

        $('#btn-android').addClass('btn-primary').removeClass('btn-outline-primary');
        $('#btn-iphone').addClass('btn-outline-primary').removeClass('btn-primary');
    });

    $('#btn-iphone').on('click', function () {
        $('#iphone-instructions').show();
        $('#android-instructions').hide();

        $('#btn-iphone').addClass('btn-primary').removeClass('btn-outline-primary');
        $('#btn-android').addClass('btn-outline-primary').removeClass('btn-primary');
    });
});
