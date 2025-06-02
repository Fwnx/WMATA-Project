# Service Overview

This document outlines the planned architecture and implementation details for the WMATA Train Tracking Application.

## System Architecture
```
┌─────────────┐     ┌──────────────┐     ┌───────────────┐
│  Vue.js UI  │────▶│ Symfony API  │────▶│  WMATA API    │
└─────────────┘     └──────────────┘     └───────────────┘
       │                    │
       │           ┌──────────────┐
       └───────────│ Redis Cache  │
                   └──────────────┘
```

## Core Services

### 1. WMATA Integration Service
- Primary interface for WMATA API communication
- Data Types:
  ```typescript
  interface TrainPrediction {
    LocationCode: string      // Station code
    Destination: string      // Terminal station
    Line: string            // RD, BL, YL, etc.
    Min: string            // Arrival time in minutes
    Car: string           // Number of cars
  }

  interface Station {
    Code: string         // Station code
    Name: string        // Full station name
    Lines: string[]    // Array of lines serving station
  }
  ```

### 2. Cache Service
- Caching Layer:
  - Station Info: 24-hour TTL
  - Predictions: 30-second TTL
- Cache Keys:
  ```
  station:{stationCode}:info
  station:{stationCode}:predictions
  ```

### 3. Rate Limiter Service
- Rate Limits:
  - Per Second: 10 requests
  - Per Day: 50,000 requests
- Implementation:
  - Uses Redis sliding window
  - Key Format: `ratelimit:wmata:{window}:{identifier}`

## Data Flow

### 1. Train Prediction Flow
```
Client Request
→ Check Redis Cache
→ If cached: Return cached data
→ If not cached:
  → Check rate limit
  → Call WMATA API
  → Cache response
  → Return to client
```

### 2. Station Information Flow
```
Client Request
→ Check Redis Cache
→ If cached: Return cached data
→ If not cached:
  → Check rate limit
  → Call WMATA API
  → Cache response (24h)
  → Return to client
```

## API Design

### Endpoints
```
GET /api/predictions/{stationCode}
GET /api/stations/{stationCode}
GET /api/stations/list
```

### Error Handling
- Rate Limit Exceeded: 429 Too Many Requests
- WMATA API Error: 502 Bad Gateway
- Cache Miss: Transparent retry
- Invalid Station: 404 Not Found

## Testing Strategy

### 1. Unit Tests

#### Backend Tests
- Service Layer
  - WMATA API integration
  - Response parsing
  - Error handling
- Cache Layer
  - Hit/miss behavior
  - TTL expiration
  - Key management
- Rate Limiter
  - Request throttling
  - Window behavior

#### Frontend Tests
- Component rendering
- Data fetching
- Error state handling
- Real-time updates

### 2. Integration Tests

#### API Integration
- Full prediction flow
- Cache integration
- Rate limit integration
- Error propagation
- Redis connection handling

### 3. E2E Tests
- Real-time prediction display
- Station information retrieval
- Error handling
- Performance under load

### 4. Performance Testing

#### Load Tests
- Endpoint response times
- Cache effectiveness
- Rate limit behavior
- System stability

#### Key Metrics
- Response time under load
- Cache hit ratio
- Rate limit effectiveness
- Redis performance
- API error rates

### 5. Test Data Management

#### Mock Data
```typescript
export const mockPredictions = [{
  LocationCode: 'A01',
  Destination: 'Glenmont',
  Line: 'RD',
  Min: '3',
  Car: '8'
}]
```

#### Test Environments
1. **Local Development**
   - Mock WMATA API
   - Local Redis instance
   - Seeded test data

2. **CI Environment**
   - Containerized services
   - Test-specific Redis instance
   - API simulation

3. **Staging**
   - Real WMATA API (test key)
   - Production-like Redis setup
   - Sanitized data

### 6. Testing Tools
- **Backend**
  - PHPUnit for unit/integration tests
  - Mockery for service mocking
  - PHP Redis mock for cache testing

- **Frontend**
  - Vitest for unit tests
  - Vue Test Utils for component testing
  - Cypress for E2E tests

- **Infrastructure**
  - k6 for load testing
  - Docker Compose for test environments
  - GitHub Actions for CI/CD

Key areas requiring thorough testing:
- Rate limiting logic
- Cache invalidation
- Error handling paths
- API response parsing
- Real-time updates 