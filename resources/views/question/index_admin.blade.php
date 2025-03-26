@extends('admin_index')

@section('title')
    پرسش ها
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_c/plugins/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_c/plugins/table/datatable/dt-global_style.css') }}">
@endsection


@section('content')
    <div class="row justify-content-center">


        @if (isset($category))
            <div class="col-10 text-center">
                <h4>انجمن های با موضوع {{ $category->title }}</h4>
                <a class="btn btn-primary" href="{{ route('question.index.admin') }}">همه انجمن ها</a>
                <hr>
            </div>
        @endif

        <div class="col-10 text-center">

            <a class="btn btn-danger" href="{{ route('admin.question.create') }}">ایجاد سوال توسط ادمین</a>

            <div class="table-responsive mb-4 mt-4">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>عنوان سوال</th>
                            <th>نمایش در گوگل</th>
                            <th>تعداد بازدید</th>
                            <th>زمان</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questions as $key => $question)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $question->title }}
                                    @if ($question->status == 1)
                                        <span class="text-success">تایید شده</span>
                                    @else
                                        <span class="text-danger">تایید نشده</span>
                                    @endif
                                    <span class="badge badge-primary">{{ $question->answer_count ?? 0 }} نظر</span>
                                </td>
                                <td>
                                    @if ($question->google_index == 1)
                                        <span class="text-success">در گوگل بیاد</span>
                                    @else
                                        <span class="text-danger"> در گوگل نیاد</span>
                                    @endif
                                </td>
                                <td>{{ $question->seen_count }}</td>
                                <td>{{ jdate($question->created_at)->ago() }}</td>
                                <td>
                                    <a class="btn btn-danger" href="" data-toggle="modal"
                                        data-target="#delete-{{ $question->id }}">حذف</a>
                                    <a class="btn btn-warning"
                                        href="{{ route('admin.question.edit', $question->id) }}">ویرایش</a>
                                    <a href="{{ route('admin.question.email', $question->id) }}" class="btn btn-dark">ارسال
                                        ایمیل</a>
                                    <a target="_blank" href="{{ route('question.show', $question->slug2) }}"
                                        class="btn btn-secondary">مشاهده</a>
                                    <a class="btn btn-light"
                                        href="{{ route('question.answers.admin', $question->id) }}">نظرها</a>
                                </td>
                            </tr>

                            <div class="modal fade" id="delete-{{ $question->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">حذف پرسش</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('question.destroy', $question) }}" method="post"
                                                role="form">
                                                @csrf
                                                {{ method_field('DELETE') }}

                                                <p>آیا از حذف پرسش اطمینان دارید؟</p>

                                                <button type="submit" class="btn btn-danger">بله</button>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف
                                                </button>
                                            </form>
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
    <script src="{{ asset('admin_c/plugins/table/datatable/datatables.js') }}"></script>

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
@endsection
