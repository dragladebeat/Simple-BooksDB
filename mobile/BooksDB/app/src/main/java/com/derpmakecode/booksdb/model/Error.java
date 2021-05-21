package com.derpmakecode.booksdb.model;

import com.google.gson.annotations.SerializedName;

/**
 * Created By ASUS on 21/05/2021
 */
public class Error {
    @SerializedName("code")
    public int code;
    @SerializedName("message")
    public String message;
}
