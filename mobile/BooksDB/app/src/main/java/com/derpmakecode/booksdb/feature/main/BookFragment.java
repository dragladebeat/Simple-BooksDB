package com.derpmakecode.booksdb.feature.main;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Toast;

import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.derpmakecode.booksdb.App;
import com.derpmakecode.booksdb.adapter.BookAdapter;
import com.derpmakecode.booksdb.data.repositories.BookRepository;
import com.derpmakecode.booksdb.databinding.FragmentBookListBinding;
import com.derpmakecode.booksdb.feature.BookDetailActivity;
import com.derpmakecode.booksdb.feature.LoginActivity;
import com.derpmakecode.booksdb.model.Book;
import com.derpmakecode.booksdb.model.Error;
import com.derpmakecode.booksdb.network.NetworkClient;
import com.google.gson.Gson;
import com.google.gson.JsonObject;
import com.varunest.sparkbutton.SparkButton;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;


/**
 * A fragment representing a list of Items.
 */
public class BookFragment extends Fragment implements BookAdapter.BookListener {
    FragmentBookListBinding binding;

    BookRepository repository;

    public static BookFragment newInstance() {
        BookFragment fragment = new BookFragment();
        Bundle args = new Bundle();
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (getArguments() != null) {
        }
        repository = new BookRepository(NetworkClient.getService(App.getInstance().getSessionManager().getAuth()));
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        binding = FragmentBookListBinding.inflate(inflater, container, false);

        // Set the adapter
        RecyclerView recyclerView = binding.list;
        recyclerView.setLayoutManager(new GridLayoutManager(getContext(), 2));

        return binding.getRoot();
    }

    @Override
    public void onStart() {
        super.onStart();

        showLoading();
        repository.list().enqueue(new Callback<List<Book>>() {
            @Override
            public void onResponse(Call<List<Book>> call, Response<List<Book>> response) {
                hideLoading();
                if (response.isSuccessful()) {
                    hideError();
                    BookAdapter adapter = new BookAdapter(response.body(), BookFragment.this);
                    binding.list.setAdapter(adapter);
                } else {
                    switch (response.code()) {
                        case 401:
                            App.getInstance().forceLogout();
                        default:
                            Gson gson = new Gson();
                            try {
                                JsonObject jsonObject = gson.fromJson(response.errorBody().string(), JsonObject.class);
                                Error error = gson.fromJson(jsonObject.getAsJsonObject("error"), Error.class);
                                showError(error.code + ": " + (TextUtils.isEmpty(error.message) ? response.message() : error.message));
                            } catch (Exception e) {
                                showError(response.message());
                            }
                    }
                }
            }

            @Override
            public void onFailure(Call<List<Book>> call, Throwable t) {
                hideLoading();
                showError(t.getMessage());
            }
        });
    }

    private void showLoading() {
        binding.loading.setVisibility(View.VISIBLE);
    }

    private void hideLoading() {
        binding.loading.setVisibility(View.INVISIBLE);
    }

    private void showError(String message) {
        binding.list.setVisibility(View.INVISIBLE);
        binding.error.getRoot().setVisibility(View.VISIBLE);
        binding.error.textError.setText(message);
    }

    private void hideError() {
        binding.list.setVisibility(View.VISIBLE);
        binding.error.getRoot().setVisibility(View.GONE);
    }

    @Override
    public void onItemClicked(Book book) {
        Intent intent = new Intent(getContext(), BookDetailActivity.class);
        intent.putExtra("id", book.id);
        startActivity(intent);
    }

    @Override
    public void onFavouriteClicked(SparkButton button, Book book, int position) {
        if (App.getInstance().getSessionManager().isLoggedIn()) {
            repository.favourite(book.id).enqueue(new Callback<Book>() {
                @Override
                public void onResponse(Call<Book> call, Response<Book> response) {
                    hideLoading();
                    if (response.isSuccessful()) {
                        button.setChecked(response.body().isFavourite);
                    } else {
                        switch (response.code()) {
                            case 401:
                                App.getInstance().forceLogout();
                            default:
                                Gson gson = new Gson();
                                try {
                                    JsonObject jsonObject = gson.fromJson(response.errorBody().string(), JsonObject.class);
                                    Error error = gson.fromJson(jsonObject.getAsJsonObject("error"), Error.class);
                                    Toast.makeText(getContext(), error.code + ": " + (TextUtils.isEmpty(error.message) ? response.message() : error.message), Toast.LENGTH_SHORT).show();
                                } catch (Exception e) {
                                    showError(response.message());
                                }
                        }
                    }
                }

                @Override
                public void onFailure(Call<Book> call, Throwable t) {
                    Toast.makeText(getContext(), t.getMessage(), Toast.LENGTH_SHORT).show();
                }
            });
        } else {
            Toast.makeText(getContext(), "You must be logged in to perform this action", Toast.LENGTH_SHORT).show();
            Intent intent = new Intent(getContext(), LoginActivity.class);
            startActivity(intent);
        }
    }
}