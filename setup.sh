#!/bin/bash

# Build and start services
echo "Building and starting services..."
if ! docker compose build; then
    echo "Error: Docker build failed"
    echo "Please check the README.md for troubleshooting steps"
    exit 1
fi

# Install dependencies
echo "Installing dependencies..."
docker compose run --rm dev-php composer install
docker compose run --rm dev-vue3 npm install

# Start services
docker compose up -d

echo "Setup completed! Please check README.md for:"
echo "- Verification steps"
echo "- Troubleshooting guide"
echo "- Common commands"
echo
echo "Frontend: http://localhost:3000"
echo "Backend: http://localhost:8000"