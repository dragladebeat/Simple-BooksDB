package com.derpmakecode.booksdb.feature.main;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Toast;

import androidx.fragment.app.Fragment;

import com.derpmakecode.booksdb.App;
import com.derpmakecode.booksdb.R;
import com.derpmakecode.booksdb.data.SessionManager;
import com.derpmakecode.booksdb.data.repositories.UserRepository;
import com.derpmakecode.booksdb.databinding.FragmentFavouriteBinding;
import com.derpmakecode.booksdb.databinding.FragmentProfileBinding;
import com.derpmakecode.booksdb.feature.RegisterActivity;
import com.derpmakecode.booksdb.model.Error;
import com.derpmakecode.booksdb.model.User;
import com.derpmakecode.booksdb.network.NetworkClient;
import com.google.gson.Gson;

import com.google.gson.JsonObject;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ProfileFragment extends Fragment {

    private FragmentProfileBinding binding;
    private UserRepository repository;

    public static ProfileFragment newInstance() {
        ProfileFragment fragment = new ProfileFragment();
        Bundle args = new Bundle();
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {
        }

        repository = new UserRepository(NetworkClient.getService(App.getInstance().getSessionManager().getAuth()));
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        binding = FragmentProfileBinding.inflate(inflater, container, false);

        User user = App.getInstance().getSessionManager().getUser();
        if(user != null) {
            binding.textEmail.setText(user.email);
            binding.textName.setText(user.name);
        }


        binding.logout.setOnClickListener(v -> logout());
        return binding.getRoot();
    }

    private void logout() {
        repository.logout().enqueue(new Callback<Void>() {
            @Override
            public void onResponse(Call<Void> call, Response<Void> response) {
                if (response.isSuccessful()) {
                    App.getInstance().forceLogout();
                } else {
                    Gson gson = new Gson();
                    try {
                        JsonObject jsonObject = gson.fromJson(response.errorBody().string(), JsonObject.class);
                        Error error = gson.fromJson(jsonObject.getAsJsonObject("error"), Error.class);
                        Toast.makeText(getContext(), error.code + ": " + (TextUtils.isEmpty(error.message) ? response.message() : error.message), Toast.LENGTH_SHORT).show();
                    } catch (Exception e) {
                        Toast.makeText(getContext(), response.message(), Toast.LENGTH_SHORT).show();
                    }
                }
            }

            @Override
            public void onFailure(Call<Void> call, Throwable t) {
                Toast.makeText(getContext(), t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }
}