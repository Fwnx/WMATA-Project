export interface Station {
    Code: string;
    Name: string;
    StationTogether1: string;
    StationTogether2: string;
    LineCode1: string;
    LineCode2: string | null;
    LineCode3: string | null;
    LineCode4: string | null;
}

export interface Prediction {
    Car: string;
    Destination: string;
    DestinationCode: string;
    DestinationName: string;
    Group: string;
    Line: string;
    LocationCode: string;
    LocationName: string;
    Min: string;
}

export const wmataService = {
    async getStations(): Promise<Station[]> {
        const response = await fetch('/api/wmata/stations');
        if (!response.ok) {
            throw new Error('Failed to fetch stations');
        }
        const data = await response.json();
        return data.Stations;
    },

    async getPredictions(stationCode: string): Promise<Prediction[]> {
        const response = await fetch(`/api/wmata/predictions/${stationCode}`);
        if (!response.ok) {
            throw new Error('Failed to fetch predictions');
        }
        const data = await response.json();
        return data.Trains;
    }
}; 