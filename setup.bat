@echo off
echo MicroKnowledge Setup Script
echo ========================

REM Check if PHP is installed
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo Error: PHP is not installed or not in PATH
    exit /b 1
)

REM Check if Composer is installed
where composer >nul 2>nul
if %errorlevel% neq 0 (
    echo Error: Composer is not installed or not in PATH
    exit /b 1
)

REM Create storage directories if they don't exist
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\logs" mkdir "storage\logs"

REM Create database directory if it doesn't exist
if not exist "database" mkdir "database"

REM Remove existing SQLite database file if it exists
if exist "database\database.sqlite" del "database\database.sqlite"

REM Create empty SQLite database file
echo Creating SQLite database file...
type nul > "database\database.sqlite"

REM Set permissions for the database file
echo Setting database file permissions...
icacls "database\database.sqlite" /grant Users:F

REM Fix migration file names if needed
if exist "database\migrations\2025_02_18_140312_create_posts_table.php" (
    echo Fixing migration file names...
    move "database\migrations\2025_02_18_140312_create_posts_table.php" "database\migrations\2014_10_12_100000_create_posts_table.php"
)
if exist "database\migrations\2025_02_18_141436_add_social_login_fields_to_users_table.php" (
    move "database\migrations\2025_02_18_141436_add_social_login_fields_to_users_table.php" "database\migrations\2014_10_12_200000_add_social_login_fields_to_users_table.php"
)

REM Copy .env file if it doesn't exist
if not exist ".env" (
    copy ".env.example" ".env"
    echo Created .env file from .env.example
)

REM Update database configuration in .env
echo Updating database configuration...
powershell -Command "(Get-Content .env) -replace 'DB_CONNECTION=.*', 'DB_CONNECTION=sqlite' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'DB_DATABASE=.*', 'DB_DATABASE=database/database.sqlite' | Set-Content .env"

REM Update session configuration in .env
echo Updating session configuration...
powershell -Command "(Get-Content .env) -replace 'SESSION_DRIVER=.*', 'SESSION_DRIVER=file' | Set-Content .env"

REM Install Composer dependencies
echo Installing Composer dependencies...
call composer install

REM Generate application key
echo Generating application key...
php artisan key:generate

REM Create storage link
echo Creating storage link...
php artisan storage:link

REM Clear all caches
echo Clearing caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

REM Drop all tables and recreate database
echo Dropping all tables and recreating database...
php artisan db:wipe --force

REM Run migrations
echo Running migrations...
php artisan migrate --force

REM Verify database tables
echo Verifying database tables...
php artisan migrate:status

echo.
echo Setup completed successfully!
echo Please make sure all migrations were executed correctly.
pause
