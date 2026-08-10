<?php
/**
 * ==============================================================================
 * TRABALHO PRÁTICO - BANCO DE DADOS 2 (IFMG CAMPUS OURO PRETO)
 * CURSO: Análise e Desenvolvimento de Sistemas (ADS)
 * CENÁRIO: Congresso Acadêmico de ADS (CONADS 2026)
 * ARQUIVO: php/public/participante_cadastrar.php
 * OBJETIVO: Formulário de credenciamento oficial de participantes no congresso.
 * ==============================================================================
 */

require_once __DIR__ . '/../config/database.php';

$mongoDb = Database::getMongoDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $matricula = trim($_POST['matricula'] ?? '');
    $tipo = trim($_POST['tipo'] ?? 'Estudante ADS');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $cidade = trim($_POST['cidade'] ?? 'Ouro Preto');

    if (empty($nome) || empty($email) || empty($matricula)) {
        $_SESSION['mensagem_erro'] = "Preencha todos os campos obrigatórios!";
    } elseif (!$mongoDb) {
        $_SESSION['mensagem_erro'] = "Não foi possível conectar ao sistema para gravar o cadastro.";
    } else {
        try {
            // Gera um ID de credencial no formato part_xxx
            $idNovo = 'part_' . sprintf('%03d', rand(100, 999));

            $resultado = $mongoDb->participantes->insertOne([
                '_id' => $idNovo,
                'nome' => $nome,
                'matricula' => $matricula,
                'tipo' => $tipo,
                'instituicao' => 'IFMG Campus Ouro Preto',
                'contato' => [
                    'email' => $email,
                    'telefone' => $telefone
                ],
                'endereco' => [
                    'cidade' => $cidade,
                    'estado' => 'MG'
                ],
                'data_cadastro' => new \MongoDB\BSON\UTCDateTime(),
                'ativo' => true
            ]);

            $_SESSION['mensagem_sucesso'] = "Participante '{$nome}' credenciado com sucesso no CONADS 2026! Código do Crachá: {$idNovo}";
            header("Location: participante_cadastrar.php");
            exit;
        } catch (\Exception $e) {
            $_SESSION['mensagem_erro'] = "Erro ao efetuar credenciamento: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';

// Lista os últimos participantes cadastrados para visualização
$participantesCadastrados = [];
if ($mongoDb) {
    try {
        $cursor = $mongoDb->participantes->find([], ['limit' => 15, 'sort' => ['_id' => -1]]);
        $participantesCadastrados = iterator_to_array($cursor);
    } catch (\Exception $e) {}
}
?>

<div class="section-header">
    <div>
        <h1 class="section-title">Credenciamento de Participantes</h1>
        <p class="section-subtitle">
            Cadastre-se para gerar seu crachá e habilitar a inscrição nas oficinas e minicursos laboratoriais do <strong>CONADS 2026</strong>.
        </p>
    </div>
</div>

<div class="grid-2">
    <!-- Formulário de Cadastro -->
    <div class="card">
        <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 1.25rem;">
            <div style="width: 38px; height: 38px; background: #EEF2FF; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">📝</div>
            <div>
                <h2 style="font-size: 1.2rem; font-weight: 700; color: var(--brand-navy);">Ficha de Cadastro do Evento</h2>
                <span style="font-size: 0.78rem; color: var(--text-muted);">Preencha seus dados para receber o crachá digital</span>
            </div>
        </div>

        <form action="participante_cadastrar.php" method="POST">
            <div class="form-group">
                <label for="nome">Nome Completo *</label>
                <input type="text" id="nome" name="nome" class="form-control" placeholder="Ex: Ana Clara Silva" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="matricula">Matrícula / RA *</label>
                    <input type="text" id="matricula" name="matricula" class="form-control" placeholder="Ex: 202600123" required>
                </div>

                <div class="form-group">
                    <label for="tipo">Categoria *</label>
                    <select id="tipo" name="tipo" class="form-control">
                        <option value="Estudante ADS">Estudante ADS</option>
                        <option value="Estudante Ciência da Computação">Estudante Ciência da Computação</option>
                        <option value="Professor / Pesquisador">Professor / Pesquisador</option>
                        <option value="Desenvolvedor de Software">Desenvolvedor de Software</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="email">E-mail para Contato *</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="ana.silva@aluno.ifmg.edu.br" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="telefone">Telefone Celular</label>
                    <input type="text" id="telefone" name="telefone" class="form-control" placeholder="(31) 98888-1111">
                </div>

                <div class="form-group">
                    <label for="cidade">Cidade de Origem</label>
                    <input type="text" id="cidade" name="cidade" class="form-control" value="Ouro Preto">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; margin-top: 0.5rem;">
                ✍️ Concluir Credenciamento
            </button>
        </form>
    </div>

    <!-- Lista de Participantes Cadastrados -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 38px; height: 38px; background: #ECFDF5; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">👥</div>
                <div>
                    <h2 style="font-size: 1.2rem; font-weight: 700; color: var(--brand-navy);">Participantes Credenciados</h2>
                    <span style="font-size: 0.78rem; color: var(--text-muted);">Lista de credenciais geradas recentemente</span>
                </div>
            </div>
        </div>

        <?php if (empty($participantesCadastrados)): ?>
            <div style="text-align: center; padding: 3rem 1rem;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📭</div>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Nenhum participante credenciado até o momento.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Participante</th>
                            <th>Categoria</th>
                            <th>Contato / Cidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($participantesCadastrados as $p): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--brand-navy);"><?= htmlspecialchars($p['nome']) ?></strong><br>
                                    <span style="font-size: 0.75rem; background: #F1F5F9; color: #475569; padding: 0.15rem 0.45rem; border-radius: 4px; font-weight: 600; font-family: monospace;">
                                        Crachá: <?= htmlspecialchars((string)$p['_id']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size: 0.78rem; background: #F0F9FF; color: #0369A1; border: 1px solid #BAE6FD; padding: 0.25rem 0.55rem; border-radius: var(--radius-sm); font-weight: 600;">
                                        <?= htmlspecialchars($p['tipo'] ?? 'Aluno') ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem; color: var(--text-main);"><?= htmlspecialchars($p['contato']['email'] ?? 'Sem e-mail') ?></div>
                                    <div style="font-size: 0.78rem; color: var(--text-muted);"><?= htmlspecialchars($p['endereco']['cidade'] ?? '') ?> - MG</div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
