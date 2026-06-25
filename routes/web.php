<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\AgendamentoController; // 🚀 Adicionado
use App\Http\Controllers\AssinaturaController;  // 🚀 Adicionado
use App\Http\Controllers\AdminController;       // 🚀 Adicionado
use App\Http\Controllers\Admin\EstoqueController;
use App\Http\Controllers\Admin\CaixaBalcaoController;

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
    // Route::get('/caixa', [AdminController::class, 'caixaForm'])->name('admin.caixa.form');
    // Route::post('/caixa/salvar', [AdminController::class, 'caixaSalvar'])->name('admin.caixa.salvar');

    Route::get('/caixa', [CaixaBalcaoController::class, 'index'])->name('admin.caixa');
    Route::get('/faturamento', [PainelController::class, 'index'])->name('admin.painel');
    Route::post('/caixa/salvar', [CaixaBalcaoController::class, 'salvar'])->name('admin.caixa.salvar');
    // // Rotas de Estoque
    Route::get('/estoque', [EstoqueController::class, 'index'])->name('admin.estoque');
    Route::post('/estoque/repor/{id}', [EstoqueController::class, 'repor'])->name('admin.estoque.repor');
    // // Rotas de Fluxo de Caixa Físico
    Route::post('/caixa/abrir', [CaixaGestaoController::class, 'abrir'])->name('admin.caixa.abrir');
    Route::post('/caixa/movimentar', [CaixaGestaoController::class, 'movimentar'])->name('admin.caixa.movimentar');
    Route::post('/caixa/fechar', [CaixaGestaoController::class, 'fechar'])->name('admin.caixa.fechar');
    Route::get('/admin/relatorio-mensal', [CaixaRelatorioController::class, 'relatorioMensal'])->name('admin.relatorio_mensal');
    Route::get('/admin/caixa/dados-fechamento', [CaixaGestaoController::class, 'dadosFechamento'])->name('admin.caixa.dados_fechamento');
    // Gestão de Colaboradores 
    // Route::get('/admin/colaboradores', [PainelController::class, 'colaboradores'])->name('admin.colaboradores');

    // Route::post('/admin/colaboradores/pagar', [PagamentoController::class, 'registrarPagamento'])->name('admin.pagamentos.store');

    // Route::get('/admin/colaboradores/evolucao', [PainelController::class, 'evolucao'])->name('admin.colaboradores.evolucao');

    Route::post('/estoque/baixa/{id}', [EstoqueController::class, 'darBaixa'])->name('admin.estoque.darBaixa');
});