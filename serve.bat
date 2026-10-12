@echo off
set "PHPRC=%~dp0"
echo [MedCase Pro] Initializing PHP with SQLite/MySQL drivers from %~dp0php.ini...
echo [MedCase Pro] Starting development server at http://127.0.0.1:8000
php -c "%~dp0php.ini" artisan serve
