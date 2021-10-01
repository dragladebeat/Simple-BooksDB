<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Book;
use App\Models\Favourite;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FavouriteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->_user;
        $data = Book::with('author')->join('favourites', 'books.id', '=', 'book_id')->where('user_id', $user->id)->select('books.*')->get();

        foreach ($data as $book) {
            $book['is_favourite'] = !empty($book->userFavourite($user));
        }
        return $data;
    }

    public function favourite(Request $request, $id)
    {
        $user = $request->_user;
        $user_id = $user;
        $book = Book::findOrFail($id);
        try {
            if (empty($book->userFavourite($user_id))) {
                $book->favourites()->attach($user_id);
            } else {
                $book->favourites()->detach($user_id);
            }
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode != 1062) {
                return Helper::respondWithError($e->getCode(), $e->getMessage());
            }
        }

        $book['is_favourite'] = !empty($book->userFavourite($user_id));
        return response()->json($book);
    }
}
