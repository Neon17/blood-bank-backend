## Plan

- If the user wants privacy as receiver or donor, we will hide their names, location but not current city, location
- We will show phone number and verification status despite privacy but other things are shown
- Receiver user can also change contact number to display
- Donor also wants privacy
- I think Showing blood requests only is highly needed


## Instead of Blood Donor, lets make it Blood Requests Website and App

## Another Important Idea:
    - Not Exposing Donor's Contact Number, (because anytime calling to donor may harm to privacy)
    - Requests should have contact number and man who wants requests can hide their name,....
    - Smart Notification is sent through mobile, email (if subscribed to email), or through web
    - Notification is sent only to those user who are nearby
    - If possible, Blood Requests privacy also can be maintained somehow or may be not
    - Make blood requests like facebook like posts (comments, like, viewed, description of requests, anything he/she types)
  

## How to Run This Project

### Prerequisites
- PHP 8.0 or higher
- Composer
- MySQL or other supported database
- Node.js (for frontend assets if applicable)

### Installation

1. Clone the repository
   ```bash
   git clone [repository-url]
   cd [project-directory]
    ```

2. Install PHP dependencies
    ```bash
   composer install
   ```

4. Install JavaScript dependencies (if needed)
   ```bash
   npm install
   ```

5. Create environment file
   ```bash
   cp .env.example .env
   ```

6. Generate application key
   ```bash
   php artisan key:generate
   ```

7. Configure database
   Edit the .env file with your database credentials:
   ```php
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=root
   DB_PASSWORD=
   ```

8. Run database migrations
   ```bash
   php artisan migrate
   ```

9. Seed database (optional)
   ```bash
   php artisan db:seed
   ```

### Running the Application

1. Start the development server
   php artisan serve

2. Access the application
   Open your browser and visit:
   http://localhost:8000

### Additional Commands

- Clear cache
  php artisan cache:clear
  php artisan view:clear
  php artisan route:clear

- Run tests
  php artisan test

Note: For production deployment, additional steps like optimizing autoloader and configuring proper environment variables are required.
