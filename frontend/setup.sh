#!/bin/bash

# Frontend Setup Script for Unix/Linux/Mac

echo "===================================="
echo "Unified POS - Frontend Setup"
echo "===================================="
echo ""

# Check Node version
echo "Checking Node.js version..."
node -v

# Check npm
if ! command -v npm &> /dev/null
then
    echo "npm not found. Please install Node.js and npm first."
    exit 1
fi

# Install dependencies
echo ""
echo "Installing npm dependencies..."
echo "This may take a few minutes..."
npm install

# Copy .env
if [ ! -f .env ]; then
    echo ""
    echo "Copying .env.example to .env..."
    cp .env.example .env
    echo "✓ .env file created"
fi

# Show .env content
echo ""
echo "Current API URL configuration:"
cat .env
echo ""

read -p "Is the API URL correct? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]
then
    echo "Please edit .env file and set VITE_API_URL to your backend URL"
    exit 0
fi

echo ""
echo "===================================="
echo "Setup completed successfully!"
echo "===================================="
echo ""
echo "To start development server, run:"
echo "  npm run dev"
echo ""
echo "Frontend will be available at: http://localhost:5173"
echo ""
echo "To build for production, run:"
echo "  npm run build"
echo ""
