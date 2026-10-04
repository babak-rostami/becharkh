let is_fuser_exist_timeout;
$('#fake-username').on('input', function () {
    clearTimeout(is_fuser_exist_timeout);
    is_fuser_exist_timeout = setTimeout(function () {
        let username = $('#fake-username').val();
        $.ajax({
            url: is_fuser_exist,
            method: 'GET',
            data: {
                username: username
            },
            success: function (response) {
                if (response.status == 1) {
                    $('#fake-user-exist').text('این نام کاربری وجود دارد').css('color',
                        'red');
                } else {
                    $('#fake-user-exist').text('این نام کاربری موجود نیست').css('color',
                        'green');
                }
            },
            error: function () {
                $('#fake-user-exist').text('خطا در بررسی نام کاربری').css('color',
                    'orange');
            }
        });
    }, 2000);
});