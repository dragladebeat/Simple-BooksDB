package com.derpmakecode.booksdb.data.repositories;

import com.derpmakecode.booksdb.model.Book;
import com.derpmakecode.booksdb.model.User;
import com.derpmakecode.booksdb.network.NetworkService;
import com.derpmakecode.booksdb.network.request.LoginRequest;
import com.derpmakecode.booksdb.network.request.RegisterRequest;
import com.derpmakecode.booksdb.network.response.LoginResponse;

import java.util.List;

import retrofit2.Call;

/**
 * Created By ASUS on 21/05/2021
 */
public class UserRepository {
    private final NetworkService networkService;

    public UserRepository(NetworkService service) {
        this.networkService = service;
    }

    public Call<LoginResponse> login(String email, String password) {
        LoginRequest request = new LoginRequest();
        request.email = email;
        request.password = password;
        return networkService.login(request);
    }

    public Call<Void> logout() {
        return networkService.logout();
    }

    public Call<LoginResponse> register(String name, String email, String password, String password_confirmation) {
        RegisterRequest request = new RegisterRequest();
        request.name = name;
        request.email = email;
        request.password = password;
        request.passwordConfirmation = password_confirmation;
        return networkService.register(request);
    }
}
