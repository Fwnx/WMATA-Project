# WMATA Train Tracking Application

A real-time train tracking application for the Washington Metropolitan Area Transit Authority (WMATA) system using Symfony and Vue.js.

*This project was forked from [DSPolitical/vue3-symfony](https://github.com/DSPolitical/vue3-symfony) and customized for WMATA integration.*

## Prerequisites

- Docker
- WMATA API Key ([Get one here](https://developer.wmata.com/))
- curl (for health checks)
- bash shell

## Setup Options

### 1. Environment Setup (Required)
First, create your environment configuration:

1. Create a new file `backend/.env.local` with the following content:
   ```
   APP_ENV=dev
   WMATA_API_KEY=your_api_key_here
   ```
   Replace `your_api_key_here` with your actual WMATA API key.

   Note: Redis configuration is handled through Docker Compose and Symfony configuration files. You don't need to add any Redis-related variables to `.env.local`.

2. Verify the file is created and contains your API key.

### 2. Choose Your Installation Method

#### Option A: Using the Setup Script (Linux/Mac)
```bash
# Run the setup script
./setup.sh
```
The script will:
- Build and start all services
- Install dependencies
- Start the application

#### Option B: Manual Setup
```bash
# Build Docker images
docker compose build

# Install PHP dependencies
docker compose run --rm dev-php composer install

# Install Node dependencies
docker compose run --rm dev-vue3 npm install

# Start all services
docker compose up -d
```

### 3. Verify Installation
- Frontend should be available at: http://localhost:3000
- Backend should be available at: http://localhost:8000
- Redis should be running and accessible

## Features

- Real-time train arrival predictions
- Station information caching (24-hour cache)
- Prediction caching (30-second cache)
- API rate limiting (10 requests/second, 50,000/day)
- Redis-backed caching and rate limiting

## First-Time Usage Notes

1. The first API request might be slower due to cache warming
2. Verify your WMATA API key is working correctly
3. Redis cache will be empty on first startup

## Troubleshooting

1. **Redis Connection Issues**
   - Verify Redis is running: `docker compose ps`
   - Check Redis logs: `docker compose logs redis`
   - Default timeout for Redis connection is 30 seconds

2. **API Key Issues**
   - Verify your API key in `.env.local`
   - Check the backend logs: `docker compose logs symfony`

3. **Frontend Issues**
   - Check Node.js logs: `docker compose logs vue3`
   - Verify the development server is running
   - Frontend may take a few moments to become available

4. **Service Health Checks**
   ```bash
   # Check Redis connection
   docker compose exec redis redis-cli ping

   # Check frontend availability
   curl -s http://localhost:3000

   # Check backend availability
   curl -s http://localhost:8000
   ```

## Development

The project uses:
- Symfony 7.2 for the backend
- Vue 3 for the frontend
- Redis for caching and rate limiting
- Docker for containerization

## Common Commands

```bash
# View logs
docker compose logs

# Restart services
docker compose restart

# Stop all services
docker compose down

# Clear Redis cache
docker compose exec redis redis-cli FLUSHALL
```

## Architecture

- **WmataService**: Handles WMATA API communication
- **WmataCacheService**: Manages Redis-based caching
- **WmataRateLimiterService**: Handles API rate limiting