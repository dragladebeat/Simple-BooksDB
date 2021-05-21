package com.derpmakecode.booksdb.feature;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;

import com.bumptech.glide.Glide;
import com.derpmakecode.booksdb.App;
import com.derpmakecode.booksdb.data.repositories.BookRepository;
import com.derpmakecode.booksdb.databinding.ActivityBookDetailBinding;
import com.derpmakecode.booksdb.model.Book;
import com.derpmakecode.booksdb.model.Error;
import com.derpmakecode.booksdb.network.NetworkClient;
import com.google.gson.Gson;

import com.google.gson.JsonObject;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class BookDetailActivity extends AppCompatActivity {

    private ActivityBookDetailBinding binding;

    BookRepository repository;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityBookDetailBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        repository = new BookRepository(NetworkClient.getService(App.getInstance().getSessionManager().getAuth()));

        init(getIntent().getIntExtra("id", 0));
    }

    private void init(int id) {
        binding.back.setOnClickListener(v -> onBackPressed());
        if (id > 0) {
            getBook(id);
        } else {
            Toast.makeText(this, "Book not found", Toast.LENGTH_SHORT).show();
        }
    }

    private void getBook(int id) {
        showLoading();
        repository.get(id).enqueue(new Callback<Book>() {
            @Override
            public void onResponse(Call<Book> call, Response<Book> response) {
                hideLoading();
                if (response.isSuccessful()) {
                    hideError();
                    @NonNull Book book = response.body() != null ? response.body() : new Book();

                    Glide.with(binding.getRoot())
                            .load(book.cover)
                            .into(binding.cover);
                    binding.title.setText(book.title);
                    if (book.author != null) {
                        binding.author.setText(book.author.name);
                    }
                    if (App.getInstance().getSessionManager().isLoggedIn()) {
                        binding.sparkButton.setChecked(book.isFavourite);
                    }

                    binding.sparkButton.setOnClickListener(v -> favourite(book));
                } else {
                    switch (response.code()) {
                        case 401:
                            App.getInstance().forceLogout();
                        default:
                            Gson gson = new Gson();
                            try {
                                JsonObject jsonObject = gson.fromJson(response.errorBody().string(), JsonObject.class);
                                Error error = gson.fromJson(jsonObject.getAsJsonObject("error"), Error.class);
                                Toast.makeText(BookDetailActivity.this, error.code + ": " + (TextUtils.isEmpty(error.message) ? response.message() : error.message), Toast.LENGTH_SHORT).show();
                            } catch (Exception e) {
                                showError(response.message());
                            }
                    }
                }
            }

            @Override
            public void onFailure(Call<Book> call, Throwable t) {
                Toast.makeText(BookDetailActivity.this, t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void favourite(Book book) {
        if (App.getInstance().getSessionManager().isLoggedIn()) {
            repository.favourite(book.id).enqueue(new Callback<Book>() {
                @Override
                public void onResponse(Call<Book> call, Response<Book> response) {
                    hideLoading();
                    if (response.isSuccessful()) {
                        binding.sparkButton.setChecked(response.body().isFavourite);
                    } else {
                        switch (response.code()) {
                            case 401:
                                App.getInstance().forceLogout();
                            default:
                                Gson gson = new Gson();
                                try {
                                    JsonObject jsonObject = gson.fromJson(response.errorBody().string(), JsonObject.class);
                                    Error error = gson.fromJson(jsonObject.getAsJsonObject("error"), Error.class);
                                    Toast.makeText(BookDetailActivity.this, error.code + ": " + (TextUtils.isEmpty(error.message) ? response.message() : error.message), Toast.LENGTH_SHORT).show();
                                } catch (Exception e) {
                                    showError(response.message());
                                }
                        }
                    }
                }

                @Override
                public void onFailure(Call<Book> call, Throwable t) {
                    Toast.makeText(BookDetailActivity.this, t.getMessage(), Toast.LENGTH_SHORT).show();
                }
            });
        } else {
            Toast.makeText(BookDetailActivity.this, "You must be logged in to perform this action", Toast.LENGTH_SHORT).show();
            Intent intent = new Intent(BookDetailActivity.this, LoginActivity.class);
            startActivity(intent);
        }
    }

    private void showLoading() {
        binding.loading.setVisibility(View.VISIBLE);
    }

    private void hideLoading() {
        binding.loading.setVisibility(View.INVISIBLE);
    }

    private void showError(String message) {
        binding.content.setVisibility(View.INVISIBLE);
        binding.error.getRoot().setVisibility(View.VISIBLE);
        binding.error.textError.setText(message);
    }

    private void hideError() {
        binding.content.setVisibility(View.VISIBLE);
        binding.error.getRoot().setVisibility(View.GONE);
    }

}