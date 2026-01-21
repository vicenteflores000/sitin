<?php

namespace App\Services;

class IaSuggestionService
{
    public function analyze(string $descripcion): array
    {
        // Placeholder sin API real aún
        // Esto te permite desarrollar sin costo ni dependencia

        return [
            'ia_criticidad_sugerida' => $this->fakeCriticidad($descripcion),
            'ia_categoria_sugerida' => null,
            'ia_modelo' => 'placeholder-rules-v1',
        ];
    }

    private function fakeCriticidad(string $text): string
    {
        $text = strtolower($text);

        if (str_contains($text, 'no enciende') || str_contains($text, 'caído')) {
            return 'Alta';
        }

        if (str_contains($text, 'lento') || str_contains($text, 'error')) {
            return 'Media';
        }

        return 'Baja';
    }
}
