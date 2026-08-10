<?php
/**
 * ==============================================================================
 * TRABALHO PRÁTICO - BANCO DE DADOS 2 (IFMG CAMPUS OURO PRETO)
 * CURSO: Análise e Desenvolvimento de Sistemas (ADS)
 * CENÁRIO: Congresso Acadêmico de ADS (CONADS 2026)
 * ARQUIVO: php/public/ranking.php
 * OBJETIVO: Ranking de atividades mais procuradas e acompanhamento de filas de espera.
 * ==============================================================================
 */

require_once __DIR__ . '/../includes/header.php';

$redis = Database::getRedis();

$rankingPopulares = [];
$filasEspera = [];
$resumosHashes = [];

if ($redis) {
    try {
        // Busca Ranking de Popularidade
        $rawRanking = $redis->zrevrange('ranking:atividades:populares', 0, -1, 'WITHSCORES');
        if (is_array($rawRanking) && !empty($rawRanking)) {
            $rankingPopulares = $rawRanking;
        }

        // Busca Filas de Espera de atividades
        $chavesFilas = ['104', '103', '101'];
        foreach ($chavesFilas as $idAtiv) {
            $chaveFila = "fila:espera:atividade:{$idAtiv}";
            $alunosFila = $redis->lrange($chaveFila, 0, -1);
            if (!empty($alunosFila)) {
                $filasEspera[$idAtiv] = $alunosFila;
            }
        }

        // Busca Resumos de Atividades
        $chavesHashes = ['101', '103', '104'];
        foreach ($chavesHashes as $idAtiv) {
            $chaveHash = "resumo:atividade:{$idAtiv}";
            $resumo = $redis->hgetall($chaveHash);
            if (!empty($resumo)) {
                $resumosHashes[$idAtiv] = $resumo;
            }
        }
    } catch (\Exception $e) {
        $_SESSION['mensagem_erro'] = "Erro ao carregar dados de popularidade: " . $e->getMessage();
    }
}
?>

<div class="section-header">
    <div>
        <h1 class="section-title">Atividades Mais Procuradas & Filas de Espera</h1>
        <p class="section-subtitle">
            Acompanhe o ranking de popularidade das palestras e oficinas do <strong>CONADS 2026</strong> e a ordem nas filas de espera.
        </p>
    </div>
</div>

<div class="grid-2">
    <!-- RANKING DE POPULARIDADE -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <div style="width: 36px; height: 36px; background: #F0F9FF; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">🏆</div>
                <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--brand-navy);">
                    Ranking de Popularidade
                </h2>
            </div>
            <span style="font-size: 0.78rem; background: #E0F2FE; color: #0369A1; padding: 0.25rem 0.65rem; border-radius: var(--radius-sm); font-weight: 700;">
                Atualização em Tempo Real
            </span>
        </div>

        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem; line-height: 1.5;">
            Classificação automática baseada no volume de buscas, interações e inscrições realizadas pelos participantes.
        </p>

        <?php if (empty($rankingPopulares)): ?>
            <div style="text-align: center; padding: 2.5rem 1rem; background: #F8FAFC; border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
                <p style="color: var(--text-muted); font-size: 0.88rem;">Nenhum dado de popularidade registrado ainda.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 80px;">Posição</th>
                            <th>Atividade</th>
                            <th style="text-align: right;">Pontos de Interesse</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $posicao = 1; 
                            foreach ($rankingPopulares as $item => $score): 
                        ?>
                            <tr>
                                <td>
                                    <span style="font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 0.95rem; color: var(--brand-navy);">
                                        #<?= $posicao ?>
                                    </span>
                                    <?php if ($posicao == 1): ?> 🥇 <?php elseif ($posicao == 2): ?> 🥈 <?php elseif ($posicao == 3): ?> 🥉 <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="color: var(--brand-navy); font-size: 0.88rem;"><?= htmlspecialchars($item) ?></strong>
                                </td>
                                <td style="text-align: right;">
                                    <span style="font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 1.05rem; color: var(--brand-blue);">
                                        <?= (int)$score ?> pts
                                    </span>
                                </td>
                            </tr>
                        <?php 
                            $posicao++;
                            endforeach; 
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- FILAS DE ESPERA -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <div style="width: 36px; height: 36px; background: #FFFBEB; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">⏳</div>
                <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--brand-navy);">
                    Filas de Espera (Oficinas Esgotadas)
                </h2>
            </div>
            <span style="font-size: 0.78rem; background: #FFFBEB; color: #B45309; border: 1px solid #FCD34D; padding: 0.25rem 0.65rem; border-radius: var(--radius-sm); font-weight: 700;">
                Ordem de Chegada
            </span>
        </div>

        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem; line-height: 1.5;">
            Lista de alunos aguardando abertura de novas vagas ou desistências em atividades esgotadas.
        </p>

        <?php if (empty($filasEspera)): ?>
            <div style="text-align: center; padding: 2.5rem 1rem; background: #F8FAFC; border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
                <p style="color: var(--text-muted); font-size: 0.88rem;">Nenhuma fila de espera no momento. Todas as atividades têm vagas livres!</p>
            </div>
        <?php else: ?>
            <?php foreach ($filasEspera as $idAtiv => $alunos): ?>
                <div style="background-color: #F8FAFC; border: 1px solid var(--border-color); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem;">
                        <strong style="color: var(--brand-navy); font-size: 0.9rem;">
                            📍 Fila para a Atividade #<?= htmlspecialchars($idAtiv) ?>
                        </strong>
                        <span style="font-size: 0.78rem; background: #FEF2F2; color: #991B1B; border: 1px solid #FCA5A5; padding: 0.15rem 0.5rem; border-radius: 4px; font-weight: 700;">
                            <?= count($alunos) ?> Aluno(s) Aguardando
                        </span>
                    </div>

                    <ol style="margin-left: 1.2rem; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">
                        <?php foreach ($alunos as $alunoStr): ?>
                            <li style="color: var(--text-main); font-weight: 500;">
                                <?= htmlspecialchars($alunoStr) ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- RESUMOS DAS ATIVIDADES EM DESTAQUE -->
<div class="card" style="margin-top: 1.75rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <div style="width: 36px; height: 36px; background: #ECFDF5; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">📌</div>
            <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--brand-navy);">
                Ficha Resumo das Atividades em Destaque
            </h2>
        </div>
    </div>

    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem;">
        Informações sintéticas das principais sessões técnicas do congresso para consulta rápida.
    </p>

    <?php if (empty($resumosHashes)): ?>
        <div style="text-align: center; padding: 2rem 1rem; background: #F8FAFC; border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
            <p style="color: var(--text-muted); font-size: 0.88rem;">Nenhum resumo em destaque cadastrado no momento.</p>
        </div>
    <?php else: ?>
        <div class="grid-3">
            <?php foreach ($resumosHashes as $idAtiv => $hash): ?>
                <div style="background: #F8FAFC; padding: 1.1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--brand-blue); margin-bottom: 0.3rem;">
                        Sessão #<?= htmlspecialchars($idAtiv) ?>
                    </div>
                    <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--brand-navy); margin-bottom: 0.5rem;">
                        <?= htmlspecialchars($hash['titulo'] ?? 'Atividade ' . $idAtiv) ?>
                    </h4>
                    <div style="font-size: 0.83rem; color: var(--text-muted); line-height: 1.5;">
                        <div><strong>Categoria:</strong> <?= htmlspecialchars($hash['tipo'] ?? 'N/A') ?></div>
                        <div><strong>Facilitador:</strong> <?= htmlspecialchars($hash['palestrante'] ?? 'N/A') ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
