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
                <h1 class="m-0">Create Author</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active">Authors</li>
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
                <form method="POST" action="/authors" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="inputName">Name</label>
                            <input type="text" class="form-control" name="name" id="inputName" placeholder="Enter Name" required>
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
@endsection