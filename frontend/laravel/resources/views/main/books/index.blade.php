@extends('main.base')
@section('css')
<!-- Ekko Lightbox -->
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
                <h1 class="m-0">Books</h1>
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
            <button class="btn btn-primary" onclick="location.href = '/books/create'">New Book</button>
        </div><!-- /.col -->
    </div><!-- /.row -->

    <div class="row m-0">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4 class="card-title">Book List</h4>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        @foreach($data['data'] as $book)
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#bookModal" data-cover="{!! $book->cover !!}" data-id="{{ $book->id }}" data-title="{{ $book->title }}" data-summary="{{ $book->summary ?? '-' }}" data-author="{{ $book->author->name ?? '-' }}">
                            <div class="col-sm card m-2" style="width: 18rem;">
                                <img class="card-img-top thumbnail center" src="{{ $book->cover }}" onerror=this.src="assets/empty.jpg">
                                <div class="card-body">
                                    <p class="card-text">{{ $book->title }}</p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="bookModal" tabindex="-1" role="dialog" aria-labelledby="bookModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <p id="id" hidden></p>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-sm">
                        <img class="card-img-top" id='cover' src="" onerror=this.src="assets/empty.jpg">
                        <p class="card-text author" id='author'></p>
                        <p class="card-text summary" id='summary'></p>
                        <div class="row col-sm">
                            <button class="btn btn-success ml-1 mr-1" onclick="startAction('update')"><i class="fas fa-edit"></i></button>
                            <form id="deleteForm" method="POST">
                                @method('DELETE')
                                @csrf
                                <button class="btn btn-danger ml-1 mr-1" onclick="startAction('delete')"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="col-3 btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div><!-- /.container-fluid -->
<!-- /.content -->
@endsection

@section('js')
<script>
    $('#bookModal').on('show.bs.modal', function(event) {
        // Button that triggered the modal
        var button = $(event.relatedTarget)
        // Extract info from data-* attributes
        var id = button.data('id')
        var cover = button.data('cover')
        var title = button.data('title')
        var summary = button.data('summary')
        var author = button.data('author')
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this)
        modal.find('#id').text(id)
        modal.find('.modal-title').text(title)
        modal.find('#cover').attr('src', cover)
        modal.find('#author').text(author)
        modal.find('#summary').text(summary)
    })

    function startAction(type) {
        id = $('#bookModal').find("#id")[0].innerHTML;
        switch (type) {
            case "update":
                url = "/books/" + id + "/edit"
                window.location.href = url;
                break;
            case "delete":
                url = "/books/" + id
                $('#deleteForm').attr('action', url);
                console.log(url);
                break;
            default:
                return;
        }
    }
</script>
@endsection