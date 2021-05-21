package com.derpmakecode.booksdb.data;

import android.content.Context;
import android.content.SharedPreferences;
import android.text.TextUtils;
import android.util.Log;

import com.derpmakecode.booksdb.BuildConfig;
import com.derpmakecode.booksdb.model.User;
import com.google.gson.Gson;

/**
 * Created By ASUS on 21/05/2021
 */
public class SessionManager {
    SharedPreferences sharedPreferences;

    public SessionManager(Context context) {
        this.sharedPreferences = context.getSharedPreferences(BuildConfig.APPLICATION_ID, Context.MODE_PRIVATE);
    }

    public void setAuth(String token) {
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putString("token", token);
        editor.apply();
    }

    public String getAuth() {
        return sharedPreferences.getString("token", null);
    }

    public void setUser(User user) {
        Gson gson = new Gson();
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putString("user", gson.toJson(user));
        editor.apply();
    }

    public User getUser() {
        Gson gson = new Gson();
        return gson.fromJson(sharedPreferences.getString("user", null), User.class);
    }


    public void logout() {
        sharedPreferences.edit().clear().apply();
    }

    public boolean isLoggedIn() {
        return !TextUtils.isEmpty(getAuth());
    }
}
