<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\AgendamentoController; // 🚀 Adicionado
use App\Http\Controllers\AssinaturaController;  // 🚀 Adicionado
use App\Http\Controllers\AdminController;       // 🚀 Adicionado

// Rota da Página Inicial Pública
Route::get('/', [PublicController::class, 'index']);

// Rotas de Login
Route::get('/login', [CustomAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [CustomAuthController::class, 'login'])->name('login.post');

// Rotas de Cadastro
Route::get('/cadastro', [CustomAuthController::class, 'showCadastro'])->name('cadastro');
Route::post('/cadastro', [CustomAuthController::class, 'cadastro'])->name('cadastro.post');

// Rotas de Recuperar Senha
Route::get('/recuperar-senha', [CustomAuthController::class, 'showRecuperar'])->name('senha.recuperar');

// Rota de Logout
Route::post('/logout', [CustomAuthController::class, 'logout'])->name('logout');

// Rotas Públicas de Consulta
Route::get('/planos', [PublicController::class, 'planos'])->name('planos.index');
Route::get('/horarios', [AgendamentoController::class, 'verHorarios'])->name('horarios.disponiveis');

// ==========================================
// ROTAS DO CLIENTE (PROTEGIDAS COM AUTH)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // Agendamentos (Apontando para AgendamentoController)
    Route::get('/agendar', [AgendamentoController::class, 'agendarForm'])->name('agendar.form');
    Route::post('/agendar', [AgendamentoController::class, 'salvarAgendamento'])->name('agendar.salvar');
    Route::get('/meus-agendamentos', [AgendamentoController::class, 'meusAgendamentos'])->name('cliente.agendamentos');

    // Planos e Assinaturas - Área do Cliente (Apontando para AssinaturaController)
    Route::get('/planos/contratar/{id}', [AssinaturaController::class, 'subscreverPlanoForm'])->name('planos.contratar');
    Route::post('/planos/assinar', [AssinaturaController::class, 'salvarAssinatura'])->name('planos.assinar');
    Route::get('/meu-plano/detalhes/{id}', [AssinaturaController::class, 'detalhesPlano'])->name('planos.detalhes');
});

// ==========================================
// ROTAS DO ADMINISTRADOR (GRUPO PROTEGIDO)
// ==========================================
Route::middleware(['auth'])->prefix('admin')->group(function () {
    
    // Painel Principal e Agenda (Apontando para AdminController)
    Route::get('/painel', [AdminController::class, 'painelAdmin'])->name('admin.painel');
    Route::get('/agenda', [AdminController::class, 'agendaAdmin'])->name('admin.agenda');
    Route::patch('/agendamentos/{id}/concluir', [AdminController::class, 'concluirAgendamento'])->name('admin.agendamentos.concluir');

    // Tela de Controle de Assinaturas e Relatórios
    Route::get('/planos', [AdminController::class, 'relatorioPlanos'])->name('admin.planos');
    Route::get('/planos/relatorio', [AdminController::class, 'visualizarRelatorio'])->name('admin.planos.relatorio');
    Route::get('/planos/{id}/detalhes', [AdminController::class, 'obterDetalhes'])->name('admin.planos.detalhes');

    // 🔐 Ações Críticas de Balcão (Ações de gerência protegidas por senha)
    Route::post('/planos/confirmar/{id}', [AdminController::class, 'confirmarPagamento'])->name('admin.planos.confirmar');
    Route::post('/planos/reativar/{id}', [AdminController::class, 'reativarAssinatura'])->name('admin.planos.reativar');
    Route::delete('/planos/cancelar/{id}', [AdminController::class, 'cancelarAssinaturaPendete'])->name('admin.planos.cancelar');

    // Gestão do Caixa / Lançamento Manual de Balcão
    Route::get('/caixa', [AdminController::class, 'caixaForm'])->name('admin.caixa.form');
    Route::post('/caixa/salvar', [AdminController::class, 'caixaSalvar'])->name('admin.caixa.salvar');
});