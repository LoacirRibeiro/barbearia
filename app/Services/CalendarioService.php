<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class CalendarioService
{
    /**
     * Verifica se uma data específica é feriado nacional no Brasil.
     */
    public static function ehFeriado(Carbon $data)
    {
        $ano = $data->year;
        $dataFormatada = $data->format('Y-m-d');

        try {
            // Consulta a BrasilAPI buscando os feriados do ano corrente
            // O Laravel faz cache automático ou você pode guardar em cache para não estourar o limite da API
            $feriados = cache()->remember("feriados_nacionais_{$ano}", now()->addMonth(), function () use ($ano) {
                $response = Http::get("https://brasilapi.com.br/api/feriados/v1/{$ano}");
                return $response->successful() ? $response->json() : [];
            });

            foreach ($feriados as $feriado) {
                if ($feriado['date'] === $dataFormatada) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            // Se a API cair, o sistema continua funcionando (não bloqueia a barbearia)
            \Log::error("Erro ao consultar BrasilAPI: " . $e->getMessage());
        }

        return false;
    }
}