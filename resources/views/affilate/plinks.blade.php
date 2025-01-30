@extends('admin_index')

@section('title')
    مدیریت لینک های عمومی
@endsection

@section('style')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <form action="{{ route('affilate.store.public.link.admin') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="title">عنوان</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="عنوان را وارد کنید">
                </div>
                <div class="form-group">
                    <label for="link">لینک عمومی</label>
                    <input type="text" class="form-control" id="link" name="link"
                        placeholder="لینک عمومی را وارد کنید">
                </div>
                <input class="btn btn-primary w-100" type="submit" value="ثبت">
            </form>
            <hr>
            @foreach ($plinks as $plink)
                <div>
                    <span>{{ $plink->title }}</span>
                    <a class="btn btn-primary" href="" data-toggle="modal" data-dismiss="modal"
                        data-target="#edit-publink-{{ $plink->id }}">ویرایش</a>
                    <a class="btn btn-danger" href="" data-toggle="modal" data-dismiss="modal"
                        data-target="#delete-publink-{{ $plink->id }}">حذف</a>
                </div>

                <div class="modal fade" id="edit-publink-{{ $plink->id }}" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <form action="{{ route('affilate.update.public.link.admin', $plink->id) }}"
                                            method="post">
                                            @csrf
                                            {{ method_field('PUT') }}
                                            <div class="form-group">
                                                <label for="title">عنوان</label>
                                                <input type="text" class="form-control" id="title" name="title"
                                                    value="{{ $plink->title }}" placeholder="عنوان را وارد کنید">
                                            </div>
                                            <div class="form-group">
                                                <label for="link">لینک عمومی</label>
                                                <input type="text" class="form-control" id="link" name="link"
                                                    value="{{ $plink->link }}" placeholder="لینک عمومی را وارد کنید">
                                            </div>
                                            <input class="btn btn-primary w-100" type="submit" value="ثبت">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="delete-publink-{{ $plink->id }}" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <form action="{{ route('affilate.delete.public.link.admin') }}" method="post">
                                            @csrf
                                            {{ method_field('DELETE') }}
                                            <span>مطمئن هستید میخواهید لینک حذف شود؟</span>
                                            <input type="hidden" name="plink_id" value="{{ $plink->id }}">
                                            <button class="btn btn-dark w-50">خیر</button>
                                            <input class="btn btn-danger" type="submit" value="بله">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection



@section('script')
@endsection
