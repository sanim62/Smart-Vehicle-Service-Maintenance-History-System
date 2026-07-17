@echo off
:: ╔══════════════════════════════════════════════════╗
:: ║  PHASE 7 — FULL SYSTEM (Everything Unlocked)    ║
:: ║  Shows: + Full Admin Panel + All features        ║
:: ╚══════════════════════════════════════════════════╝

SET PATH=C:\oracle\instantclient_19;%PATH%
cd /d "c:\Users\user\Herd\database project\vehicle-service"

powershell -Command "(Get-Content .env) -replace 'SHOW_PHASE=\d+', 'SHOW_PHASE=7' | Set-Content .env"
php artisan config:clear >nul 2>&1
php artisan route:clear  >nul 2>&1

echo.
echo  [OK]  Phase 7 activated — FULL SYSTEM unlocked
echo  Open your browser to: http://vehicle-service.test
echo.
pause
