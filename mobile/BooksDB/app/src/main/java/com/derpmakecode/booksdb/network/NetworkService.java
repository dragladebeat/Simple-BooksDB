package com.derpmakecode.booksdb.network;

import com.derpmakecode.booksdb.model.Book;
import com.derpmakecode.booksdb.model.User;
import com.derpmakecode.booksdb.network.request.LoginRequest;
import com.derpmakecode.booksdb.network.request.RegisterRequest;
import com.derpmakecode.booksdb.network.response.LoginResponse;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.DELETE;
import retrofit2.http.Field;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.Path;


/**
 * Created By ASUS on 21/05/2021
 */
public interface NetworkService {
    @GET("books")
    Call<List<Book>> listBook();

    @GET("books/{id}")
    Call<Book> getBook(@Path("id") int id);

    @GET("favourites")
    Call<List<Book>> listFavourite();

    @POST("favourites/{id}")
    Call<Book> favourite(@Path("id") int id);

    @POST("auth/login")
    Call<LoginResponse> login(@Body LoginRequest request);

    @POST("auth/logout")
    Call<Void> logout();

    @POST("auth/register")
    Call<LoginResponse> register(@Body RegisterRequest request);
}
