package com.derpmakecode.booksdb.feature.main;

import android.content.Intent;
import android.os.Bundle;
import android.view.MenuItem;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.fragment.app.Fragment;

import com.derpmakecode.booksdb.App;
import com.derpmakecode.booksdb.R;
import com.derpmakecode.booksdb.databinding.ActivityMainBinding;
import com.derpmakecode.booksdb.feature.LoginActivity;
import com.google.android.material.bottomnavigation.BottomNavigationView;

public class MainActivity extends AppCompatActivity implements BottomNavigationView.OnNavigationItemSelectedListener {

    private ActivityMainBinding binding;
    private boolean isLoggedIn;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityMainBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        isLoggedIn = App.getInstance().getSessionManager().isLoggedIn();
        init();
    }

    private void init() {
        loadFragment(new BookFragment());
        BottomNavigationView bottomNavigationView = binding.navigation;
        bottomNavigationView.setOnNavigationItemSelectedListener(this);
    }

    @Override
    public boolean onNavigationItemSelected(@NonNull MenuItem item) {
        Fragment fragment = null;
        switch (item.getItemId()) {
            case R.id.action_home:
                fragment = BookFragment.newInstance();
                break;
            case R.id.action_favourites:
                if (!isLoggedIn) {
                    Toast.makeText(MainActivity.this, "You must be logged in to perform this action", Toast.LENGTH_SHORT).show();
                    Intent intent = new Intent(MainActivity.this, LoginActivity.class);
                    startActivity(intent);
                } else {
                    fragment = FavouriteFragment.newInstance();
                }
                break;
            case R.id.action_profile:
                if (!isLoggedIn) {
                    Toast.makeText(MainActivity.this, "You must be logged in to perform this action", Toast.LENGTH_SHORT).show();
                    Intent intent = new Intent(MainActivity.this, LoginActivity.class);
                    startActivity(intent);
                } else {
                    fragment = ProfileFragment.newInstance();
                }
                break;
        }

        return loadFragment(fragment);
    }

    // method untuk load fragment yang sesuai
    private boolean loadFragment(Fragment fragment) {
        if (fragment != null) {
            getSupportFragmentManager().beginTransaction()
                    .replace(binding.container.getId(), fragment)
                    .commit();
            return true;
        }
        return false;
    }
}