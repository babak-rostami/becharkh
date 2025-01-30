@extends('admin_index')

@section('style')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
@endsection

@section('content')

    <div class="row justify-content-center p-2">
        <div class="col-10">

            <form action="{{route('car.detail.store',$model->id)}}"
                  method="post"
                  role="form" enctype="multipart/form-data">
                @csrf
                <legend>مشخصات {{$model->title}} {{$model->brand->title}}</legend>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">تصویر</label>
                            <input type="file" class="form-control" value="{{old('production_year')}}" name="image"
                                   id="image">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">سال ساخت</label>
                            <input type="text" class="form-control" value="{{old('production_year')}}"
                                   name="production_year"
                                   id="production_year">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">سیلندر</label>
                            <input type="text" class="form-control" value="{{old('cylinder')}}" name="cylinder"
                                   id="cylinder">
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="form-group">
                            <label for="">حجم موتور</label>
                            <input type="text" class="form-control" value="{{old('engine_volume')}}"
                                   name="engine_volume"
                                   id="engine_volume">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">شتاب</label>
                            <input type="text" class="form-control" value="{{old('acceleration')}}" name="acceleration"
                                   id="acceleration">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">قدرت موتور</label>
                            <input type="text" class="form-control" value="{{old('engine_power')}}" name="engine_power"
                                   id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">گشتاور</label>
                            <input type="text" class="form-control" value="{{old('torque')}}" name="torque" id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">حداکثر سرعت</label>
                            <input type="text" class="form-control" value="{{old('max_speed')}}" name="max_speed" id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">گیربکس</label>
                            <input type="text" class="form-control" value="{{old('gearbox')}}" name="gearbox" id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">دیفرانسیل</label>
                            <input type="text" class="form-control" value="{{old('differential')}}" name="differential"
                                   id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">کلاس بدنه</label>
                            <input type="text" class="form-control" value="{{old('body_class')}}" name="body_class"
                                   id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">وزن خودرو</label>
                            <input type="text" class="form-control" value="{{old('car_weight')}}" name="car_weight"
                                   id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">مصرف سوخت</label>
                            <input type="text" class="form-control" value="{{old('fuel_consumption')}}"
                                   name="fuel_consumption" id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">حجم باک</label>
                            <input type="text" class="form-control" value="{{old('buck_volume')}}" name="buck_volume"
                                   id="">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">کشور سازنده</label>
                            <input type="text" class="form-control" value="{{old('country')}}" name="country" id="">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">سیستم ترمز</label>
                            <input type="text" class="form-control" value="{{old('brake_system')}}" name="brake_system"
                                   id="">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">سیستم مدیا</label>
                            <input type="text" class="form-control" value="{{old('media_system')}}" name="media_system"
                                   id="">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">سیستم آینه و شیشه</label>
                            <input type="text" class="form-control" value="{{old('mirror_glass_system')}}"
                                   name="mirror_glass_system"
                                   id="">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">روشنایی</label>
                            <input type="text" class="form-control" value="{{old('lighting')}}" name="lighting" id="">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">رفاهی</label>
                            <input type="text" class="form-control" value="{{old('welfare')}}" name="welfare" id="">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">نقاط قوت</label>
                            <textarea name="power_points"
                                      style="width: 100%; height: 150px; resize: none">{{old('power_points')}}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">نقاط ضعف</label>
                            <textarea name="weak_points"
                                      style="width: 100%; height: 150px; resize: none">{{old('weak_points')}}</textarea>

                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">سایر امکانات</label>
                            <textarea name="others"
                                      style="width: 100%; height: 150px; resize: none">{{old('others')}}</textarea>

                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">مشخصات کلی</label>
                            <textarea name="description"
                                      class="form-control">{{old('description')}}</textarea>

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
