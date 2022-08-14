## Setup
- Clone the repository
- Run composer install `composer install `
- Create .env file ``` cp .env.example .env  ```
- Generate app_key ``` php artisan key:generate ```
- Add rapid_api_key to .env file ```RAPID_API_KEY=*** ```
- Add Mail configuration .env file
``` 
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io // I used mailtrap for testing
MAIL_PORT=2525
MAIL_USERNAME=***
MAIL_PASSWORD=***
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=example@example.com
```
- Start server `php artisan serve`
- Run tests `php artisan test`
