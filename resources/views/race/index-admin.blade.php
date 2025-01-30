@extends('admin_index')

@section('title')
    مسابقات
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{asset('admin_c/plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin_c/plugins/table/datatable/dt-global_style.css')}}">
    <link href="{{asset('admin_c/plugins/file-upload/file-upload-with-preview.min.css')}}" rel="stylesheet"
          type="text/css"/>
@endsection


@section('content')

    <div class="row justify-content-center">

        <div class="col-12 text-center">
            <a class="btn btn-primary" href="" data-toggle="modal"
               data-target="#create">ایجاد مسابقه</a>
        </div>

        <div class="modal fade" id="create" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">ایجاد مسابقه</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('race.store')}}" method="POST" role="form" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="title">عنوان مسابقه</label>
                                <input type="text" class="form-control" name="title" id="title"
                                       placeholder="عنوان مسابقه را وارد کنید">
                            </div>

                            <div class="form-group">
                                <label for="body">متن مسابقه</label>
                                <textarea class="form-control" name="body" id="body"
                                          placeholder="متن مسابقه را وارد کنید"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="date">چند روز ؟</label>
                                <input type="number" class="form-control" name="date" id="date"
                                       placeholder="مسابقه چند روز طول بکشد؟">
                            </div>

                            <div class="form-group">
                                <label>تگ ها</label>
                                <input class="form-control" id="tags" type="text"
                                       placeholder="تگ های مرتبط با مطلب را مشخص کنید">
                            </div>

                            <div class="form-group">
                                <input class="form-control" dir="rtl" name="tags" id="selected-tags"
                                       value="{{old('tags')}}"
                                       readonly placeholder="تگ های انتخاب شده">
                                <span id="tagcount" class="badge badge-success"></span>
                                <span id="cleartags" style="cursor: pointer"
                                      class="badge badge-danger">پاک کردن همه</span>
                            </div>

                            <div class="show-tag-result px-2"></div>

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

                            <button type="submit" class="btn btn-primary">ایجاد</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="table-responsive mb-4 mt-4">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>تصویر</th>
                        <th>کاربر</th>
                        <th>عنوان مسابقه</th>
                        <th>زمان باقی مانده</th>
                        <th>تعداد رای</th>
                        <th>بازدید</th>
                        <th>منتشر شده؟</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($races as $key => $race)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>
                                <img src="{{asset('files/race/images/'.$race->image)}}" style="width: 100px">
                            </td>
                            <td>{{$race->user->name}}({{$race->creatorToString()}})</td>
                            <td>{{$race->title}}</td>
                            <td>{{$race->untilEndHour()}} ساعت <span class="badge badge-secondary">{{$race->untilEndMin()}} دقیقه </span>
                            <td>{{$race->voteCount()}}</td>
                            <td>{{$race->seen_count}}</td>
                            <td><span
                                    class="badge {{$race->status? "badge-success" : "badge-danger"}}">{{$race->status? "بله" : "خیر"}}</span>
                                <a class="btn btn-outline-info"
                                   href="{{$race->status? route('race.status.false',$race->id): route('race.status.true',$race->id)}}">{{$race->status? "غیرفعال کردن":"منتشر کردن"}}</a>
                            </td>
                            <td>
                                <a class="btn btn-danger" href="" data-toggle="modal"
                                   data-target="#delete-{{$race->id}}">حذف</a>
                                <a class="btn btn-warning" href="" data-toggle="modal"
                                   data-target="#edit-{{$race->id}}">ویرایش</a>
                                <a class="btn btn-secondary" href="{{route('admin.race.options',$race->id)}}">گزینه
                                    ها</a>
                            </td>
                        </tr>

                        <div class="modal fade" id="edit-{{$race->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">ویرایش مسابقه</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{route('race.update',$race->id)}}" method="POST" role="form"
                                              enctype="multipart/form-data">
                                            {{method_field('put')}}
                                            @csrf
                                            <div class="form-group">
                                                <label for="title">عنوان مسابقه</label>
                                                <input type="text" class="form-control" name="title" id="title"
                                                       placeholder="عنوان مسابقه را وارد کنید" value="{{$race->title}}">
                                            </div>

                                            <div class="form-group">
                                                <label for="body">متن مسابقه</label>
                                                <textarea class="form-control" name="body"
                                                          id="body">{{$race->body}}</textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="date">چند روز ؟</label>
                                                <input type="number" class="form-control" name="date" id="date"
                                                       placeholder="در صورت وارد کردن زمان جدید محاسبه میشود">
                                            </div>


                                            <div class="form-group">
                                                @foreach($race->tags() as $tag)
                                                    <span class="badge badge-info"><a data-toggle="modal"
                                                                                      data-target="#deletetag-{{$tag->id}}"><img
                                                                style="width: 25px; cursor: pointer" class="mx-1"
                                                                src="{{asset('files/other/images/remove.png')}}"></a>#{{$tag->title}}</span>
                                                    <!-- tag delete modal -->
                                                    <div class="modal fade" id="deletetag-{{$tag->id}}" tabindex="-1"
                                                         role="dialog"
                                                         aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLabel">حذف
                                                                        تگ</h5>
                                                                    <button type="button" class="close"
                                                                            data-dismiss="modal"
                                                                            aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    آیا از حذف تگ {{$tag->title}} از این مسابقه اطمینان
                                                                    دارید؟
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">
                                                                        نه
                                                                    </button>
                                                                    <a href="{{route('tag.destroy',['tid'=>$tag->id,'pid'=>$race->id ,'class'=>'race'])}}"
                                                                       class="btn btn-danger">بله</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="show-tag-result px-2"></div>

                                            <div class="row align-items-center">
                                                <div class="col-6">
                                                    <input type="file" name="image" class="form-control">
                                                </div>
                                                <div class="col-6">
                                                    <span>تصویر فعلی:</span>
                                                    <img src="{{asset('files/race/images/'.$race->image)}}"
                                                         style="width: 90%">
                                                </div>
                                            </div>


                                            <button type="submit" class="btn btn-primary">ویرایش</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="delete-{{$race->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">حذف مسابقه</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>آیا از حذف مسابقه اطمینان دارید؟</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">خیر
                                        </button>
                                        <a href="{{route('race.destroy',$race->id)}}" class="btn btn-danger">بله</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>



@endsection



@section('script')
    <script src="{{asset('admin_c/plugins/table/datatable/datatables.js')}}"></script>

    <script>
        $('#zero-config').DataTable({
            "oLanguage": {
                "oPaginate": {
                    "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>',
                    "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>'
                },
                "sInfo": "صفحه _PAGE_ از _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "جستجو کنید...",
                "sLengthMenu": "نتایج :  _MENU_",
            },
            "stripeClasses": [],
            "lengthMenu": [7, 10, 20, 50],
            "pageLength": 7
        });
    </script>

    <script src="{{asset('admin_c/plugins/file-upload/file-upload-with-preview.min.js')}}"></script>

    <script>
        var firstUpload = new FileUploadWithPreview('myFirstImage')
    </script>


    <script>
        var tagCount = 0;

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

@endsection
