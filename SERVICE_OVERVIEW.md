# Service Overview

This document outlines the architecture and implementation details for the WMATA Train Tracking Application.

## System Architecture

The application follows a standard three-tier architecture with caching:
```
┌─────────────┐     ┌──────────────┐     ┌───────────────┐
│  Vue.js UI  │────▶│ Symfony API  │────▶│  WMATA API    │
└─────────────┘     └──────────────┘     └───────────────┘
       │                    │
       │           ┌──────────────┐
       └───────────│ Redis Cache  │
                   └──────────────┘
```

## Frontend Design

The frontend is built with Vue.js and follows a component-based architecture that emphasizes reusability and clear data flow.

### Key Components

1. **Train Arrival View**
   - Main container component
   - Manages the overall user experience
   - Coordinates data flow between child components
   - Handles error states and loading indicators

2. **Station Selection**
   - Allows users to choose from available stations
   - Provides clear feedback during loading
   - Shows errors if station data can't be loaded
   - Updates parent components when selection changes

3. **Prediction Display**
   - Shows upcoming train arrivals in a grid format
   - Color-coded by train line for easy reading
   - Displays arrival times and destinations
   - Handles empty states appropriately

4. **Loading Indicator**
   - Consistent loading experience across the app
   - Provides visual feedback during data fetches
   - Can be configured with or without text

### Data Flow Pattern

The application uses a top-down data flow pattern:
```
Train Arrival View (Parent)
├─▶ Station Selection
│   └─▶ Updates parent when station changes
├─▶ Loading Indicator
│   └─▶ Shows during any data fetch
└─▶ Prediction Display
    └─▶ Shows train arrival data
```

## Backend Services

### WMATA Integration

The backend provides a reliable interface to the WMATA API with:
- Consistent data formatting
- Error handling
- Rate limit management
- Response caching

Key data structures:
```typescript
// Train prediction information
interface TrainPrediction {
  LocationCode: string      // Station code
  Destination: string      // Terminal station
  Line: string            // RD, BL, YL, etc.
  Min: string            // Arrival time in minutes
  Car: string           // Number of cars
}

// Station information
interface Station {
  Code: string         // Station code
  Name: string        // Full station name
  Lines: string[]    // Array of lines serving station
}
```

### Caching Strategy

The application uses Redis for caching with different TTLs based on data type:
- Station Information: 24-hour cache (rarely changes)
- Train Predictions: 30-second cache (frequently updates)

Cache keys follow a consistent pattern:
```
station:{stationCode}:info
station:{stationCode}:predictions
```

### Rate Limiting

To respect WMATA's API limits, the application implements:
- Per-second limit: 10 requests
- Daily limit: 50,000 requests

Using a Redis-based sliding window implementation.

## API Endpoints

The backend exposes three main endpoints:
```
GET /api/predictions/{stationCode}  # Get upcoming trains
GET /api/stations/{stationCode}     # Get station details
GET /api/stations/list             # Get all stations
```

### Error Handling

The API provides clear error responses for common scenarios:
- Rate limit exceeded: 429 Too Many Requests
- WMATA API issues: 502 Bad Gateway
- Invalid station: 404 Not Found
- Cache misses: Automatic retry

## Testing Strategy

Our testing approach covers multiple layers of the application to ensure reliability and maintainability.

### Frontend Testing

1. **Component Tests**
   - Individual component behavior
     * Station selection interactions
     * Prediction display rendering
     * Loading states
     * Error handling
   - Component integration
     * Parent-child communication
     * Event handling
     * Data flow

2. **Service Layer Tests**
   - API client functionality
   - Data transformation
   - Error handling scenarios
   - Cache interaction

### Backend Testing

1. **Service Layer**
   - WMATA API integration
     * Request formatting
     * Response parsing
     * Error scenarios
   - Rate limiting logic
     * Request counting
     * Window management
     * Limit enforcement
   - Cache management
     * Storage/retrieval
     * TTL handling
     * Invalidation

2. **API Endpoint Tests**
   - Request validation
   - Response formatting
   - Error responses
   - HTTP status codes

### Integration Testing

1. **Full Flow Testing**
   - Station selection → prediction display
   - Cache hit/miss scenarios
   - Rate limit handling
   - Error propagation

2. **External Dependencies**
   - WMATA API interaction
   - Redis connection handling
   - Error recovery

### Performance Testing

1. **Load Testing**
   - Response times under load
   - Cache effectiveness
   - Rate limit accuracy
   - System stability

2. **Key Metrics**
   - API response times
   - Cache hit ratios
   - Error rates
   - Resource usage

### Test Environments

1. **Development**
   - Local testing with mocks
   - Rapid iteration
   - Component isolation

2. **Integration**
   - Full system testing
   - External service integration
   - Performance validation

This testing strategy ensures:
- Reliable component behavior
- Robust error handling
- Performance under load
- System stability
- Maintainable codebase

## Monitoring Considerations

Key metrics to monitor:
- API response times
- Cache hit rates
- Rate limit status
- Error frequencies
- Redis performance 