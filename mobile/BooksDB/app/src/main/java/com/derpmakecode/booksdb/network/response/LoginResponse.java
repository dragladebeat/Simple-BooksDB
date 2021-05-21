package com.derpmakecode.booksdb.network.response;

import com.derpmakecode.booksdb.model.Auth;
import com.derpmakecode.booksdb.model.User;
import com.google.gson.annotations.SerializedName;

/**
 * Created By ASUS on 21/05/2021
 */
public class LoginResponse {
    @SerializedName("user")
    public User user;

    @SerializedName("auth")
    public Auth auth;

}
