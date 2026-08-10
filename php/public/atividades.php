<?php
/**
 * ==============================================================================
 * TRABALHO PRÁTICO - BANCO DE DADOS 2 (IFMG CAMPUS OURO PRETO)
 * CURSO: Análise e Desenvolvimento de Sistemas (ADS)
 * CENÁRIO: Congresso Acadêmico de ADS (CONADS 2026)
 * ARQUIVO: php/public/atividades.php
 * OBJETIVO: Programação de atividades acadêmicas do congresso.
 * ==============================================================================
 */

require_once __DIR__ . '/../config/database.php';

$mongoDb = Database::getMongoDb();
$redis = Database::getRedis();

require_once __DIR__ . '/../includes/header.php';

// Filtro opcional por tipo de atividade
$filtroTipo = $_GET['tipo'] ?? 'todos';

// Lista de atividades a serem exibidas
$atividades = [];

if ($mongoDb) {
    $query = [];
    if ($filtroTipo !== 'todos') {
        $query['tipo'] = $filtroTipo;
    }

    try {
        $cursor = $mongoDb->atividades->find($query);
        
        foreach ($cursor as $doc) {
            $idAtividade = (string)$doc['_id'];
            $vagasMongo = $doc['vagas_disponiveis'];

            // Consulta de vagas (com cache transparente em segundo plano)
            $vagasExibidas = $vagasMongo;
            if ($redis) {
                $chaveCache = "cache:vagas:atividade:{$idAtividade}";
                $valorCache = $redis->get($chaveCache);

                if ($valorCache !== null && $valorCache !== false) {
                    $vagasExibidas = (int)$valorCache;
                } else {
                    $redis->setex($chaveCache, 60, (string)$vagasMongo);
                }
            }

            $atividades[] = [
                'doc' => $doc,
                'vagas' => $vagasExibidas
            ];
        }
    } catch (\Exception $e) {
        $_SESSION['mensagem_erro'] = "Erro ao carregar programação: " . $e->getMessage();
    }
}
?>

<div class="section-header">
    <div>
        <h1 class="section-title">Programação Geral das Atividades</h1>
        <p class="section-subtitle">
            Confira a grade oficial de Cross-Fires, Oficinas Práticas e Palestras da semana do <strong>CONADS 2026</strong>.
        </p>
    </div>
    
    <!-- Filtros por Categoria -->
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="atividades.php?tipo=todos" class="btn <?= $filtroTipo === 'todos' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">Todas as Atividades</a>
        <a href="atividades.php?tipo=Cross-fire" class="btn <?= $filtroTipo === 'Cross-fire' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">Cross-fire</a>
        <a href="atividades.php?tipo=Oficina" class="btn <?= $filtroTipo === 'Oficina' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">Oficinas Práticas</a>
        <a href="atividades.php?tipo=Palestra" class="btn <?= $filtroTipo === 'Palestra' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">Palestras</a>
    </div>
</div>

<!-- Lista de Cards de Atividades -->
<?php if (empty($atividades)): ?>
    <div class="card" style="text-align: center; padding: 3.5rem 1.5rem;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--brand-navy);">Nenhuma atividade cadastrada nesta categoria</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.4rem;">
            Selecione outro filtro acima ou acesse a programação completa.
        </p>
    </div>
<?php else: ?>
    <div class="grid-2">
        <?php foreach ($atividades as $item): ?>
            <?php 
                $doc = $item['doc']; 
                $idAtiv = (string)$doc['_id'];
                $temVagas = $item['vagas'] > 0;
            ?>
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <!-- Header do Card: Tipo + Status de Vagas -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.9rem; gap: 0.5rem; flex-wrap: wrap;">
                        <span style="background-color: #F1F5F9; color: #334155; font-size: 0.72rem; font-weight: 700; padding: 0.3rem 0.65rem; border-radius: var(--radius-sm); text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid #E2E8F0;">
                            📌 <?= htmlspecialchars($doc['tipo']) ?>
                        </span>

                        <?php if ($temVagas): ?>
                            <span class="badge-available">
                                🟢 Vagas Disponíveis
                            </span>
                        <?php else: ?>
                            <span class="badge-soldout">
                                ⏳ Esgotada (Fila de Espera)
                            </span>
                        <?php endif; ?>
                    </div>

                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--brand-navy); margin-bottom: 0.6rem; line-height: 1.35;">
                        <?= htmlspecialchars($doc['titulo']) ?>
                    </h3>

                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1.25rem; line-height: 1.55;">
                        <?= htmlspecialchars($doc['descricao'] ?? '') ?>
                    </p>

                    <!-- Detalhes do Evento -->
                    <div style="background-color: #F8FAFC; padding: 0.85rem 1rem; border-radius: var(--radius-md); font-size: 0.85rem; margin-bottom: 1.25rem; border: 1px solid var(--border-color);">
                        <div style="margin-bottom: 0.3rem;">
                            <strong style="color: var(--brand-navy);">👤 Palestrante/Facilitador:</strong> 
                            <span style="color: var(--text-main);"><?= htmlspecialchars($doc['palestrante']['nome'] ?? 'A definir') ?></span>
                        </div>
                        
                        <?php if (!empty($doc['ferramentas_comparadas'])): ?>
                            <div style="margin-bottom: 0.3rem;">
                                <strong style="color: var(--brand-navy);">🛠️ Tópicos em Destaque:</strong> 
                                <span style="color: var(--brand-blue); font-weight: 600;">
                                    <?= implode(' vs ', array_map('htmlspecialchars', (array)$doc['ferramentas_comparadas'])) ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <div>
                            <strong style="color: var(--brand-navy);">📍 Local do Evento:</strong> 
                            <span style="color: var(--text-main);"><?= htmlspecialchars($doc['local'] ?? 'Auditório Principal') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Rodapé do Card (Vagas & Ação) -->
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.85rem; border-top: 1px solid var(--border-color);">
                        <div>
                            <span style="font-size: 0.78rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Vagas Restantes:</span><br>
                            <strong style="font-size: 1.25rem; font-family: 'Plus Jakarta Sans', sans-serif; color: <?= $temVagas ? 'var(--status-success)' : 'var(--status-danger)' ?>;">
                                <?= $item['vagas'] ?> <span style="font-size: 0.85rem; font-weight: 500; color: var(--text-muted);">/ <?= $doc['vagas_totais'] ?></span>
                            </strong>
                        </div>
                        
                        <?php if ($temVagas): ?>
                            <a href="inscricao.php?atividade_id=<?= $idAtiv ?>" class="btn btn-primary btn-sm">
                                Garantir Vaga →
                            </a>
                        <?php else: ?>
                            <a href="inscricao.php?atividade_id=<?= $idAtiv ?>" class="btn btn-sm" style="background-color: #FEF2F2; color: #991B1B; border: 1px solid #FCA5A5;">
                                ⏳ Entrar na Fila de Espera
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
