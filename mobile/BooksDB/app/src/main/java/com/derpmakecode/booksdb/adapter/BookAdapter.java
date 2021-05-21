package com.derpmakecode.booksdb.adapter;

import android.view.LayoutInflater;
import android.view.ViewGroup;

import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;
import com.derpmakecode.booksdb.App;
import com.derpmakecode.booksdb.databinding.FragmentBookBinding;
import com.derpmakecode.booksdb.model.Book;
import com.varunest.sparkbutton.SparkButton;

import java.util.List;

public class BookAdapter extends RecyclerView.Adapter<BookAdapter.ViewHolder> {

    public List<Book> mValues;
    private final BookListener listener;

    public BookAdapter(List<Book> items, BookListener listener) {
        mValues = items;
        this.listener = listener;
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        FragmentBookBinding binding = FragmentBookBinding.inflate(LayoutInflater.from(parent.getContext()), parent, false);
        return new ViewHolder(binding);
    }

    @Override
    public void onBindViewHolder(final ViewHolder holder, int position) {
        Book book = mValues.get(position);
        holder.book = book;

        Glide.with(holder.binding.getRoot())
                .load(book.cover)
                .into(holder.binding.cover);
        holder.binding.title.setText(book.title);
        if (book.author != null) {
            holder.binding.author.setText(book.author.name);
        }
        if (App.getInstance().getSessionManager().isLoggedIn()) {
            holder.binding.sparkButton.setChecked(book.isFavourite);
        }

        holder.binding.getRoot().setOnClickListener(v -> listener.onItemClicked(book));
        holder.binding.sparkButton.setOnClickListener(v -> listener.onFavouriteClicked(holder.binding.sparkButton, book, position));
    }

    @Override
    public int getItemCount() {
        return mValues.size();
    }

    public class ViewHolder extends RecyclerView.ViewHolder {
        public final FragmentBookBinding binding;
        public Book book;

        public ViewHolder(FragmentBookBinding binding) {
            super(binding.getRoot());
            this.binding = binding;
        }
    }

    public interface BookListener {
        void onItemClicked(Book book);

        void onFavouriteClicked(SparkButton button, Book book, int position);
    }
}