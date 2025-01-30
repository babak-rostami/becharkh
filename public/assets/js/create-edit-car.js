

function focusFunction(id) {
    div_id = "message-" + id;
    document.getElementById(div_id).style.display = "block";
}

function focusOutFunction(id) {
    div_id = "message-" + id;
    document.getElementById(div_id).style.display = "none";
}

$('#city').change(function () {
    var city = $(this).find('option:selected').text();
})

$('#ostan').change(function () {
    $('#city').html('').fadeIn(800).append('<option value="{{null}}">لطفا کمی صبر کنید ...</option>');
    var id = $(this).find('option:selected').val();
    $.ajax({
        method: 'get',
        url: '/getCitiesCreate',
        data: {
            "id": id,
        },
        success: function (msg) {
            $('#city').html(msg);
        }
    })
});


$('#has_price').change(function () {
    var id = $(this).find('option:selected').val();
    if (id == 1) {
        document.getElementById('price').disabled = false;
        document.getElementById('price').placeholder = 'قیمت خودرو را وارد کنید';
    } else {
        document.getElementById('price').disabled = true;
        document.getElementById('price').placeholder = '';
        document.getElementById('price').placeholder = 'توافقی';
    }
});


(function($) {
    $.fn.inputFilter = function(inputFilter) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
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

$(document).ready(function() {
    $(".number-only").inputFilter(function(value) {
        return /^\d*$/.test(value);    // Allow digits only, using a RegExp
    });
});
