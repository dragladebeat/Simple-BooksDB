package com.derpmakecode.booksdb;

import android.app.Application;
import android.content.Intent;
import android.util.Log;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatDelegate;

import com.derpmakecode.booksdb.data.SessionManager;
import com.derpmakecode.booksdb.feature.main.MainActivity;

/**
 * Created By ASUS on 21/05/2021
 */
public class App extends Application {
    private static App app;
    private static SessionManager sessionManager;
    @Override
    public void onCreate() {
        super.onCreate();
        app = App.this;
        AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_YES);
    }

    public SessionManager getSessionManager() {
        if(sessionManager == null) {
            sessionManager = new SessionManager(app.getApplicationContext());
        }

        return sessionManager;
    }

    public static App getInstance() {
        return app;
    }

    public void forceLogout() {
        Toast.makeText(app, "Session has expired", Toast.LENGTH_SHORT).show();
        sessionManager.logout();
        Intent intent = new Intent(app, MainActivity.class);
        intent.setFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT | Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
        app.startActivity(intent);
    }
}
