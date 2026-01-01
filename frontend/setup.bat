@echo off
REM Frontend Setup Script for Windows

echo ====================================
echo Unified POS - Frontend Setup
echo ====================================
echo.

REM Check Node version
echo Checking Node.js version...
node -v
if errorlevel 1 (
    echo Node.js not found. Please install Node.js 18.x or higher.
    pause
    exit /b 1
)

REM Check npm
echo.
echo Checking npm...
npm -v
if errorlevel 1 (
    echo npm not found. Please install Node.js which includes npm.
    pause
    exit /b 1
)

REM Install dependencies
echo.
echo Installing npm dependencies...
echo This may take a few minutes...
npm install

REM Copy .env
if not exist .env (
    echo.
    echo Copying .env.example to .env...
    copy .env.example .env
    echo [32m√[0m .env file created
)

REM Show .env content
echo.
echo Current API URL configuration:
type .env
echo.

set /p correct="Is the API URL correct? (y/n): "
if /i not "%correct%"=="y" (
    echo Please edit .env file and set VITE_API_URL to your backend URL
    pause
    exit /b 0
)

echo.
echo ====================================
echo Setup completed successfully!
echo ====================================
echo.
echo To start development server, run:
echo   npm run dev
echo.
echo Frontend will be available at: http://localhost:5173
echo.
echo To build for production, run:
echo   npm run build
echo.
pause
