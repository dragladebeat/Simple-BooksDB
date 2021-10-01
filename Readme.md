Backend
1. Lumen Framework
2. Lumen-Generator : https://github.com/flipboxstudio/lumen-generator
3. Intervention-Image : http://image.intervention.io/getting_started/installation
4. PHP - JWT : https://github.com/firebase/php-jwt

Frontend :
1. Laravel Framework
2. Admin LTE : https://github.com/ColorlibHQ/AdminLTE/releases

Android :
- Retrofit : implementation 'com.squareup.retrofit2:retrofit:(insert latest version)'
- Retrofit GSON Adapter implementation 'com.squareup.retrofit2:converter-gson:2.9.0'
- Favourite Button : implementation 'com.github.ivbaranov:materialfavoritebutton:0.1.5'

Persiapan Backend
1. Instalasi Lumen : composer create-project --prefer-dist laravel/lumen backend
2. Lumen-Generator : https://github.com/flipboxstudio/lumen-generator
3. Buat model : {Book, Author, Favourite} php artisan make:model Book -fmc
4. Buat Routes CRUD
5. Atur exception handler