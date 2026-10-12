Write-Host "[Jajal Medical] Starting development server at http://127.0.0.1:8000" -ForegroundColor Green
Write-Host "[Jajal Medical] Using custom php.ini configuration from $PSScriptRoot\php.ini" -ForegroundColor Cyan
Set-Location -Path $PSScriptRoot
php -c "php.ini" -S 127.0.0.1:8000 -t public
