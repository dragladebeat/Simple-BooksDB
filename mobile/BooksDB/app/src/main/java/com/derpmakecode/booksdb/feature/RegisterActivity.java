package com.derpmakecode.booksdb.feature;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Log;
import android.view.View;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.derpmakecode.booksdb.App;
import com.derpmakecode.booksdb.data.SessionManager;
import com.derpmakecode.booksdb.data.repositories.UserRepository;
import com.derpmakecode.booksdb.databinding.ActivityRegisterBinding;
import com.derpmakecode.booksdb.feature.main.MainActivity;
import com.derpmakecode.booksdb.model.Error;
import com.derpmakecode.booksdb.network.NetworkClient;
import com.derpmakecode.booksdb.network.response.LoginResponse;
import com.google.gson.Gson;
import com.google.gson.JsonObject;

import org.json.JSONObject;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class RegisterActivity extends AppCompatActivity {

    private ActivityRegisterBinding binding;
    private UserRepository repository;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityRegisterBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());
        repository = new UserRepository(NetworkClient.getService(App.getInstance().getSessionManager().getAuth()));
        init();
    }

    private void init() {
        binding.back.setOnClickListener(v -> onBackPressed());

        binding.buttonRegister.setOnClickListener(v -> register(binding.etName.getText().toString(), binding.etEmail.getText().toString(), binding.etPassword.getText().toString(), binding.etPasswordConfirmation.getText().toString()));
    }

    private void register(String name, String email, String password, String passwordConfirmation) {
        showLoading();
        repository.register(name, email, password, passwordConfirmation).enqueue(new Callback<LoginResponse>() {
            @Override
            public void onResponse(Call<LoginResponse> call, Response<LoginResponse> response) {
                hideLoading();
                if (response.isSuccessful()) {
                    SessionManager sessionManager = App.getInstance().getSessionManager();
                    sessionManager.setAuth(response.body().auth.accessToken);
                    sessionManager.setUser(response.body().user);

                    Intent intent = new Intent(RegisterActivity.this, MainActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT | Intent.FLAG_ACTIVITY_CLEAR_TOP);
                    startActivity(intent);
                } else {
                    Gson gson = new Gson();
                    try {
                        JsonObject jsonObject = gson.fromJson(response.errorBody().string(), JsonObject.class);
                        Error error = gson.fromJson(jsonObject.getAsJsonObject("error"), Error.class);
                        Toast.makeText(RegisterActivity.this, error.code + ": " + (TextUtils.isEmpty(error.message) ? response.message() : error.message), Toast.LENGTH_SHORT).show();
                    } catch (Exception e) {
                        e.printStackTrace();
                        Toast.makeText(RegisterActivity.this, response.message(), Toast.LENGTH_SHORT).show();
                    }
                }
            }

            @Override
            public void onFailure(Call<LoginResponse> call, Throwable t) {
                hideLoading();
                Toast.makeText(RegisterActivity.this, t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void showLoading() {
        binding.loading.setVisibility(View.VISIBLE);
        binding.buttonRegister.setVisibility(View.INVISIBLE);
    }

    private void hideLoading() {
        binding.loading.setVisibility(View.INVISIBLE);
        binding.buttonRegister.setVisibility(View.VISIBLE);
    }
}