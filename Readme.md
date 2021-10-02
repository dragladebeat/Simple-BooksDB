Backend
1. Lumen Framework ( composer create-project --prefer-dist laravel/lumen:^7.0 backend )
2. Lumen-Generator : https://github.com/flipboxstudio/lumen-generator
3. Intervention-Image : http://image.intervention.io/getting_started/installation
4. PHP - JWT : https://github.com/firebase/php-jwt

Frontend :
1. Laravel Framework ( composer create-project --prefer-dist laravel/laravel:^7.0 web )
2. Admin LTE : https://github.com/ColorlibHQ/AdminLTE/releases

Android :
- Retrofit : implementation 'com.squareup.retrofit2:retrofit:(insert latest version)'
- Retrofit GSON Adapter implementation 'com.squareup.retrofit2:converter-gson:2.9.0'
- Favourite Button : implementation 'com.github.ivbaranov:materialfavoritebutton:0.1.5'

Persiapan Backend
1. Instalasi Lumen : composer create-project --prefer-dist laravel/lumen:^7.0 backend
2. Lumen-Generator : https://github.com/flipboxstudio/lumen-generator
3. Buat model : {Book, Author, Favourite, BlacklistedToken} php artisan make:model Book -fmc
4. Buat Routes CRUD
5. Atur exception handler

## Cara Pemakaian
- Backend
    - Copy .env.example ke .env
    - Ubah konfigurasi database pada .env
    - isi JWT_SECRET pada .env
    - Ubah APP_URL sesuai IP / host yang dibutuhkan (Contoh : 192.168.0.101)
    - Jalankan migration (**php artisan migrate**)
    - Jalankan backend (**php artisan serve --host=192.168.0.101 --port=8000**)

- CMS
    - Copy .env.example ke .env
    - Ubah API_URL sesuai backend (Contoh : 192.168.0.101/api/)
    - Jalankan CMS (**php artisan serve --port=8080**)