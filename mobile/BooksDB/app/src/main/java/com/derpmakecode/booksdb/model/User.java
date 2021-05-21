package com.derpmakecode.booksdb.model;

import com.google.gson.annotations.SerializedName;

/**
 * Created By ASUS on 21/05/2021
 */
public class User {
    @SerializedName("id")
    public int id;
    @SerializedName("email")
    public String email;
    @SerializedName("name")
    public String name;

}
