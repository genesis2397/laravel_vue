# laravel_vue
This project making a CRUD functionality. Also can stream thru Video.Js plugin

## Installation

- Clone the repository
- Copy .env.example to .env
- Set the DB_ environment variables in .env file
- Create a database with the name specified in DB_DATABASE
- ```composer install```
- ```npm install```
- ```php artisan key:generate```
- Migrate and seed the database with 
- ```php artisan migrate:fresh --seed```
- Generate dummy datas using Factory (optional) ----- In your CLI terminal type ```php artisan tinker``` then, ```$client = App\Models\Client::factory(10)->create();``` for (Ex. for Client data table).
- Run the application:
- ```php artisan serve```
- ```npm run dev```
- For Linking Storage for Photos (must-do)
- ```php artisan storage:link```
- ```chmod -R 775 storage```
- ```php artisan config:clear```
- You can now log in with user "john@example.com", default passwords will be setted as "Hello@123"
