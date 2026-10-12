$env:PHPRC = $PSScriptRoot
Write-Host "[MedCase Pro] Initializing PHP with SQLite/MySQL drivers from $PSScriptRoot\php.ini..." -ForegroundColor Cyan
Write-Host "[MedCase Pro] Starting development server at http://127.0.0.1:8000" -ForegroundColor Green
php -c "$PSScriptRoot\php.ini" artisan serve
