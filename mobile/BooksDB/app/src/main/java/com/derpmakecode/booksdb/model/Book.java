package com.derpmakecode.booksdb.model;

import com.google.gson.annotations.SerializedName;

/**
 * Created By ASUS on 21/05/2021
 */
public class Book {
    @SerializedName("id")
    public int id;
    @SerializedName("title")
    public String title;
    @SerializedName("summary")
    public String summary;
    @SerializedName("cover")
    public String cover;
    @SerializedName("is_favourite")
    public boolean isFavourite;
    @SerializedName("updated_at")
    public String updatedAt;
    @SerializedName("created_at")
    public String createdAt;
    @SerializedName("author")
    public Author author;
}
