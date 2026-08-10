<?php
/**
 * ==============================================================================
 * TRABALHO PRÁTICO - BANCO DE DADOS 2 (IFMG CAMPUS OURO PRETO)
 * CURSO: Análise e Desenvolvimento de Sistemas (ADS)
 * CENÁRIO: Congresso Acadêmico de ADS (CONADS 2026)
 * ARQUIVO: php/public/index.php
 * OBJETIVO: Página inicial do congresso voltada para o participante do evento.
 * ==============================================================================
 */

require_once __DIR__ . '/../includes/header.php';

$mongoDb = Database::getMongoDb();

$totalAtividades = 0;
$totalParticipantes = 0;
$totalInscricoes = 0;

if ($mongoDb) {
    try {
        $totalAtividades = $mongoDb->atividades->countDocuments();
        $totalParticipantes = $mongoDb->participantes->countDocuments();
        $totalInscricoes = $mongoDb->inscricoes->countDocuments();
    } catch (\Exception $e) {
        // Silencia se a coleção ainda não existir
    }
}
?>

<!-- Hero Banner do Congresso -->
<div class="hero-banner">
    <div class="hero-badge-tag">
        <span>📍 IFMG Campus Ouro Preto</span>
        <span>•</span>
        <span>10 a 14 de Agosto de 2026</span>
    </div>
    
    <h1 class="hero-title">
        I Congresso Nacional de Análise e Desenvolvimento de Sistemas
    </h1>
    
    <p class="hero-desc">
        Bem-vindo ao portal oficial do <strong>CONADS 2026</strong>. Participe dos debates sobre arquitetura de software, tendências tecnológicas, oficinas práticas de alta performance e palestras ministradas por especialistas e pesquisadores da área de TI.
    </p>

    <div class="hero-metrics">
        <div class="metric-box">
            <div class="metric-value"><?= $totalAtividades ?></div>
            <div class="metric-label">Atividades na Grade</div>
        </div>
        <div class="metric-box">
            <div class="metric-value"><?= $totalParticipantes ?></div>
            <div class="metric-label">Participantes Inscritos</div>
        </div>
        <div class="metric-box">
            <div class="metric-value"><?= $totalInscricoes ?></div>
            <div class="metric-label">Inscrições Realizadas</div>
        </div>
        <div class="metric-box">
            <div class="metric-value">5 Dias</div>
            <div class="metric-label">De Imersão Tecnológica</div>
        </div>
    </div>
</div>

<!-- Destaques e Ações Rápidas -->
<div class="section-header">
    <div>
        <h2 class="section-title">Programação & Credenciamento</h2>
        <p class="section-subtitle">Acesse as principais seções do congresso para garantir sua vaga e acompanhar o evento.</p>
    </div>
</div>

<div class="grid-3">
    <div class="card">
        <div style="font-size: 2rem; margin-bottom: 0.75rem;">📅</div>
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--brand-navy); margin-bottom: 0.4rem;">Programação das Atividades</h3>
        <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.25rem;">
            Confira a lista completa de palestras, minicursos práticos e debates técnicos (*Cross-Fires*) disponíveis durante a semana do evento.
        </p>
        <a href="atividades.php" class="btn btn-primary" style="width: 100%;">Ver Programação Completa →</a>
    </div>

    <div class="card">
        <div style="font-size: 2rem; margin-bottom: 0.75rem;">✍️</div>
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--brand-navy); margin-bottom: 0.4rem;">Credenciamento de Aluno</h3>
        <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.25rem;">
            Realize o cadastro inicial de participantes para habilitar a inscrição nas oficinas práticas e minicursos laboratoriais.
        </p>
        <a href="participante_cadastrar.php" class="btn btn-primary" style="width: 100%;">Realizar Credenciamento →</a>
    </div>

    <div class="card">
        <div style="font-size: 2rem; margin-bottom: 0.75rem;">🏆</div>
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--brand-navy); margin-bottom: 0.4rem;">Atividades Populares & Filas</h3>
        <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.25rem;">
            Acompanhe o ranking das atividades mais procuradas pelos alunos e veja a posição em tempo real nas filas de espera das oficinas.
        </p>
        <a href="ranking.php" class="btn btn-primary" style="width: 100%;">Ver Ranking & Filas →</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
