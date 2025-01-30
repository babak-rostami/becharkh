@extends('admin_index')


@section('title')
    ویرایش مقاله
@endsection


@section('style')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>

    <script>
        window.id = "{{$blog->id}}";
    </script>

    <link href="{{asset('admin_c/plugins/file-upload/file-upload-with-preview.min.css')}}" rel="stylesheet"
          type="text/css"/>


    <link rel="stylesheet" href="{{asset('css/chosen.css')}}">

    <script src="{{asset('js/chosen.jquery.js')}}"></script>
    <script src="{{asset('js/chosen.proto.js')}}"></script>



    <script>
        $(document).ready(function () {
            $(".chosen-select").chosen();
        });

    </script>
    
@endsection


@section('content')

    <form action="" method="post" role="form" class="cmxform" id="blogform"
          enctype="multipart/form-data">
        @csrf
        {{method_field('PUT')}}

        <ul class="nav nav-tabs  mb-3 mt-3" id="simpletab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab"
                   aria-controls="pills-home" aria-selected="true">اطلاعات کلی</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-seo-tab" data-toggle="pill" href="#pills-seo" role="tab"
                   aria-controls="pills-seo" aria-selected="false">سئو</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-image-tab" data-toggle="pill" href="#pills-image" role="tab"
                   aria-controls="pills-seo" aria-selected="false">تصویر</a>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                 aria-labelledby="pills-home-tab">

                <div class="row mt-2 p-4 align-items-center justify-content-center">
                    <div class="col-10">
                        <div class="form-group">
                            <label for="title">عنوان</label>
                            <input required type="text" readonly class="form-control" name="title" id="title-input"
                                   value="{{$blog->title}}">
                            <span id="title-span"></span>
                        </div>

                        <div class="form-group">
                            <label for="category_id">دسته بندی</label>
                            <select required name="category_id" id="category_id" class="form-control">
                                @foreach($categories as $category)
                                    <option
                                        {{$category->id == $blog->category_id ? "selected":""}} value="{{$category->id}}">{{$category->title}}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="row">
                            <div class="col-12 text-center">
                                <span class="badge badge-warning">در صورت نیاز برند و مدل انتخاب شود</span>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label for="brand">برند</label>
                                    <select class="chosen-select form-control" name="brand" id="brand">
                                        <option value="{{null}}">برند را انتخاب کنید</option>
                                        @foreach($brands as $brand)
                                            <option
                                                {{isset($blog->brand) && $blog->brand->id == $brand->id ? "selected":""}} value="{{$brand->id}}">{{$brand->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="model">مدل</label>
                                    <select class="form-control" name="model" id="model">
                                        @if(isset($blog->brand))
                                            <option value="{{null}}">مدل را انتخاب کنید</option>
                                            @foreach(brand_models($blog->brand->id) as $m)
                                                <option
                                                    {{isset($blog->model) && $m->id == $blog->model->id?"selected":""}} value="{{$m->id}}">{{$m->title}}</option>
                                            @endforeach
                                        @else
                                            <option value="{{null}}">مدل را انتخاب کنید</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <input class="form-control" id="tags" type="text"
                                   placeholder="تگ های مرتبط با مطلب را مشخص کنید">
                        </div>

                        <div class="form-group">
                            <input class="form-control" dir="rtl" name="tags" id="selected-tags" value="{{old('tags')}}"
                                   readonly placeholder="تگ های انتخاب شده">
                            @foreach($blog->tags() as $tag)
                                <span class="badge badge-info"><a data-toggle="modal"
                                                                  data-target="#deletetag-{{$tag->id}}"><img
                                            style="width: 25px; cursor: pointer" class="mx-1"
                                            src="{{asset('files/other/images/remove.png')}}"></a>#{{$tag->title}}</span>
                                <!-- tag delete modal -->
                                <div class="modal fade" id="deletetag-{{$tag->id}}" tabindex="-1" role="dialog"
                                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">حذف تگ</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                آیا از حذف تگ {{$tag->title}} از این مقاله اطمینان دارید؟
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    نه
                                                </button>
                                                <a href="{{route('tag.destroy',['tid'=>$tag->id,'pid'=>$blog->id ,'class'=>'blog'])}}"
                                                   class="btn btn-danger">بله</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <span id="tagcount" class="badge badge-success">{{$blog->tags()->count()}}</span>
                            <span id="cleartags" style="cursor: pointer" class="badge badge-danger">پاک کردن همه</span>
                        </div>

                        <div class="show-tag-result px-2"></div>

                    </div>

                    <div class="col-10 mt-3">
                        <div class="form-group">
                            <label for="short_description">توضیحات کوتاه</label>
                            <textarea style="width: 100%; height: 150px; resize: none" class="form-control" required
                                      name="short_description"
                                      id="description-input">{{$blog->short_description}}</textarea>
                            <span id="description-span"></span>
                        </div>
                    </div>
                    <div class="col-10">
                        <div class="form-group">
                            <label for="description">توضیحات</label>
                            <textarea name="description" class="form-control">{!! $blog->content !!}</textarea>
                        </div>
                    </div>
                </div>

            </div>


            <div class="tab-pane fade" id="pills-seo" role="tabpanel" aria-labelledby="pills-seo-tab">


                <div class="row justify-content-center mt-2 p-4">
                    <div class="col-10">

                        <div class="form-group">
                            <label for="meta_title">meta title</label><span class="ml-2 badge badge-danger"
                                                                            style="cursor: pointer" data-toggle="modal"
                                                                            data-target="#meta_title_message">نکات مهم</span>
                            <input required type="text" class="form-control" name="meta_title" id="meta-title-input"
                                   value="{{$blog->meta_title}}">
                            <span id="meta-title-span"></span>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="meta_title_message" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">قوانین متا تایتل خوب</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>کلمه کلیدی کلمه ای است که کاربران در گوگل سرچ میکنند تا به مقاله شما
                                            برسند</p>
                                        <hr>
                                        <p>سعی کنید کلمه کلیدی را اول متا تایتل بیاورید</p>
                                        <hr>
                                        <p>متا تایتل کوتاه سخت تر از متا تایتل بلند به نتایج اول گوگل می رسند مثلا (خرید
                                            سانتافه) سخت تر از (خرید سانتافه 2018 تهران)</p>
                                        <hr>
                                        <p>کلمه کلیدی قبلا برای مقاله دیگری استفاده نشده باشد (از طریق جستجوی سایت
                                            میتوانید مطلع شوید)</p>
                                        <hr>
                                        <p>جذاب باشد تا کاربران را ترغیب به کلیک کند</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">فهمیدم
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="meta_description">meta description</label><span class="ml-2 badge badge-danger"
                                                                                        style="cursor: pointer"
                                                                                        data-toggle="modal"
                                                                                        data-target="#meta_description_message">نکات مهم</span>
                            <textarea class="form-control" style="width: 100%; resize: none; height: 150px"
                                      name="meta_description"
                                      id="meta-description-input">{{$blog->meta_description}}</textarea>
                            <span id="meta-description-span"></span>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="meta_description_message" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">قوانین متا دیسکریپشن خوب</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>از کلمه کلیدی در متا دیسکریپشن هم استفاده شود</p>
                                        <hr>
                                        <p>وقتی کاربری در صفحه نتایج با 10 سایت روبرو میشود، مثل این است که دارد در یک
                                            خیابان که 10 مغازه با جنس مشابه دارد راه میرود. اینکه از بین این 10 مغازه به
                                            کدام یک وارد شود به عواملی مثل نام، طراحی ظاهری و ویترین مغازه ها بستگی
                                            دارد.
                                            ویترین مغازه در دنیای اینترنت همان توضیحات متا یک سایت در صفحه نتایج
                                            هستند.</p>
                                        <hr>
                                        <p>کال تو اکشن مشخص داشته باشد مثلا : با خواندن این مقاله میتوانید 0 تا 100
                                            آموزش تعویض لاستیک را یاد بگیرید</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">فهمیدم
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="meta_keywords">meta keywords (تگ ها با , از هم جدا شوند)</label>
                            <textarea class="form-control" style="width: 100%; resize: none; height: 150px"
                                      name="meta_keywords">{{$blog->meta_keywords}}</textarea>
                        </div>

                    </div>
                </div>

            </div>


            <div class="tab-pane fade" id="pills-image" role="tabpanel" aria-labelledby="pills-image-tab">

                <div class="row mt-2 p-4 justify-content-center align-items-center">

                    <div class="col-5 my-4">
                        <div class="custom-file-container" data-upload-id="myFirstImage">
                            <label>تصویر <a href="javascript:void(0)" class="custom-file-container__image-clear"
                                            title="Clear Image">x</a></label>
                            <label class="custom-file-container__custom-file">
                                <input required name="image" type="file"
                                       class="custom-file-container__custom-file__custom-file-input"
                                       accept="image/*">
                                <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>
                                <span class="custom-file-container__custom-file__custom-file-control"></span>
                            </label>
                            <div class="custom-file-container__image-preview"></div>
                        </div>
                    </div>
                    <div class="col-5 my-4 text-center">
                        <span class="badge badge-info">عکس فعلی :</span>
                        <img class="mt-2" style="width: 100%" src="{{asset('files/blog/images/'.$blog->image)}}">
                    </div>

                </div>


            </div>

        </div>


        <div class="row">
            <div class="col-10 text-center">
                <a id="store" class="btn btn-primary" href="">ثبت و انتشار مطلب</a>
                <a id="temp-store" class="btn btn-warning" href="">ذخیره موقت کن و بمان</a>
                <a id="temp-store-exit" class="btn btn-info" href="">ذخیره موقت کن و خارج شو</a>
            </div>
        </div>
    </form>

@endsection


@section('script')

    <script>
        $(document).ready(function () {
            $("#title-input").on('input', function () {
                if ($(this).val().length > 55) {
                    $("#title-span").css('color', '#f11f0b');
                } else {
                    $("#title-span").css('color', '#0bf100');
                }
                $("#title-span").text("55/" + $(this).val().length);
            });
            $("#meta-title-input").on('input', function () {
                if ($(this).val().length > 55) {
                    $("#meta-title-span").css('color', '#f11f0b');
                } else {
                    $("#meta-title-span").css('color', '#0bf100');
                }
                $("#meta-title-span").text("55/" + $(this).val().length);
            });
            $("#description-input").on('input', function () {
                if ($(this).val().length > 150) {
                    $("#description-span").css('color', '#f11f0b');
                } else {
                    $("#description-span").css('color', '#0bf100');
                }
                $("#description-span").text("150/" + $(this).val().length);
            });
            $("#meta-description-input").on('input', function () {
                if ($(this).val().length > 150) {
                    $("#meta-description-span").css('color', '#f11f0b');
                } else {
                    $("#meta-description-span").css('color', '#0bf100');
                }
                $("#meta-description-span").text("150/" + $(this).val().length);
            });
        });
    </script>

    <script src="{{asset('admin_c/plugins/file-upload/file-upload-with-preview.min.js')}}"></script>
    <script>
        var firstUpload = new FileUploadWithPreview('myFirstImage')
    </script>

    <script>

        $("#temp-store").on('click', function (e) {
            e.preventDefault();
            $('#blogform').attr('action', '{{route('blog.update',['id'=>$blog->id,'status'=>0,'exit'=>0])}}').submit();
        });
        $("#store").on('click', function (e) {
            e.preventDefault();
            $('#blogform').attr('action', '{{route('blog.update',['id'=>$blog->id,'status'=>1,'exit'=>1])}}').submit();
        });
        $("#temp-store-exit").on('click', function (e) {
            e.preventDefault();
            $('#blogform').attr('action', '{{route('blog.update',['id'=>$blog->id,'status'=>0,'exit'=>1])}}').submit();
        });

        // setInterval(function () {
        //     $('#blogform').attr('action', 'https://tondiran.ir/admin/blog/update/' + id + '/0/0').submit();
        // }, 600000)

    </script>


    <script>
        CKEDITOR.replace('description', {
            language: 'fa',
            filebrowserImageBrowseUrl: '{{asset('/laravel-filemanager?type=Images')}}',
            filebrowserImageUploadUrl: '{{asset('/laravel-filemanager/upload?type=Images&_token=')}}',
            filebrowserBrowseUrl: '{{asset('/laravel-filemanager?type=Files')}}',
            filebrowserUploadUrl: '{{asset('/laravel-filemanager/upload?type=Files&_token=')}}'
        });
    </script>

    <script>

        var tagCount = parseInt($('#tagcount').html());

        var typingTagTimer;                //timer identifier
        var doneTypingTagInterval = 100;  //time in ms, 5 second for example

        //on keyup, start the countdown
        $("#tags").on('keyup', function () {
            clearTimeout(typingTagTimer);
            typingTagTimer = setTimeout(doneTypingTag, doneTypingTagInterval);
        });

        //on keydown, clear the countdown
        $("#tags").on('keydown', function () {
            $('.show-tag-result').html('<span>تگ</span><hr><span>در حال جستجو...</span>')
            clearTimeout(typingTagTimer);
        });

        //user is "finished typing," do something
        function doneTypingTag() {
            if (tagCount < 10) {
                $.ajax({
                    method: 'get',
                    url: '/tag-search/' + $("#tags").val(),
                    success: function (msg) {
                        $('.show-tag-result').html(msg);
                    }
                })
            } else {
                $('.show-tag-result').html("شما مجاز به انتخاب 10 تگ می باشید");
            }
        }

        function selectTag(value) {
            tagCount += 1;
            $('.show-tag-result').html("");
            $('#tagcount').html("10/" + tagCount);
            $('#selected-tags').val($('#selected-tags').val() + "" + value.innerHTML);
            $('#tags').val("");
        }

        $('#cleartags').on('click', function () {
            tagCount = 0;
            $('#tagcount').html("10/" + tagCount);
            $('#selected-tags').val("");
        })

    </script>

    <script>
        $('#model').change(function () {
            var model = $(this).find('option:selected').text();
        })

        $('#brand').change(function () {
            $('#model').html('').fadeIn(800).append('<option value="{{null}}">لطفا کمی صبر کنید ...</option>');
            var id = $(this).find('option:selected').val();
            $.ajax({
                method: 'get',
                url: '/getModelsCreate',
                data: {
                    "id": id,
                },
                success: function (msg) {
                    $('#model').html(msg);
                }
            })
        });
    </script>

@endsection
