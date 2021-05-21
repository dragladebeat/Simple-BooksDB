package com.derpmakecode.booksdb.data.repositories;

import com.derpmakecode.booksdb.model.Book;
import com.derpmakecode.booksdb.network.NetworkService;

import java.util.List;

import retrofit2.Call;

/**
 * Created By ASUS on 21/05/2021
 */
public class BookRepository {

    private final NetworkService networkService;

    public BookRepository(NetworkService service) {
        this.networkService = service;
    }

    public Call<List<Book>> list() {
        return networkService.listBook();
    }

    public Call<Book> get(int id) {
        return networkService.getBook(id);
    }

    public Call<List<Book>> listFavourite() {
        return networkService.listFavourite();
    }

    public Call<Book> favourite(int id) {
        return networkService.favourite(id);
    }
}
