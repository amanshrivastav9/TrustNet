@echo off
echo Testing TrustNet API Login
echo ========================
echo.

set API_KEY=TRN_397b8d85340a1c040f105c9bfd8dda49
set SECRET_KEY=SK_6917abe768039c6b129b6e6623862aaefad980b1f3ceea6d

echo Attempt 1:
curl -X POST http://localhost/trustnet/api/login.php -H "Content-Type: application/json" -d "{\"api_key\":\"%API_KEY%\",\"secret_key\":\"%SECRET_KEY%\",\"email\":\"test@example.com\",\"password\":\"wrong\"}"

echo.
echo Attempt 2:
curl -X POST http://localhost/trustnet/api/login.php -H "Content-Type: application/json" -d "{\"api_key\":\"%API_KEY%\",\"secret_key\":\"%SECRET_KEY%\",\"email\":\"test@example.com\",\"password\":\"wrong\"}"

echo.
echo Attempt 3:
curl -X POST http://localhost/trustnet/api/login.php -H "Content-Type: application/json" -d "{\"api_key\":\"%API_KEY%\",\"secret_key\":\"%SECRET_KEY%\",\"email\":\"test@example.com\",\"password\":\"wrong\"}"

echo.
echo Attempt 4:
curl -X POST http://localhost/trustnet/api/login.php -H "Content-Type: application/json" -d "{\"api_key\":\"%API_KEY%\",\"secret_key\":\"%SECRET_KEY%\",\"email\":\"test@example.com\",\"password\":\"wrong\"}"

echo.
echo Attempt 5 (Should show blocked):
curl -X POST http://localhost/trustnet/api/login.php -H "Content-Type: application/json" -d "{\"api_key\":\"%API_KEY%\",\"secret_key\":\"%SECRET_KEY%\",\"email\":\"test@example.com\",\"password\":\"wrong\"}"

pause