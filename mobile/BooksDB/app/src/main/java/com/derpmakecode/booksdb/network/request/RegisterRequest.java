package com.derpmakecode.booksdb.network.request;

import com.google.gson.annotations.SerializedName;

/**
 * Created By ASUS on 21/05/2021
 */
public class RegisterRequest {
    @SerializedName("email")
    public String email;
    @SerializedName("name")
    public String name;
    @SerializedName("password")
    public String password;
    @SerializedName("password_confirmation")
    public String passwordConfirmation;
}
