package com.derpmakecode.booksdb.model;

import com.google.gson.annotations.SerializedName;

/**
 * Created By ASUS on 21/05/2021
 */
public class Auth {
    @SerializedName("access_token")
    public String accessToken;
    @SerializedName("tokenType")
    public String tokenType;
    @SerializedName("expiresIn")
    public int expiresIn;
}
