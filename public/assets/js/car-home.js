function focusFunction(id) {
    div_id = "message-" + id;
    document.getElementById(div_id).style.display = "block";
}

function focusOutFunction(id) {
    div_id = "message-" + id;
    document.getElementById(div_id).style.display = "none";
}

$('.show-search').click(function (e) {
    e.preventDefault();

    // $('#advanced-search').zIndex = 2;
    if (document.getElementById('advanced-search').style.zIndex == -1) {
        document.getElementById('advanced-search').style.zIndex = 2;
    } else {
        document.getElementById('advanced-search').style.zIndex = -1;
    }
})

$('#city').change(function () {
    var city = $(this).find('option:selected').text();
})

$('#ostan').change(function () {
    $('#city').html('').fadeIn(800).append('<option value="{{null}}">لطفا کمی صبر کنید ...</option>');
    var id = $(this).find('option:selected').val();
    $.ajax({
        method: 'get',
        url: '/getCities',
        data: {
            "id": id,
        },
        success: function (msg) {
            $('#city').html(msg);
        }
    })
});


$('#model').change(function () {
    var model = $(this).find('option:selected').val();
    var brand = $("#brand option:selected").val();
    var action = 'https://becharkh.com/cars/' + brand + '/' + model;
    document.getElementById("search_form").action = action;

})

$('#brand').change(function () {
    $('#model').html('').fadeIn(800).append('<option value="{{null}}">لطفا کمی صبر کنید ...</option>');
    var brand = $(this).find('option:selected').val();

    var action = 'https://becharkh.com/cars/' + brand;
    document.getElementById("search_form").action = action;


    $.ajax({
        method: 'get',
        url: '/getModels',
        data: {
            "nameEn": brand,
        },
        success: function (msg) {
            $('#model').html(msg);
        }
    })
});

$('#brand-reminer').change(function () {
    $('#model-reminder').html('').fadeIn(800).append('<option value="{{null}}">لطفا کمی صبر کنید ...</option>');
    var id = $(this).find('option:selected').val();

    $.ajax({
        method: 'get',
        url: '/getModelsReminder',
        data: {
            "id": id,
        },
        success: function (msg) {
            $('#model-reminder').html(msg);
        }
    })
});

function FormatNumber(id1, id2) {
    document.getElementById(id2).value = FormatNumberBy3(document.getElementById(id1).value);
}

function FormatNumberBy3(num, decpoint, sep) {
    // check for missing parameters and use defaults if so
    if (arguments.length == 2) {
        sep = ",";
    }
    if (arguments.length == 1) {
        sep = ",";
        decpoint = ".";
    }
    // need a string for operations
    num = num.toString();
    // separate the whole number and the fraction if possible
    a = num.split(decpoint);
    x = a[0]; // decimal
    y = a[1]; // fraction
    z = "";


    if (typeof (x) != "undefined") {
        // reverse the digits. regexp works from left to right.
        for (i = x.length - 1; i >= 0; i--)
            z += x.charAt(i);
        // add seperators. but undo the trailing one, if there
        z = z.replace(/(\d{3})/g, "$1" + sep);
        if (z.slice(-sep.length) == sep)
            z = z.slice(0, -sep.length);
        x = "";
        // reverse again to get back the number
        for (i = z.length - 1; i >= 0; i--)
            x += z.charAt(i);
        // add the fraction back in, if it was there
        if (typeof (y) != "undefined" && y.length > 0)
            x += decpoint + y;
    }
    return x;
}

$(document).ready(function () {
    $(".number-only").inputFilter(function (value) {
        return /^\d*$/.test(value);    // Allow digits only, using a RegExp
    });
});

$(".reminder-form").submit(function (e) {
    if ($('#price1').val().length < 7) {
        $('.alert-price1').text("عدد را بصورت کامل وارد کنید")
        e.preventDefault()
    } else {
        $('.alert-price1').text("")
    }

    if ($('#price2').val().length < 7) {
        $('.alert-price2').text("عدد را بصورت کامل وارد کنید")
        e.preventDefault()
    } else {
        $('.alert-price2').text("")
    }
});

(function ($) {
    $.fn.inputFilter = function (inputFilter) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function () {
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                this.value = "";
            }
        });
    };
}(jQuery));

$(document).ready(function () {
    $(".number-only").inputFilter(function (value) {
        return /^\d*$/.test(value);    // Allow digits only, using a RegExp
    });
});
