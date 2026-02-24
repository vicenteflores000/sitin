<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OutlookCalendarService
{
    public function createEvent(array $payload): ?array
    {
        $mailbox = config('services.microsoft_graph.mailbox');
        if (! $mailbox) {
            return null;
        }

        return $this->request("users/{$mailbox}/events", $payload, 'POST');
    }

    public function updateEvent(string $eventId, array $payload): ?array
    {
        $mailbox = config('services.microsoft_graph.mailbox');
        if (! $mailbox) {
            return null;
        }

        return $this->request("users/{$mailbox}/events/{$eventId}", $payload, 'PATCH');
    }

    public function deleteEvent(string $eventId): bool
    {
        $mailbox = config('services.microsoft_graph.mailbox');
        if (! $mailbox) {
            return false;
        }

        $response = $this->request("users/{$mailbox}/events/{$eventId}", null, 'DELETE');

        return $response !== null;
    }

    protected function request(string $path, ?array $payload, string $method): ?array
    {
        $token = $this->getToken();
        if (! $token) {
            return null;
        }

        $url = 'https://graph.microsoft.com/v1.0/' . ltrim($path, '/');
        $request = Http::withToken($token)->acceptJson();

        if ($method === 'POST') {
            $response = $request->post($url, $payload ?? []);
        } elseif ($method === 'PATCH') {
            $response = $request->patch($url, $payload ?? []);
        } elseif ($method === 'DELETE') {
            $response = $request->delete($url);
        } else {
            $response = $request->get($url);
        }

        if ($response->failed()) {
            Log::warning('Microsoft Graph request failed', [
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        return $response->json() ?? [];
    }

    protected function getToken(): ?string
    {
        return Cache::remember('microsoft_graph_token', 3000, function () {
            $tenantId = config('services.microsoft_graph.tenant_id');
            $clientId = config('services.microsoft_graph.client_id');
            $clientSecret = config('services.microsoft_graph.client_secret');

            if (! $tenantId || ! $clientId || ! $clientSecret) {
                Log::warning('Microsoft Graph credentials missing.');
                return null;
            }

            $response = Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => 'https://graph.microsoft.com/.default',
                'grant_type' => 'client_credentials',
            ]);

            if ($response->failed()) {
                Log::warning('Microsoft Graph token request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $token = $response->json('access_token');
            $expires = (int) $response->json('expires_in', 3600);
            if (! $token) {
                return null;
            }

            Cache::put('microsoft_graph_token', $token, max($expires - 120, 300));

            return $token;
        });
    }
}
