@extends('index')


@section('title')
    ثبت آگهی
@endsection


@section('style')

    <link rel="stylesheet" href="{{asset('css/chosen.css')}}">
    {{--    <link rel="stylesheet" href="{{asset('css/chosen-dark.css')}}">--}}

    <script src="{{asset('js/chosen.jquery.js')}}"></script>
    <script src="{{asset('js/chosen.proto.js')}}"></script>



    <script>
        $(document).ready(function () {
            $(".chosen-select").chosen();
        });

    </script>

    <meta name="robots" content="noindex">


    <style>


        #ostan_chosen {
            width: 100% !important;
        }

        #brand_chosen {
            width: 100% !important;
        }


        fieldset {
            display: none;
            padding: 20px;
            margin-top: 50px;
            margin-bottom: 50px;
            border-radius: 5px;
            box-shadow: 3px 3px 25px 1px gray
        }

        #first {
            display: block;
            padding: 20px;
            margin-top: 50px;
            border-radius: 5px;
            box-shadow: 3px 3px 25px 1px gray
        }

        input[type=text], input[type=password], select {
            width: 100%;
            margin: 10px 0;
            padding: 5px;
            border-radius: 4px
        }

        textarea {
            width: 100%;
            margin: 10px 0;
            height: 70px;
            padding: 5px;
            border-radius: 4px
        }

        input[type=submit], input[type=button] {
            width: 120px;
            margin: 15px 25px;
            padding: 5px;
            height: 40px;
            background-color: #a0522d;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-family: 'Droid Serif', serif
        }

        h2, p {
            text-align: center;
            font-family: 'Droid Serif', serif
        }

        li {
            margin-right: 52px;
            display: inline;
            color: #c1c5cc;
            font-family: 'Droid Serif', serif
        }

    </style>

@endsection


