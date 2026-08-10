<?php
/**
 * ==============================================================================
 * TRABALHO PRÁTICO - BANCO DE DADOS 2 (IFMG CAMPUS OURO PRETO)
 * CURSO: Análise e Desenvolvimento de Sistemas (ADS)
 * CENÁRIO: Congresso Acadêmico de ADS (CONADS 2026)
 * ARQUIVO: php/public/inscricao.php
 * OBJETIVO: Formulário de inscrição em atividades do congresso.
 * ==============================================================================
 */

require_once __DIR__ . '/../config/database.php';

$mongoDb = Database::getMongoDb();
$redis = Database::getRedis();

$idAtividade = $_GET['atividade_id'] ?? $_POST['atividade_id'] ?? null;

if (!$idAtividade) {
    $_SESSION['mensagem_erro'] = "Selecione uma atividade válida para realizar a inscrição.";
    header("Location: atividades.php");
    exit;
}

// Busca detalhes da atividade
$atividadeDoc = null;
if ($mongoDb) {
    try {
        $atividadeDoc = $mongoDb->atividades->findOne(['_id' => $idAtividade]);
    } catch (\Exception $e) {}
}

if (!$atividadeDoc) {
    $_SESSION['mensagem_erro'] = "Atividade '{$idAtividade}' não encontrada.";
    header("Location: atividades.php");
    exit;
}

// Busca lista de participantes credenciados
$participantes = [];
if ($mongoDb) {
    try {
        $cursor = $mongoDb->participantes->find(['ativo' => true]);
        $participantes = iterator_to_array($cursor);
    } catch (\Exception $e) {}
}

// Processa envio do formulário de inscrição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idParticipante = $_POST['participante_id'] ?? '';
    
    if (empty($idParticipante)) {
        $_SESSION['mensagem_erro'] = "Selecione um participante para continuar.";
    } else {
        try {
            $vagasLivres = (int)$atividadeDoc['vagas_disponiveis'];
            $tituloAtiv = $atividadeDoc['titulo'];

            if ($vagasLivres > 0) {
                // Inscrição com vaga confirmada
                $idInscricao = 'insc_' . sprintf('%03d', rand(100, 999));
                $mongoDb->inscricoes->insertOne([
                    '_id' => $idInscricao,
                    'participante_id' => $idParticipante,
                    'atividade_id' => $idAtividade,
                    'data_inscricao' => new \MongoDB\BSON\UTCDateTime(),
                    'status' => 'confirmada',
                    'presenca_confirmada' => false
                ]);

                // Decrementa 1 vaga
                $mongoDb->atividades->updateOne(
                    ['_id' => $idAtividade],
                    ['$inc' => ['vagas_disponiveis' => -1]]
                );

                if ($redis) {
                    $redis->del("cache:vagas:atividade:{$idAtividade}");
                    $redis->zincrby("ranking:atividades:populares", 10, "{$idAtividade} - {$tituloAtiv}");
                }

                $_SESSION['mensagem_sucesso'] = "Inscrição confirmada com sucesso! Seu lugar na atividade está garantido.";
            } else {
                // Atividade esgotada: adiciona na fila de espera
                if ($redis) {
                    $partDoc = $mongoDb->participantes->findOne(['_id' => $idParticipante]);
                    $nomePart = $partDoc['nome'] ?? $idParticipante;

                    $redis->rpush("fila:espera:atividade:{$idAtividade}", "{$idParticipante} - {$nomePart}");
                    $redis->zincrby("ranking:atividades:populares", 5, "{$idAtividade} - {$tituloAtiv}");

                    $_SESSION['mensagem_sucesso'] = "⚠️ Vagas esgotadas! O participante '{$nomePart}' foi incluído na FILA DE ESPERA da atividade. Você será avisado caso surja uma desistência.";
                } else {
                    $_SESSION['mensagem_erro'] = "Atividade esgotada no momento.";
                }
            }

            header("Location: atividades.php");
            exit;
        } catch (\Exception $e) {
            $_SESSION['mensagem_erro'] = "Erro ao processar inscrição: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="card" style="max-width: 680px; margin: 2rem auto;">
    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
        <div style="width: 42px; height: 42px; background: #EEF2FF; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">🎟️</div>
        <div>
            <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--brand-navy);">Inscrição em Atividade Acadêmica</h2>
            <span style="font-size: 0.8rem; color: var(--text-muted);">Confirme seus dados para garantir sua participação</span>
        </div>
    </div>

    <!-- Resumo da Atividade Escolhida -->
    <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-left: 4px solid var(--brand-blue); padding: 1.1rem; border-radius: var(--radius-md); margin-bottom: 1.75rem;">
        <span style="font-size: 0.75rem; font-weight: 700; color: var(--brand-blue); text-transform: uppercase; letter-spacing: 0.5px;">Atividade Selecionada</span>
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--brand-navy); margin: 0.25rem 0 0.5rem 0;">
            <?= htmlspecialchars($atividadeDoc['titulo']) ?>
        </h3>
        
        <div style="font-size: 0.85rem; color: var(--text-muted); display: flex; gap: 1rem; flex-wrap: wrap;">
            <div><strong>Categoria:</strong> <?= htmlspecialchars($atividadeDoc['tipo']) ?></div>
            <div><strong>Facilitador:</strong> <?= htmlspecialchars($atividadeDoc['palestrante']['nome'] ?? 'A definir') ?></div>
            <div><strong>Local:</strong> <?= htmlspecialchars($atividadeDoc['local'] ?? 'Auditório') ?></div>
        </div>

        <div style="margin-top: 0.85rem; padding-top: 0.75rem; border-top: 1px dashed var(--border-color); display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 0.85rem; color: var(--text-muted);">Disponibilidade de Vagas:</span>
            <?php if ($atividadeDoc['vagas_disponiveis'] > 0): ?>
                <span class="badge-available">🟢 <?= $atividadeDoc['vagas_disponiveis'] ?> Vagas Disponíveis</span>
            <?php else: ?>
                <span class="badge-soldout">
                    ⏳ ESGOTADA (Inscrição para Fila de Espera)
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Formulário para Selecionar Aluno -->
    <form action="inscricao.php" method="POST">
        <input type="hidden" name="atividade_id" value="<?= htmlspecialchars($idAtividade) ?>">

        <div class="form-group">
            <label for="participante_id">Selecione o Participante Credenciado *</label>
            <select name="participante_id" id="participante_id" class="form-control" required>
                <option value="">-- Escolha um aluno/participante credenciado --</option>
                <?php foreach ($participantes as $p): ?>
                    <option value="<?= htmlspecialchars((string)$p['_id']) ?>">
                        <?= htmlspecialchars($p['nome']) ?> (<?= htmlspecialchars($p['tipo']) ?> — Crachá: <?= htmlspecialchars((string)$p['_id']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.75rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1; padding: 0.85rem;">
                Confirmar Inscrição 🚀
            </button>
            <a href="atividades.php" class="btn btn-secondary" style="padding: 0.85rem 1.25rem;">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
