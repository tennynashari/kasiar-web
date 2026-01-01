@echo off
REM Backend Setup Script for Windows

echo ====================================
echo Unified POS - Backend Setup
echo ====================================
echo.

REM Check PHP version
echo Checking PHP version...
php -v
if errorlevel 1 (
    echo PHP not found. Please install PHP 8.2 or higher.
    pause
    exit /b 1
)

REM Check Composer
echo.
echo Checking Composer...
composer --version
if errorlevel 1 (
    echo Composer not found. Please install Composer first.
    pause
    exit /b 1
)

REM Install dependencies
echo.
echo Installing Composer dependencies...
composer install

REM Copy .env
if not exist .env (
    echo.
    echo Copying .env.example to .env...
    copy .env.example .env
    echo.
    echo [32m√[0m .env file created
    echo.
    echo Please edit .env file and configure your database settings.
    echo Then run this script again.
    pause
    exit /b 0
)

REM Generate app key
echo.
echo Generating application key...
php artisan key:generate

REM Test database connection
echo.
echo Testing database connection...
php artisan db:show 2>nul
if errorlevel 1 (
    echo [31m×[0m Database connection failed
    echo Please check your database settings in .env file
    pause
    exit /b 1
)

echo [32m√[0m Database connected successfully

REM Run migrations
echo.
set /p migrate="Do you want to run migrations? (y/n): "
if /i "%migrate%"=="y" (
    echo Running migrations...
    php artisan migrate
    
    echo.
    set /p seed="Do you want to seed sample data? (y/n): "
    if /i "%seed%"=="y" (
        echo Seeding database...
        php artisan db:seed
        echo.
        echo [32m√[0m Sample data created:
        echo   - Owner: owner@kasir.app / password
        echo   - Kasir: kasir@kasir.app / password
        echo   - Products ^& Categories
    )
)

echo.
echo ====================================
echo Setup completed successfully!
echo ====================================
echo.
echo To start the server, run:
echo   php artisan serve
echo.
echo API will be available at: http://localhost:8000/api
echo.
pause
