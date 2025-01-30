@extends('index')

@section('title')
    مدیریت آیتم {{ $item->title }}
@endsection

@section('style')
    <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script>
@endsection

@section('content')
    <div class="row justify-content-center bg-wht p-3">

        <div class="col-12 text-center">
            <a class="btn btn-secondary" href="{{ route('item.images.admin', $item->id) }}">تصاویر</a>
            <a class="btn btn-danger" href="" data-toggle="modal" data-target="#delete-{{ $item->id }}">حذف</a>

            <div class="modal fade" id="delete-{{ $item->id }}" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            مطمئنید که میخواهید حذف شود؟
                            <br>
                            تنها در صورتی که آیتم تایید نشده است حذف کنید!
                            <br>
                            یا اینکه تازه اضافه کرده اید و مطمئن هستید که ایندکس نشده است
                            <br>
                            <a href="{{ route('feature.item.destroy.admin', $item->id) }}" class="btn btn-danger">حذف</a>
                            <a class="btn btn-primary" href="" data-dismiss="modal">بیخیال</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 text-right mt-4">
            <form action="{{ route('feature.item.update.admin', $item->id) }}" enctype="multipart/form-data" method="post"
                role="form">
                @csrf

                {{ method_field('PUT') }}

                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="title">نام ویژگی</label>
                            <input type="text" class="form-control" name="title" id="title"
                                value="{{ $item->title }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="title_en">نام انگلیسی ویژگی</label>
                            <input type="text" class="form-control" name="title_en" id="title_en"
                                value="{{ $item->title_en }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="slug">slug</label>
                            <input type="text" class="form-control" name="slug" id="slug"
                                value="{{ $item->slug }}">
                        </div>
                    </div>
                    @if ($item->status == 0)
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="slug">slug</label>
                                <input type="text" class="form-control" name="slug" id="slug"
                                    value="{{ $item->slug }}">
                            </div>
                        </div>
                    @endif
                    <div class="col-12 col-sm-6">
                        <label for="status">تایید شده</label>
                        <select class="form-control" name="status" id="status">
                            <option value="0" @if ($item->status == '0') selected @endif>
                                خیر
                            </option>
                            <option value="1" @if ($item->status == '1') selected @endif>
                                بله
                            </option>
                        </select>
                    </div>
                    @if ($parent_itmes != null)
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="parent_id">آیتم بالایی</label>
                                <select class="form-control" name="parent_id" id="parent_id">
                                    @foreach ($parent_itmes as $i)
                                        <option @if ($i->id == $item->parent_id) selected @endif
                                            value="{{ $i->id }}">
                                            {{ $i->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="col-12">
                        <div class="form-group">
                            <label for="category_id">دسته بندی</label>
                            <select name="category_id" class="form-control">
                                @foreach ($item->feature->categories as $category)
                                    <option @if ($category->id == $item->category_id) selected @endif value="{{ $category->id }}">
                                        {{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="crl_price_url">crawl price url</label>
                            <input type="text" value="{{ $item->crl_price_url }}" class="form-control"
                                name="crl_price_url" id="crl_price_url">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="title_in_rtable">متا تایتل میزگرد</label>
                            <input type="text" value="{{ $item->title_in_rtable }}" class="form-control"
                                name="title_in_rtable" id="title_in_rtable">
                        </div>
                        <div class="form-group">
                            <label for="desc_in_rtable">متا دسکریپشن میزگرد</label>
                            <textarea class="form-control" name="desc_in_rtable" id="desc_in_rtable">{{ $item->desc_in_rtable }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>دسکریپشن میزگرد ادیتور</label>
                            <textarea class="form-control ckeditor" name="desc_in_rtable_editor">{{ $item->desc_in_rtable_editor }}</textarea>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="title_in_comment">متا تایتل نظرات کاربران</label>
                            <input type="text" value="{{ $item->title_in_comment }}" class="form-control"
                                name="title_in_comment" id="title_in_comment">
                        </div>
                        <div class="form-group">
                            <label for="desc_in_comment">متا دسکریپشن نظرات کاربران</label>
                            <textarea class="form-control" name="desc_in_comment" id="desc_in_comment">{{ $item->desc_in_comment }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>دسکریپشن نظرات ادیتور</label>
                            <textarea class="form-control ckeditor" name="desc_in_comment_editor">{{ $item->desc_in_comment_editor }}</textarea>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="title_in_ads">متا تایتل آگهی ها</label>
                            <input type="text" value="{{ $item->title_in_ads }}" class="form-control"
                                name="title_in_ads" id="title_in_ads">
                        </div>
                        <div class="form-group">
                            <label for="desc_in_ads">متا دسکریپشن آگهی ها</label>
                            <textarea class="form-control" name="desc_in_ads" id="desc_in_ads">{{ $item->desc_in_ads }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>دسکریپشن آگهی ادیتور</label>
                            <textarea class="form-control ckeditor" name="desc_in_ads_editor">{{ $item->desc_in_ads_editor }}</textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-2 w-100">ثبت تغییرات</button>
            </form>
        </div>
    </div>
@endsection



@section('script')
    <script>
        const editors = document.querySelectorAll('.ckeditor');
        editors.forEach(editor => {
            ClassicEditor.create(editor, {
                language: "fa",
            });
        });
    </script>
@endsection
