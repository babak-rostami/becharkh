<div class="col-12">
    @if (session('success'))
        <p class="alert alert-success text-center">{{ session('success') }}</p>
    @endif
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <p class="alert alert-danger text-center">{{ $error }}</p>
        @endforeach
    @endif
</div>
