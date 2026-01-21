<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class GlpiService
{
    protected string $url;
    protected string $appToken;
    protected string $userToken;

    protected ?string $sessionToken = null;

    public function __construct()
    {
        $this->url = rtrim(config('services.glpi.url'), '/');
        $this->appToken = config('services.glpi.app_token');
        $this->userToken = config('services.glpi.user_token');
    }

    protected function getSession(): ?string
    {
        if ($this->sessionToken) {
            return $this->sessionToken;
        }

        try {
            $response = Http::timeout(2)
                ->withHeaders([
                    'App-Token'     => $this->appToken,
                    'Authorization' => 'user_token ' . $this->userToken,
                ])
                ->get($this->url . '/initSession');

            if (! $response->successful()) {
                return null;
            }

            return $this->sessionToken = $response->json('session_token');
        } catch (Throwable) {
            return null;
        }
    }

    protected function headers(): array
    {
        return [
            'Session-Token' => $this->sessionToken,
            'App-Token'     => $this->appToken,
            'Content-Type'  => 'application/json',
        ];
    }

    protected function request(string $method, string $uri, array $options = [])
    {
        if (! $this->getSession()) {
            throw new RuntimeException('GLPI no disponible');
        }

        return Http::timeout(5)
            ->withHeaders($this->headers())
            ->$method($this->url . $uri, $options);
    }

    public function getTicket(int $ticketId): ?array
    {
        try {
            return $this->request('get', "/Ticket/{$ticketId}")
                ->json();
        } catch (Throwable) {
            return null;
        }
    }

    public function getLocations(): array
    {
        try {
            return $this->request('get', '/Location', [
                'range' => '0-9999',
            ])->json();
        } catch (Throwable) {
            return [];
        }
    }

    public function getUserIdByEmail(string $email): ?int
    {
        try {
            $login = $this->normalizeLogin($email);

            $response = $this->request('get', '/search/User', [
                'forcedisplay' => [2, 1, 5],
                'criteria' => [[
                    'field'      => 1,
                    'searchtype' => 'contains',
                    'value'      => $login,
                ]],
                'range' => '0-0',
            ])->json();

            return $response['data'][0][2] ?? null;
        } catch (Throwable) {
            return null;
        }
    }

    public function createTicket(Ticket $ticket): array
    {
        $glpiUserId = $this->getUserIdByEmail($ticket->usuario_mail);
        $payload = $this->buildPayload($ticket, $glpiUserId);

        try {
            $response = $this->request('post', '/Ticket', $payload);

            if (! $response->successful()) {
                throw new \Exception($response->body());
            }

            $ticket->update([
                'estado_envio_glpi' => 'enviado',
                'glpi_ticket_id'    => $response->json('id'),
                'payload_glpi'      => $payload,
            ]);

            return [
                'status'  => 'enviado',
                'id'      => $response->json('id'),
                'payload' => $payload,
            ];
        } catch (Throwable $e) {
            return [
                'status'  => 'error',
                'payload' => $payload,
                'error'   => $e->getMessage(),
            ];
        }
    }

    protected function buildPayload(Ticket $ticket, int $glpiUserId): array
    {
        return [
            'input' => [
                'name'                  => "[{$ticket->area}] {$ticket->categoria}",
                'content'               => $this->buildContent($ticket),
                'type'                  => 1,
                'impact'                => $this->mapImpact($ticket->impacto),
                'source'                => 'Formulario TI',
                'priority'              => $ticket->prioridad,
                'locations_id'          => $ticket->glpi_location_id,
                '_users_id_requester'   => $glpiUserId,
            ],
        ];
    }

    protected function buildContent(Ticket $ticket): string
    {
        return
            "Descripción:\n{$ticket->descripcion}\n\n" .
            "Área: {$ticket->area}\n" .
            "Categoría: {$ticket->categoria}\n" .
            "Impacto declarado: {$ticket->impacto}\n\n" .
            "Equipo: {$ticket->pc}\n" .
            "Usuario: {$ticket->usuario}\n" .
            "IP origen: {$ticket->ip_origen}\n" .
            "Origen: {$ticket->origen}";
    }

    protected function mapImpact(string $impacto): int
    {
        return match ($impacto) {
            'Impide atender usuarios' => 5,
            'Dificulta el trabajo'    => 3,
            default                  => 1,
        };
    }

    public function mapStatus(int $status): string
    {
        return match ($status) {
            1, 2, 3, 4 => 'recibido',
            5, 6       => 'resuelto',
            default    => 'desconocido',
        };
    }

    protected function normalizeLogin(string $email): string
    {
        return strtolower(
            strstr($email, '@', true) ?: $email
        );
    }
}
