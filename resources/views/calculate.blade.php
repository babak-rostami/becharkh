@extends('index')


@section('content')

    <div class="row justify-content-center">
        <div class="col-6 text-center">
            <div class="form-group">
                <label for="">مبلغ خودرو (تومان)</label>
                <input type="text" class="form-control number-only" id="price" placeholder="مبلغ خودرو (تومان)"
                       onkeyup="javascript:FormatNumber('price','price-span');">
                <input disabled id="price-span">
                <br>
                <a class="btn btn-warning mt-2" id="calculate">محاسبه مالیات</a>
                <hr>
                <span id="result" style="border-bottom: 2px solid #005cbf"></span>
            </div>
        </div>
    </div>

    <script>
        $("#calculate").click(function () {
            var price = $("#price").val();
            var more;
            var result;
            if (price <= 1000000000) {
                $("#result").text("مالیاتی تعلق نمی گیرد")
            } else if (price > 1000000000 && price <= 1500000000) {
                more = price - 1000000000;
                result = more * (1 / 100);
                $("#result").text(result + " میلیون تومان ")
            } else if (price > 1500000000 && price <= 3000000000) {
                more = price - 1500000000;
                result = (more * (2 / 100)) + (500000000 * (1 / 100));
                $("#result").text(result + " میلیون تومان ")
            } else if (price > 3000000000 && price <= 4500000000) {
                more = price - 3000000000;
                result = (more * (3 / 100)) + (500000000 * (1 / 100)) + (1500000000 * (2 / 100));
                $("#result").text(result + " میلیون تومان ")
            } else if (price > 4500000000) {
                more = price - 4500000000;
                result = (more * (4 / 100)) + (500000000 * (1 / 100)) + (1500000000 * (2 / 100)) + (1500000000 * (3 / 100));
                $("#result").text(result + " میلیون تومان ")
            }

        })

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
    </script>
@endsection
