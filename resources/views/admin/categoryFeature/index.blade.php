@extends('index')

@section('title')
    @if (isset($category))
        ویژگی های دسته {{ $category->title }}
    @else
        مدیریت ویژگی ها
    @endif
@endsection

@section('style')
@endsection


@section('content')
    <div class="row justify-content-center">

        <div class="col-12 text-center">
            @if (session('success'))
                <p class="alert alert-success text-center">{{ session('success') }}</p>
            @endif

            <a class="btn btn-primary mt-4" href="{{ route('create.feature.admin') }}">ایجاد ویژگی جدید</a>

            <table class="table table-hover my-4" style="width:100%">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>عنوان</th>
                        <th>دسته بندی</th>
                        <th>تایید شده؟</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($features as $key => $feu)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $feu->title }}</td>
                            <td>
                                @foreach ($feu->categories as $c)
                                    {{ $c->title }}
                                @endforeach
                            </td>
                            <td>{{ $feu->status == 1 ? 'بله' : 'خیر' }}</td>
                            <td>
                                <a class="btn btn-warning" href="{{ route('edit.feature.admin', $feu->id) }}">ویرایش</a>
                                <a class="btn btn-primary"
                                    href="{{ route('category.features.admin', ['cat_id' => $feu->categories->first()->id, 'feature_id' => $feu->id]) }}">زیرمجموعه
                                    ها</a>
                                <a class="btn btn-dark" href="{{ route('feature.items.admin', $feu->id) }}">آیتم
                                    ها</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>


        <!-- select parent feature Modal -->
        <div class="modal fade" id="feature" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if ($allFeatures->count() > 0)
                            @foreach ($allFeatures as $fe)
                                <div class="row my-2 p-2" style="background-color: #f1f1f1">
                                    <div style="cursor: pointer" onclick="featureDrop('{{ $fe->id }}')"
                                        id="feature-drop-{{ $fe->id }}" class="col-9">
                                        {{ $fe->title }}
                                    </div>
                                    <div class="col-3"
                                        onclick="selectFeature('{{ $fe->id }}','{{ $fe->title }}','')"
                                        style="background-color: #2c2c2c ; cursor: pointer">
                                        انتخاب
                                    </div>
                                </div>
                                <div id="feu-children-{{ $fe->id }}"></div>
                            @endforeach
                        @else
                            <div class="alert alert-warning">دسته ای پیدا نشد</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection



@section('script')
@endsection
