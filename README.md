
## Install
composer update
please edit env by adding database 
env setup 
create database
php artisan migrate
php artisan db:seed --class=SlotSeeder
php artisan serve


##procedure

Admin panel is set using guard operation
admin login -/login (eg http://127.0.0.1:8000/admin) we can register and login 
view customer list and upcoming

admin login -  /admin
customer list - /home
upcominglinks-   /upcoming-booking

Passport is used for api 
url for booking -/api/booking
url for checkout -/api/checkout

Appointment number ( OOOXXX [ Slot Number+ Appointment Sequence ] ) [ Appointment
Sequence - AAA,AAB,AAC …. ZZX, ZZY, ZZZ ] 
We'll get bookings: 26*26*26 only after its repeating
so same pattern with infite loop is created



##For cancel we need use cron job if the customer is not checkin 15min after booking start time it will automatically move to cancel 
that option is not added here

Customer can book other slot only after cancel the booking or checkout 
