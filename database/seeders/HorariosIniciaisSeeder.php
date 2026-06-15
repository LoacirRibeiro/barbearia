<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barbeiro;
use App\Models\HorarioDisponivel;
use Carbon\Carbon;

class HorariosIniciaisSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Busca todos os barbeiros cadastrados no sistema
        $barbeiros = Barbeiro::all();

        if ($barbeiros->isEmpty()) {
            $this->command->warn('⚠️ Nenhum barbeiro encontrado! Cadastre pelo menos um barbeiro no banco antes de rodar este seeder.');
            return;
        }

        // 2. Define o dia que queremos gerar os horários (Ex: Amanhã)
        $dataAlvo = now()->addDay()->format('Y-m-d'); 

        // 3. Define o horário de início (08:00) e o limite máximo de término (19:00)
        $horaInicio = Carbon::createFromFormat('Y-m-d H:i:s', $dataAlvo . ' 08:00:00');
        $horaLimite = Carbon::createFromFormat('Y-m-d H:i:s', $dataAlvo . ' 19:00:00');

        $quantidadeCriada = 0;

        // 4. Laço para ir somando 40 minutos até atingir o limite das 19:00
        while ($horaInicio->lessThanOrEqualTo($horaLimite)) {
            
            // Cadastra esse horário para CADA um dos barbeiros do seu banco
            foreach ($barbeiros as $barbeiro) {
                // firstOrCreate evita duplicar se você rodar o comando duas vezes por engano
                HorarioDisponivel::firstOrCreate([
                    'barbeiro_id' => $barbeiro->id,
                    'data_hora'   => $horaInicio->copy(),
                ], [
                    'disponivel'  => true
                ]);
            }

            $quantidadeCriada++;
            // 🌟 Soma 40 minutos para o próximo bloco
            $horaInicio->addMinutes(40);
        }

        $this->command->info("✅ Sucesso! Gerados {$quantidadeCriada} blocos de horários (com intervalos de 40 min) para todos os barbeiros.");
    }
}