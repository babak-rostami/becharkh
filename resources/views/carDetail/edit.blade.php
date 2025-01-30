@extends('admin_index')

@section('style')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
@endsection

@section('content')

    <div class="row justify-content-center p-2">
        <div class="col-10">

            <form action="{{route('car.detail.update',['model_id'=>$model->id , 'detail_id'=>$detail->id])}}"
                  method="post"
                  role="form" enctype="multipart/form-data">
                @csrf

                {{method_field('PUT')}}

                <legend>مشخصات {{$model->title}} {{$model->brand->title}}</legend>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">تصویر</label>
                            <input type="file" class="form-control" name="image"
                                   id="image">
                        </div>
                    </div>
                    <div class="col-6">
                        <img style="width: 200px" src="{{asset('files/carmodel/images/'.$detail->image)}}">
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">سال ساخت</label>
                            <input type="text" class="form-control" value="{{$detail->production_year}}"
                                   name="production_year"
                                   id="production_year">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">سیلندر</label>
                            <input type="text" class="form-control" value="{{$detail->cylinder}}" name="cylinder"
                                   id="cylinder">
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="form-group">
                            <label for="">حجم موتور</label>
                            <input type="text" class="form-control" value="{{$detail->engine_volume}}"
                                   name="engine_volume"
                                   id="engine_volume">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">شتاب</label>
                            <input type="text" class="form-control" value="{{$detail->acceleration}}"
                                   name="acceleration" id="acceleration">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">قدرت موتور</label>
                            <input type="text" class="form-control" value="{{$detail->engine_power}}"
                                   name="engine_power" id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">گشتاور</label>
                            <input type="text" class="form-control" value="{{$detail->torque}}" name="torque" id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">حداکثر سرعت</label>
                            <input type="text" class="form-control" value="{{$detail->max_speed}}" name="max_speed"
                                   id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">گیربکس</label>
                            <input type="text" class="form-control" value="{{$detail->gearbox}}" name="gearbox" id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">دیفرانسیل</label>
                            <input type="text" class="form-control" value="{{$detail->differential}}"
                                   name="differential" id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">کلاس بدنه</label>
                            <input type="text" class="form-control" value="{{$detail->body_class}}" name="body_class"
                                   id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">وزن خودرو</label>
                            <input type="text" class="form-control" value="{{$detail->car_weight}}" name="car_weight"
                                   id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">مصرف سوخت</label>
                            <input type="text" class="form-control" value="{{$detail->fuel_consumption}}"
                                   name="fuel_consumption" id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">حجم باک</label>
                            <input type="text" class="form-control" value="{{$detail->buck_volume}}" name="buck_volume"
                                   id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">کشور سازنده</label>
                            <input type="text" class="form-control" value="{{$detail->country}}" name="country" id="">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">سیستم ترمز</label>
                            <input type="text" class="form-control" value="{{$detail->brake_system}}"
                                   name="brake_system" id="">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">سیستم مدیا</label>
                            <input type="text" class="form-control" value="{{$detail->media_system}}"
                                   name="media_system" id="">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">سیستم آینه و شیشه</label>
                            <input type="text" class="form-control" value="{{$detail->mirror_glass_system}}"
                                   name="mirror_glass_system"
                                   id="">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">روشنایی</label>
                            <input type="text" class="form-control" value="{{$detail->lighting}}" name="lighting" id="">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">رفاهی</label>
                            <input type="text" class="form-control" value="{{$detail->welfare}}" name="welfare" id="">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">نقاط قوت</label>
                            <textarea name="power_points"
                                      style="width: 100%; height: 150px; resize: none">{{$detail->power_points}}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">نقاط ضعف</label>
                            <textarea name="weak_points"
                                      style="width: 100%; height: 150px; resize: none">{{$detail->weak_points}}</textarea>

                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">سایر امکانات</label>
                            <textarea name="others"
                                      style="width: 100%; height: 150px; resize: none">{{$detail->others}}</textarea>

                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">مشخصات کلی</label>
                            <textarea name="description"
                                      class="form-control">{{$detail->description}}</textarea>

                        </div>
                    </div>
                </div>


                <button type="submit" class="btn btn-primary">ثبت</button>
            </form>

        </div>
    </div>

@endsection


@section('script')
    <script>
        CKEDITOR.replace('description', {
            language: 'fa',
            filebrowserImageBrowseUrl: '{{asset('/laravel-filemanager?type=Images')}}',
            filebrowserImageUploadUrl: '{{asset('/laravel-filemanager/upload?type=Images&_token=')}}',
            filebrowserBrowseUrl: '{{asset('/laravel-filemanager?type=Files')}}',
            filebrowserUploadUrl: '{{asset('/laravel-filemanager/upload?type=Files&_token=')}}'
        });
    </script>
@endsection
