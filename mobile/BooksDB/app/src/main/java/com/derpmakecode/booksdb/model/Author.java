package com.derpmakecode.booksdb.model;

import com.google.gson.annotations.SerializedName;

/**
 * Created By ASUS on 21/05/2021
 */
public class Author {
    @SerializedName("id")
    public int id;
    @SerializedName("name")
    public String name;
    @SerializedName("updated_at")
    public String updatedAt;
    @SerializedName("created_at")
    public String createdAt;
}
