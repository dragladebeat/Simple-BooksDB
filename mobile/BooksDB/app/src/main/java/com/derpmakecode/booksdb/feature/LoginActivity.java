package com.derpmakecode.booksdb.feature;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.derpmakecode.booksdb.App;
import com.derpmakecode.booksdb.data.SessionManager;
import com.derpmakecode.booksdb.data.repositories.UserRepository;
import com.derpmakecode.booksdb.databinding.ActivityLoginBinding;
import com.derpmakecode.booksdb.feature.main.MainActivity;
import com.derpmakecode.booksdb.model.Error;
import com.derpmakecode.booksdb.network.NetworkClient;
import com.derpmakecode.booksdb.network.response.LoginResponse;
import com.google.gson.Gson;

import com.google.gson.JsonObject;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class LoginActivity extends AppCompatActivity {

    private ActivityLoginBinding binding;
    private UserRepository repository;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityLoginBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        repository = new UserRepository(NetworkClient.getService(App.getInstance().getSessionManager().getAuth()));
        init();
    }

    private void init() {
        binding.back.setOnClickListener(v -> onBackPressed());

        binding.buttonLogin.setOnClickListener(v -> {
            login(binding.etEmail.getText().toString(), binding.etPassword.getText().toString());
        });

        binding.register.setOnClickListener(v -> {
            Intent intent = new Intent(LoginActivity.this, RegisterActivity.class);
            startActivity(intent);
        });
    }

    private void login(String email, String password) {
        showLoading();
        repository.login(email, password).enqueue(new Callback<LoginResponse>() {
            @Override
            public void onResponse(Call<LoginResponse> call, Response<LoginResponse> response) {
                hideLoading();
                if (response.isSuccessful()) {
                    SessionManager sessionManager = App.getInstance().getSessionManager();
                    sessionManager.setAuth(response.body().auth.accessToken);
                    sessionManager.setUser(response.body().user);

                    Intent intent = new Intent(LoginActivity.this, MainActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT | Intent.FLAG_ACTIVITY_CLEAR_TOP);
                    startActivity(intent);

                } else {
                    Gson gson = new Gson();
                    try {
                        JsonObject jsonObject = gson.fromJson(response.errorBody().string(), JsonObject.class);
                        Error error = gson.fromJson(jsonObject.getAsJsonObject("error"), Error.class);
                        Toast.makeText(LoginActivity.this, error.code + ": " + (TextUtils.isEmpty(error.message) ? response.message() : error.message), Toast.LENGTH_SHORT).show();
                    } catch (Exception e) {
                        Toast.makeText(LoginActivity.this, response.message(), Toast.LENGTH_SHORT).show();
                    }
                }
            }

            @Override
            public void onFailure(Call<LoginResponse> call, Throwable t) {
                hideLoading();
                Toast.makeText(LoginActivity.this, t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void showLoading() {
        binding.loading.setVisibility(View.VISIBLE);
        binding.buttonLogin.setVisibility(View.INVISIBLE);
    }

    private void hideLoading() {
        binding.loading.setVisibility(View.INVISIBLE);
        binding.buttonLogin.setVisibility(View.VISIBLE);
    }
}