package com.derpmakecode.booksdb.network.request;

import com.google.gson.annotations.SerializedName;

/**
 * Created By ASUS on 21/05/2021
 */
public class LoginRequest {
    @SerializedName("email")
    public String email;
    @SerializedName("password")
    public String password;
}
