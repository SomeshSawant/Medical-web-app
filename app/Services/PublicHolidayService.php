<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PublicHolidayService
{
    /**
     * Fetch upcoming public holidays worldwide or for a specified country code.
     * Uses timeout and graceful error recovery so medical forms are never blocked.
     *
     * @return Collection<int, array{date: string, name: string, localName: string, countryCode: string}>
     */
    public function getUpcomingHolidays(int $limit = 5): Collection
    {
        $apiUrl = config('services.nager_date.url', env('PUBLIC_HOLIDAYS_API_URL', 'https://date.nager.at/api/v3/NextPublicHolidaysWorldwide'));

        try {
            $response = Http::timeout(3)
                ->acceptJson()
                ->get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();

                if (is_array($data)) {
                    return collect($data)
                        ->take($limit)
                        ->map(fn ($item) => [
                            'date' => $item['date'] ?? '',
                            'name' => $item['name'] ?? '',
                            'localName' => $item['localName'] ?? '',
                            'countryCode' => $item['countryCode'] ?? '',
                        ]);
                }
            }

            Log::warning('PublicHolidayService: Non-successful response from Nager.Date API', [
                'status' => $response->status(),
            ]);
        } catch (\Throwable $e) {
            Log::info('PublicHolidayService: Unable to fetch holidays from external API (handled gracefully)', [
                'message' => $e->getMessage(),
            ]);
        }

        return collect();
    }
}
