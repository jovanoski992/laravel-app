<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

- Created command in Laravel which generates CSV file with 90 days schedule for cleaning office from given date.

After you download the code and set up your environment, migrate the db etc.
You can call the method from command line using following command: 

- php artisan **command:generate_schedule**
- it will ask you to write date in format yyyy-mm-dd
- After adding the date it will generate the CSV and display the success message.
- File will be generated and located into csv folder into root of the project.
- Command does have validation for the date format, and shows error message if it's wrong format.
