@extends('admin_index')

@section('style')
    <link href="{{asset('admin_c/plugins/file-upload/file-upload-with-preview.min.css')}}" rel="stylesheet"
          type="text/css"/>
@endsection

@section('content')

    <form action="{{route('race.store')}}" method="POST" role="form" enctype="multipart/form-data">
        @csrf
        <legend>مسابقه جدید</legend>

        <div class="form-group">
            <label for="title">عنوان مسابقه</label>
            <input type="text" class="form-control" name="title" id="title" placeholder="عنوان مسابقه را وارد کنید">
        </div>

        <div class="form-group">
            <label for="body">متن مسابقه</label>
            <textarea class="form-control" name="title" id="title"></textarea>
        </div>

        <div class="form-group">
            <label for="date">چند روز ؟</label>
            <input type="number" class="form-control" name="date" id="date" placeholder="مسابقه چند روز طول بکشد؟">
        </div>

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

@endsection

@section('script')
    <script src="{{asset('admin_c/plugins/file-upload/file-upload-with-preview.min.js')}}"></script>

    <script>
        var firstUpload = new FileUploadWithPreview('myFirstImage')
    </script>
@endsection
