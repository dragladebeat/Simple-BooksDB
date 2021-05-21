<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Author;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthorController extends Controller
{
    public function index()
    {
        return Author::with('books')->get();
    }

    public function show($id)
    {
        return Author::with('books')->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|min:3'
            ]
        );

        if ($validator->fails()) {
            throw new BadRequestHttpException($validator->errors()->first());
        }
        DB::beginTransaction();
        $author = new Author();
        $author->name = $request->input('name');
        $author->save();
        DB::commit();

        return response()->json($author);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|min:3'
            ]
        );

        if ($validator->fails()) {
            throw new BadRequestHttpException($validator->errors()->first());
        }

        DB::beginTransaction();
        $author = Author::findOrFail($id);
        $author->name = $request->input('name');
        $author->save();
        DB::commit();

        return response()->json($author);
    }

    public function delete($id)
    {
        $author = Author::with('books')->findOrFail($id);

        DB::transaction(function () use ($author) {
            foreach ($author->books as $book) {
                $book->author_id = null;
                $book->save();
            }
            $author->delete();
        });

        return response()->json($author);
    }
}