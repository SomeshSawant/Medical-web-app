@echo off
echo [Medical web app] Starting development server at http://127.0.0.1:8000
echo [Medical web app] Using custom php.ini configuration from %~dp0php.ini
cd /d "%~dp0"
php -c "php.ini" -S 127.0.0.1:8000 -t public
