@extends('main.base')
@section('css')
<style>
</style>
@endsection
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Book</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active">Books</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="container-fluid">
    <div class="row mb-2 m-0 col-12">
        @if(!empty(session('error')))
        <div class="col-12 alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        @if(!empty($data['error']))
        <div class="col-12 alert alert-danger alert-dismissible fade show" role="alert">
            {{ $data['error'] }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
    </div><!-- /.row -->

    <div class="row m-0">
        <div class="col-12">
            <div class="card card-primary">
                <!-- /.card-header -->
                <!-- form start -->
                <form method="POST" action="/books/{{ $data['book']->id }}" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="exampleInputFile">Cover</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="cover" id="exampleInputFile">
                                    <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                </div>
                                <div class="input-group-append">
                                    <span class="input-group-text">Upload</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputTitle">Book Title</label>
                            <input type="text" class="form-control" name="title" id="inputTitle" placeholder="Enter Title" required value="{{ $data['book']->title ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label for="inputSummary">Summary</label>
                            <textarea class="form-control" name="summary" id="inputSummary" rows="3" placeholder="Summary">{{ $data['book']->summary }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Author</label>
                            <select class="form-control" name="author">
                                <option value="">Choose Author</option>
                                @foreach($data['authors'] as $author)
                                <option value="{{ $author->id }}" {{ !empty($data['book']->author) ? $author->id == $data['book']->author->id ? 'selected' : '' :'' }}>{{ $author->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><!-- /.container-fluid -->
<!-- /.content -->
@endsection
@section('js')
<!-- bs-custom-file-input -->
<script src="/assets/AdminLTE-3.1.0/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>

<script>
    $(function() {
        bsCustomFileInput.init();
    });
</script>
@endsection