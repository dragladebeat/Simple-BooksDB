@extends('main.base')
@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="/assets/AdminLTE-3.1.0/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="/assets/AdminLTE-3.1.0/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="/assets/AdminLTE-3.1.0/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

<style>
    .thumbnail {
        width: 240px;
        height: 240px;
        background-position: center center;
        background-repeat: no-repeat;
        overflow: hidden;
        object-fit: cover;
    }

    .center {
        margin-left: auto;
        margin-right: auto;
    }
</style>
@endsection
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Authors</h1>
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
            {{ session('error')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
    </div><!-- /.row -->

    <div class="row mb-2 m-0 col-12">
        @if(!empty(session('success')))
        <div class="col-12 alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
    </div><!-- /.row -->
    <div class="row mb-2 m-0">
        <div class="col-1">
            <button class="btn btn-primary" onclick="location.href = '/authors/create'">New Author</button>
        </div><!-- /.col -->
    </div><!-- /.row -->

    <div class="row m-0">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4 class="card-title">Author List</h4>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- /.card-body -->
                                    @foreach($data['data'] as $author)
                                    <tr>
                                        <td>{{ $author->id }}</td>
                                        <td>{{ $author->name }}</td>
                                        <td>
                                            <div class="row">
                                                <span><button class="btn btn-success ml-1 mr-1" onclick="startAction('update', {{ $author->id }})"><i class="fas fa-edit"></i></button></span>
                                                <span>
                                                    <form method="POST" action="/authors/{{ $author->id }}">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger ml-1 mr-1"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </span>
                                            </div>


                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- /.container-fluid -->
<!-- /.content -->
@endsection

@section('js')
<!-- DataTables  & Plugins -->
<script src="/assets/AdminLTE-3.1.0/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/assets/AdminLTE-3.1.0/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="/assets/AdminLTE-3.1.0/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="/assets/AdminLTE-3.1.0/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="/assets/AdminLTE-3.1.0/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="/assets/AdminLTE-3.1.0/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script>
    function startAction(type, id) {
        switch (type) {
            case "update":
                url = "/authors/" + id + "/edit"
                window.location.href = url;
                break;
            case "delete":
                break;
            default:
                return;
        }
    }

    $(function() {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
</script>
@endsection