@section('content')


    <form class="regform" action="{{route('car.store')}}" method="post" role="form" enctype="multipart/form-data">
        @csrf

        <!-- Fieldsets -->
        <fieldset id="first">
            <h2 class="title">مشخصات خودرو</h2>
            <div class="row justify-content-center mt-4 bg-wht p-4 radius-10 mx-2 align-items-center">

                <div class="col-10 col-sm-5">
                    <div class="form-group">
                        <select required class="chosen-select form-control" name="brand" id="brand">
                            <option class="d-none" value="{{null}}">برند را انتخاب کنید</option>
                            @foreach($brands as $brand)
                                <option
                                    {{old('brand') == $brand->id ? "selected":""}} value="{{$brand->id}}">{{$brand->title}}</option>
                            @endforeach
                        </select>
                        @error('brand')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-10 col-sm-5">
                    <div class="form-group">
                        <select required class="form-control" name="model" id="model">
                            @if(old('brand') != null)
                                <option value="{{null}}">مدل را انتخاب کنید</option>
                                @foreach($brand->findItId(old('brand'))->models as $m)
                                    <option
                                        {{$m->id == old('model')?"selected":""}} value="{{$m->id}}">{{$m->title}}</option>
                                @endforeach
                            @else
                                <option value="{{null}}">مدل را انتخاب کنید</option>
                            @endif
                        </select>
                        @error('model')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="col-10 col-sm-5">
                    <div class="form-group">
                        <input required placeholder="سال تولید"
                               class="text_field form-control number-only" type="text"
                               onfocus="focusFunction(1)"
                               onfocusout="focusOutFunction(1)" name="production_year"
                               value="{{old('production_year')}}">
                        <div id="message-1" style="display: none">
                            <img src="{{asset('files/other/images/circle.webp')}}">
                            <span style="font-size: 14px">چهار رقمی شمسی مثلا : 1390</span>
                        </div>
                        @error('production_year')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-10 col-sm-5">
                    <div class="form-group">
                        <select required class="form-control" name="how_sell" id="how_sell">
                            <option class="d-none" value="{{null}}">نحوه فروش را انتخاب کنید</option>
                            <option {{old('how_sell') == 1 ? "selected":""}} value="1">نقدی</option>
                            <option {{old('how_sell') == 2 ? "selected":""}} value="2">اقساطی</option>
                            <option {{old('how_sell') == 3 ? "selected":""}} value="3">نقدی و اقساطی</option>
                        </select>
                        @error('how_sell')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-10 col-sm-5">
                    <div class="form-group">
                        <input required placeholder="کارکرد خودرو" class="text_field form-control"
                               name="kilometer" id="kilometer"
                               type="text"
                               value="{{old('kilometer')}}">
                        @error('kilometer')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-10 col-sm-5">
                    <div class="form-group">
                        <select required class="text_field form-control" name="gearbox" id="gearbox">
                            <option class="d-none" value="{{null}}">گیربکس را انتخاب کنید</option>
                            <option {{old('gearbox') == 1 ? "selected":""}} value="1">دنده ای</option>
                            <option {{old('gearbox') == 2 ? "selected":""}} value="2">اتومات</option>
                        </select>
                        @error('gearbox')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-10 col-sm-5">
                    <div class="form-group">
                        <select required class="text_field form-control" name="fuel_type" id="fuel_type">
                            <option value="{{null}}">نوع سوخت را انتخاب کنید</option>
                            <option {{old('fuel_type') == 1 ? "selected":""}} value="1">بنزین</option>
                            <option {{old('fuel_type') == 2 ? "selected":""}} value="2">گازوئیل(دیزل)</option>
                            <option {{old('fuel_type') == 3 ? "selected":""}} value="3">دوگانه سوز</option>
                            <option {{old('fuel_type') == 4 ? "selected":""}} value="4">هیبریدی</option>
                        </select>
                        @error('fuel_type')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-10 col-sm-5">
                    <div class="form-group">
                        <select required class="text_field form-control" name="body_condition" id="body_condition">
                            <option value="{{null}}">وضعیت بدنه</option>
                            <option {{old('body_condition') == 1 ? "selected":""}} value="1">بدون رنگ</option>
                            <option {{old('body_condition') == 2 ? "selected":""}} value="2">یک لکه رنگ</option>
                            <option {{old('body_condition') == 3 ? "selected":""}} value="3">دو لکه رنگ</option>
                            <option {{old('body_condition') == 4 ? "selected":""}} value="4">چند لکه رنگ</option>
                            <option {{old('body_condition') == 5 ? "selected":""}} value="5">دور رنگ</option>
                            <option {{old('body_condition') == 6 ? "selected":""}} value="6">کامل رنگ</option>
                            <option {{old('body_condition') == 7 ? "selected":""}} value="7">گلگیر تعویض</option>
                            <option {{old('body_condition') == 8 ? "selected":""}} value="8">کاپوت تعویض</option>
                            <option {{old('body_condition') == 9 ? "selected":""}} value="9">درب تعویض</option>
                            <option {{old('body_condition') == 10 ? "selected":""}} value="10">اتاق تعویض</option>
                            <option {{old('body_condition') == 11 ? "selected":""}} value="11">تصادفی</option>
                            <option {{old('body_condition') == 12 ? "selected":""}} value="12">اوراقی</option>
                        </select>
                        @error('body_condition')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

            </div>
            <input id="next_btn1" onclick="next_step1()" type="button" value="بعدی">
        </fieldset>
        <fieldset id="second">
            <h2 class="title">جزئیات آگهی</h2>

            <div class="row justify-content-center mt-4 bg-wht p-4 radius-10 mx-2 align-items-center">

                <div class="col-10 col-sm-5">
                    <div class="form-group">
                        <select required class="form-control" id="has_price" name="has_price">
                            <option {{old('has_price') == 1 ? "selected":""}} value="1">قیمت نمایش داده شود</option>
                            <option {{old('has_price') == 2 ? "selected":""}} value="2">توافقی</option>
                        </select>
                    </div>
                </div>
                <div class="col-10 col-sm-5">
                    <div class="form-group">
                        <input required
                               @if(old('has_price') == 2) disabled placeholder="توافقی"
                               @else placeholder="قیمت خودرو را وارد کنید" value="{{old('price')}}" @endif
                               class="text_field form-control number-only" type="text" onfocus="focusFunction(4)"
                               onfocusout="focusOutFunction(4)" id="price" name="price">
                        <div id="message-4" style="display: none">
                            <img src="{{asset('files/other/images/circle.webp')}}">
                            <span style="font-size: 14px">عدد برحسب ملیون تومان مثال : 300000000</span>
                        </div>
                        @error('price')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-10 col-sm-5">
                    <div class="form-group">
                        <input required class="text_field form-control"
                               placeholder="عنوان آگهی" type="text"
                               onfocus="focusFunction(2)"
                               onfocusout="focusOutFunction(2)" name="title" value="{{old('title')}}">
                        <div id="message-2" style="display: none">
                            <img src="{{asset('files/other/images/circle.webp')}}">
                            <span style="font-size: 14px">مثلا : پژو 206 مدل 89 برای بازدید بهتر از کلمات انگلیسی استفاده نشود</span>
                        </div>

                        @error('title')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-10 col-sm-5">
                    <div class="form-group">
                        <input required class="text_field form-control number-only" placeholder="شماره تماس"
                               type="text" name="phone"
                               value="{{old('phone')}}">
                        @error('phone')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-10">
                    <div class="form-group">
                    <textarea required class="text_field form-control" placeholder="توضیحات آگهی"
                              onfocus="focusFunction(3)"
                              onfocusout="focusOutFunction(3)" name="body">{{old('body')}}</textarea>
                        <div id="message-3" style="display: none">
                            <img src="{{asset('files/other/images/circle.webp')}}">
                            <span style="font-size: 14px">توضیحات کلی رنگ , وضعیت بدنه , سال تولید , کارکرد و ...</span>
                        </div>
                        @error('body')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-10 col-sm-5">
                    <div class="form-group">
                        <select required class="chosen-select form-control" name="ostan" id="ostan">
                            <option class="d-none" value="{{null}}">استان را انتخاب کنید</option>
                            @foreach($ostans as $ostan)
                                <option
                                    {{old('ostan') == $ostan->id?"selected":""}} value="{{$ostan->id}}">{{$ostan->title}}</option>
                            @endforeach
                        </select>
                        @error('ostan')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-10 col-sm-5">
                    <div class="form-group">
                        <select required class="form-control" name="city" id="city">
                            <option value="{{null}}">شهر را انتخاب کنید</option>
                            @if(old('ostan') != null)
                                @foreach($ostan->findIt(old('ostan'))->cities as $c)
                                    <option
                                        {{$c->id == old('city')?"selected":""}} value="{{$c->id}}">{{$c->title}}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('city')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <input id="pre_btn1" onclick="prev_step1()" type="button" value="قبلی">
            <input id="next_btn2" name="next" onclick="next_step2()" type="button" value="بعدی">
        </fieldset>
        <fieldset id="third">
            <h2 class="title">تصاویر</h2>
            <div class="row justify-content-center">
                @for($i=1; $i<= auth('user')->user()->adImageCount() ; $i++)
                    <div class="col-3 text-center shadow-sm p-4 mx-1 mt-2">
                        <img class="w-50" id="blah-{{$i}}" src="{{asset('files/other/images/choose-car.png')}}"
                             alt="تصویر را انتخاب کنید"/>
                        <br>
                        <input onchange="readURL(this,{{$i}})" type='file' name="img-{{$i}}" id="imgInp-{{$i}}"/>
                    </div>
                @endfor
            </div>

            <input id="pre_btn2" onclick="prev_step2()" type="button" value="قبلی">
            <input class="submit_btn btn btn-success" type="submit" value="ثبت آگهی">
        </fieldset>
    </form>

@endsection


@section('script')

    <script type="text/javascript" src="{{asset('assets/js/create-edit-car.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/main.js')}}"></script>


    <script>
        function readURL(input, i) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    id = '#blah-' + i;
                    $(id).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        /*---------------------------------------------------------*/
        // Function that executes on click of first next button.
        function next_step1() {
            document.getElementById("first").style.display = "none";
            document.getElementById("second").style.display = "block";
            document.getElementById("active2").style.color = "red";
        }

        // Function that executes on click of first previous button.
        function prev_step1() {
            document.getElementById("first").style.display = "block";
            document.getElementById("second").style.display = "none";
            document.getElementById("active1").style.color = "red";
            document.getElementById("active2").style.color = "gray";
        }

        // Function that executes on click of second next button.
        function next_step2() {
            document.getElementById("second").style.display = "none";
            document.getElementById("third").style.display = "block";
            document.getElementById("active3").style.color = "red";
        }

        // Function that executes on click of second previous button.
        function prev_step2() {
            document.getElementById("third").style.display = "none";
            document.getElementById("second").style.display = "block";
            document.getElementById("active2").style.color = "red";
            document.getElementById("active3").style.color = "gray";
        }

    </script>
@endsection
