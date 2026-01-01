#!/bin/bash

# Backend Setup Script for Unix/Linux/Mac

echo "===================================="
echo "Unified POS - Backend Setup"
echo "===================================="
echo ""

# Check PHP version
echo "Checking PHP version..."
php -v

# Check if composer is installed
if ! command -v composer &> /dev/null
then
    echo "Composer not found. Please install Composer first."
    exit 1
fi

# Install dependencies
echo ""
echo "Installing Composer dependencies..."
composer install

# Copy .env
if [ ! -f .env ]; then
    echo ""
    echo "Copying .env.example to .env..."
    cp .env.example .env
    echo "✓ .env file created"
    echo ""
    echo "Please edit .env file and configure your database settings."
    echo "Then run this script again."
    exit 0
fi

# Generate app key
echo ""
echo "Generating application key..."
php artisan key:generate

# Check database connection
echo ""
echo "Testing database connection..."
php artisan db:show 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✓ Database connected successfully"
    
    # Run migrations
    echo ""
    read -p "Do you want to run migrations and seeders? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]
    then
        echo "Running migrations..."
        php artisan migrate
        
        echo ""
        read -p "Do you want to seed sample data? (y/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]
        then
            echo "Seeding database..."
            php artisan db:seed
            echo ""
            echo "✓ Sample data created:"
            echo "  - Owner: owner@kasir.app / password"
            echo "  - Kasir: kasir@kasir.app / password"
            echo "  - Products & Categories"
        fi
    fi
else
    echo "✗ Database connection failed"
    echo "Please check your database settings in .env file"
    exit 1
fi

echo ""
echo "===================================="
echo "Setup completed successfully!"
echo "===================================="
echo ""
echo "To start the server, run:"
echo "  php artisan serve"
echo ""
echo "API will be available at: http://localhost:8000/api"
echo ""
