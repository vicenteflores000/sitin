<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GraphDirectoryService
{
    public function searchUsers(string $query = '', int $limit = 50): array
    {
        $query = trim($query);

        if ($query !== '') {
            $remote = $this->fetchSearchUsers($query, $limit);
            if (! empty($remote)) {
                return $remote;
            }
        }

        $users = $this->getUsersCache();
        if ($query !== '') {
            $needle = mb_strtolower($query);
            $users = array_filter($users, function (array $user) use ($needle) {
                $name = mb_strtolower($user['displayName'] ?? '');
                $mail = mb_strtolower($user['mail'] ?? '');
                $upn = mb_strtolower($user['userPrincipalName'] ?? '');
                return str_contains($name, $needle)
                    || str_contains($mail, $needle)
                    || str_contains($upn, $needle);
            });
        }

        $users = array_values($users);
        if ($limit > 0) {
            $users = array_slice($users, 0, $limit);
        }

        return $users;
    }

    protected function getUsersCache(): array
    {
        $cached = Cache::get('graph_users_cache_v1');
        if (is_array($cached) && ! empty($cached)) {
            return $cached;
        }

        $users = $this->fetchAllUsers();
        if (! empty($users)) {
            Cache::put('graph_users_cache_v1', $users, 900);
        }

        return $users;
    }

    protected function fetchAllUsers(): array
    {
        $token = $this->getToken();
        if (! $token) {
            return [];
        }

        $users = [];
        $url = 'https://graph.microsoft.com/v1.0/users?$select=id,displayName,mail,userPrincipalName&$top=999';

        while ($url) {
            $response = Http::withToken($token)->acceptJson()->get($url);
            if ($response->failed()) {
                Log::warning('Microsoft Graph users request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                break;
            }

            $payload = $response->json() ?? [];
            $users = array_merge($users, $payload['value'] ?? []);
            $url = $payload['@odata.nextLink'] ?? null;
        }

        usort($users, function (array $a, array $b) {
            return strcmp($a['displayName'] ?? '', $b['displayName'] ?? '');
        });

        return $users;
    }

    protected function fetchSearchUsers(string $query, int $limit): array
    {
        $token = $this->getToken();
        if (! $token) {
            return [];
        }

        $escaped = str_replace("'", "''", $query);
        $filter = "startswith(displayName,'{$escaped}') or startswith(mail,'{$escaped}') or startswith(userPrincipalName,'{$escaped}')";

        $response = Http::withToken($token)->acceptJson()->get('https://graph.microsoft.com/v1.0/users', [
            '$select' => 'id,displayName,mail,userPrincipalName',
            '$filter' => $filter,
            '$top' => $limit,
        ]);

        if ($response->failed()) {
            Log::warning('Microsoft Graph users search failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [];
        }

        return $response->json('value') ?? [];
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
