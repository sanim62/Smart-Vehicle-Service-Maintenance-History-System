@echo off
:: ╔══════════════════════════════════════════════════╗
:: ║  PHASE 5 — Bookings & Services                   ║
:: ║  Shows: + Booking system + Service records       ║
:: ╚══════════════════════════════════════════════════╝

SET PATH=C:\oracle\instantclient_19;%PATH%
cd /d "c:\Users\user\Herd\database project\vehicle-service"

powershell -Command "(Get-Content .env) -replace 'SHOW_PHASE=\d+', 'SHOW_PHASE=5' | Set-Content .env"
php artisan config:clear >nul 2>&1
php artisan route:clear  >nul 2>&1

echo.
echo  [OK]  Phase 5 activated — Bookings and Services unlocked
echo  Open your browser to: http://vehicle-service.test
echo.
pause
