<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    // Enable factory
    use HasFactory;

    public function author()
    {
        return $this->belongsTo(Author::class, 'author_id', 'id');
    }

    public function favourites()
    {
        return $this->belongsToMany(User::class, 'favourites', 'book_id', 'user_id');
    }

    public function userFavourite($user)
    {
        return $this->leftJoin('favourites', 'books.id', '=', 'book_id')->where('user_id', $user->id)->where('books.id', $this->id)->select('favourites.*')->first();
    }
}
