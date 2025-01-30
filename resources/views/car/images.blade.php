@extends('index')


@section('title')
    تصاویر آگهی
@endsection


@section('style')
    <meta name="_token" content="{{csrf_token()}}"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/min/dropzone.min.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.js"></script>

    <meta name="robots" content="noindex">

@endsection


@section('content')

    <div class="row justify-content-center">

        @if(session('success'))
            <div>
                <p class="alert alert-success text-center">{{session('success')}}</p>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p style="color: #000000">{{$error}}</p>
                @endforeach
            </div>
        @endif


        <div class="col-12 text-center mt-4 mb-5">

            <form method="post"
                  action="{{route('ads.image.store',['slug'=> request()->slug, 'random_id'=>request()->random_id])}}"
                  enctype="multipart/form-data"
                  class="dropzone mt-4" id="dropzone">
                @csrf
                <div class="dz-message" data-dz-message><span>تصاویر آگهی را انتخاب کنید</span></div>
            </form>

            <a class="btn btn-success mt-4" href="{{route('mylist')}}">مشاهده آگهی</a>
        </div>

    </div>

    @if(!$ad->images->isEmpty())
        <div class="row shadow-lg mx-5 mt-4 p-2 mb-5">

            @foreach($ad->images as $image)
                <div class="col-12 col-md-6 col-lg-3 mb-4 text-center">
                    <img style="border-radius: 50% ; width: 150px; height: 150px"
                         src="{{asset('files/advertise/images/'.$image->image)}}">
                    <br>
                    <a href="" data-toggle="modal" data-target="#exampleModal-{{$image->id}}"
                       class="btn btn-danger mt-2">حذف</a>
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal-{{$image->id}}" tabindex="-1" role="dialog"
                         aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">حذف تصویر</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>آیا از حذف تصویر اطمینان دارید؟</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
                                    <a href="{{route('ad.image.delete',$image->id)}}" class="btn btn-danger">بله</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    @endif

@endsection


@section('script')

@endsection
