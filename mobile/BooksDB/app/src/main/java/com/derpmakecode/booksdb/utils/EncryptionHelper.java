package com.derpmakecode.booksdb.utils;

import android.util.Base64;

import java.nio.charset.StandardCharsets;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.security.SecureRandom;

import javax.crypto.Cipher;
import javax.crypto.spec.GCMParameterSpec;
import javax.crypto.spec.SecretKeySpec;

/**
 * Created By ASUS on 21/05/2021
 */
public class EncryptionHelper {
    String key;

    public EncryptionHelper(String key) {
        this.key = md5(key);
    }

    public String decrypt(String encrypted) {
        String decryptedText = null;
        byte[] encodedData = Base64.decode(encrypted, Base64.DEFAULT);

        try {
            Cipher c;

            SecureRandom secureRandom = new SecureRandom();
            byte[] iv = new byte[12]; //NEVER REUSE THIS IV WITH SAME KEY
            secureRandom.nextBytes(iv);

            c = Cipher.getInstance("AES/GCM/NoPadding");
            GCMParameterSpec gcmParameterSpec = new GCMParameterSpec(16, iv);
            SecretKeySpec sKey = new SecretKeySpec(key.getBytes(), 0, 32, "AES/GCM/NoPadding");

            c.init(Cipher.DECRYPT_MODE, sKey, gcmParameterSpec);

            byte[] decodedData = c.doFinal(encodedData);
            decryptedText = new String(decodedData, StandardCharsets.UTF_8);
        } catch (Exception e) {
            e.printStackTrace();
        }
        return decryptedText;
    }

    private String md5(final String s) {
        final String MD5 = "MD5";
        try {
            // Create MD5 Hash
            MessageDigest digest = java.security.MessageDigest
                    .getInstance(MD5);
            digest.update(s.getBytes());
            byte messageDigest[] = digest.digest();

            // Create Hex String
            StringBuilder hexString = new StringBuilder();
            for (byte aMessageDigest : messageDigest) {
                String h = Integer.toHexString(0xFF & aMessageDigest);
                while (h.length() < 2)
                    h = "0" + h;
                hexString.append(h);
            }
            return hexString.toString();

        } catch (NoSuchAlgorithmException e) {
            e.printStackTrace();
        }
        return "";
    }
}
