Steps for the installation.

Open the command window and follow the steps

1. git clone https://github.com/tarekllf01/file-mgr-tarek.git
2. cd file-mgr-tarek
3. mv .env.example .env
4. composer install
5. php artisan key:generate

6. php artisan storage:link
7. php artisan migrate
8. php artisan serve

or simply do these steps instead of 6,7,8

6. php artisan serve
7. run browser and visit http://127.0.0.1/install


Deafult user credentials already filled up in the login form.

email: email@email.com
pass:  password


