<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\Helper;
use App\Models\Author;
use App\Models\Book;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class BookController extends Controller
{
    private $authHelper;
    function __construct(AuthHelper $authHelper)
    {
        $this->authHelper = $authHelper;
    }
    public function index(Request $request)
    {
        if (!empty($request->bearerToken())) {
            $user = $this->authHelper->getUser($request->bearerToken());
        }
        $query = Book::with('author');
        $data = $query->get();
        foreach ($data as $book) {
            if (!empty($user)) {
                $book['is_favourite'] = !empty($book->userFavourite($user));
            }
        }
        return $data;
    }

    public function show(Request $request, $id)
    {
        if (!empty($request->bearerToken())) {
            $user = $this->authHelper->getUser($request->bearerToken());
        }
        $book = Book::with('author')->findOrFail($id);
        if (!empty($user)) {
            $book['is_favourite'] = !empty($book->userFavourite($user));
        }
        return $book;
    }

    public function store(Request $request)
    {
        $user = $request->_user;
        if ($user->type != 'admin') {
            throw new UnauthorizedHttpException('', 'Unauthorized');
        }
        Log::info(json_encode($request->all()));
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required|string|min:2',
                'summary' => 'string|nullable|min:10'
            ]
        );

        if ($validator->fails()) {
            throw new BadRequestHttpException($validator->errors()->first());
        }
        DB::beginTransaction();
        $book = new Book();
        $book->title = $request->input('title');
        $book->summary = $request->input('summary');
        if (!empty($request->input('author_id'))) {
            $author = Author::findOrFail($request->input('author_id'));
            $book->author_id = $author->id;
        }
        $book->save();

        if (!empty($request->input('cover'))) {

            $file_path = Helper::storeImage('books/cover', $book->id . '.jpg', $request->input('cover'));

            if (!$file_path) {
                throw new Exception('Failed to upload image');
            }

            $book->cover = $file_path;
        }
        $book->save();
        DB::commit();

        $book->load('author');
        return response()->json($book);
    }

    public function update($id, Request $request)
    {
        $user = $request->_user;
        if ($user->type != 'admin') {
            throw new UnauthorizedHttpException('Unauthorized');
        }
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required|string|min:2',
                'summary' => 'string|nullable|min:10'
            ]
        );

        if ($validator->fails()) {
            throw new BadRequestHttpException($validator->errors()->first());
        }

        DB::beginTransaction();
        $book = Book::findOrFail($id);
        $book->title = $request->input('title');
        $book->summary = $request->input('summary');
        if (!empty($request->input('author_id'))) {
            $author = Author::findOrFail($request->input('author_id'));
            $book->author_id = $author->id;
        }
        if (!empty($request->input('cover'))) {

            $file_path = Helper::storeImage('books/cover', $book->id . '.jpg', $request->input('cover'));

            if (!$file_path) {
                throw new Exception('Failed to upload image');
            }

            $book->cover = $file_path;
        }
        $book->save();
        DB::commit();

        $book->load('author');
        return response()->json($book);
    }

    public function delete(Request $request, $id)
    {
        $user = $request->_user;
        if ($user->type != 'admin') {
            throw new UnauthorizedHttpException('Unauthorized');
        }
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json($book);
    }
}
