<?php
/**
 * ==============================================================================
 * TRABALHO PRÁTICO - BANCO DE DADOS 2 (IFMG CAMPUS OURO PRETO)
 * CURSO: Análise e Desenvolvimento de Sistemas (ADS)
 * CENÁRIO: Congresso Acadêmico de ADS (CONADS 2026)
 * ARQUIVO: php/config/init_data.php
 * OBJETIVO: Script auxiliar em PHP para inicializar o MongoDB e Redis diretamente 
 * pela web (útil se o aluno/professor executar a aplicação pelo navegador).
 * ==============================================================================
 */

require_once __DIR__ . '/database.php';

$mongo = Database::getMongoDb();
$redis = Database::getRedis();

if (!$mongo) {
    die("⚠️ Erro: MongoDB não está acessível ou extensão 'mongodb' não encontrada.");
}

// 1. Limpa coleções
$mongo->eventos->drop();
$mongo->participantes->drop();
$mongo->atividades->drop();
$mongo->inscricoes->drop();

// 2. Insere evento
$mongo->eventos->insertOne([
    '_id' => 'evt_conads_2026',
    'nome' => 'I Congresso Nacional de Análise e Desenvolvimento de Sistemas - CONADS',
    'edicao' => 1,
    'ano' => 2026,
    'tema_principal' => 'Inovação, Arquitetura NoSQL e Ferramentas Modernas de Software',
    'local' => 'IFMG Campus Ouro Preto - Auditório Principal e Laboratórios',
    'status' => 'ativo'
]);

// 3. Insere participantes
$mongo->participantes->insertMany([
    [
        '_id' => 'part_001',
        'nome' => 'Ana Silva',
        'matricula' => '20240101',
        'tipo' => 'Estudante ADS',
        'instituicao' => 'IFMG Campus Ouro Preto',
        'contato' => ['email' => 'ana.silva@aluno.ifmg.edu.br', 'telefone' => '(31) 98888-1111'],
        'endereco' => ['cidade' => 'Ouro Preto', 'estado' => 'MG'],
        'ativo' => true
    ],
    [
        '_id' => 'part_002',
        'nome' => 'Carlos Eduardo Santos',
        'matricula' => '20240102',
        'tipo' => 'Estudante ADS',
        'instituicao' => 'IFMG Campus Ouro Preto',
        'contato' => ['email' => 'carlos.santos@aluno.ifmg.edu.br', 'telefone' => '(31) 98888-2222'],
        'endereco' => ['cidade' => 'Mariana', 'estado' => 'MG'],
        'ativo' => true
    ],
    [
        '_id' => 'part_003',
        'nome' => 'Prof. Dr. Ricardo Oliveira',
        'matricula' => 'SERV9901',
        'tipo' => 'Professor / Pesquisador',
        'instituicao' => 'IFMG Campus Ouro Preto',
        'contato' => ['email' => 'ricardo.oliveira@ifmg.edu.br', 'telefone' => '(31) 98888-3333'],
        'endereco' => ['cidade' => 'Ouro Preto', 'estado' => 'MG'],
        'ativo' => true
    ],
    [
        '_id' => 'part_004',
        'nome' => 'Beatriz Lima',
        'matricula' => '20240210',
        'tipo' => 'Estudante ADS',
        'instituicao' => 'UFOP',
        'contato' => ['email' => 'beatriz.lima@aluno.ufop.br', 'telefone' => '(31) 98888-4444'],
        'endereco' => ['cidade' => 'Ouro Preto', 'estado' => 'MG'],
        'ativo' => true
    ]
]);

// 4. Insere atividades (Cross-Fire, Oficinas, Palestras)
$mongo->atividades->insertMany([
    [
        '_id' => 'ativ_101',
        'evento_id' => 'evt_conads_2026',
        'titulo' => 'Cross-Fire: Docker vs Podman - Qual containerizador utilizar?',
        'tipo' => 'Cross-fire',
        'descricao' => 'Debate e comparação prática entre desenvolvedores sobre contêineres e infraestrutura.',
        'palestrante' => ['nome' => 'Eng. Gabriel Torres', 'empresa' => 'CloudMinas'],
        'ferramentas_comparadas' => ['Docker', 'Podman'],
        'vagas_totais' => 30,
        'vagas_disponiveis' => 15,
        'local' => 'Auditório Master - Bloco A',
        'status' => 'ativa'
    ],
    [
        '_id' => 'ativ_102',
        'evento_id' => 'evt_conads_2026',
        'titulo' => 'Cross-Fire: React vs Vue vs Angular - A Batalha Front-End',
        'tipo' => 'Cross-fire',
        'descricao' => 'Desenvolvedores sêniores defendem a arquitetura de seus frameworks web preferidos.',
        'palestrante' => ['nome' => 'Camila Fernandes & Dev Team', 'empresa' => 'WebStudio'],
        'ferramentas_comparadas' => ['React', 'Vue.js', 'Angular'],
        'vagas_totais' => 40,
        'vagas_disponiveis' => 5,
        'local' => 'Auditório Master - Bloco A',
        'status' => 'ativa'
    ],
    [
        '_id' => 'ativ_103',
        'evento_id' => 'evt_conads_2026',
        'titulo' => 'Oficina Prática de MongoDB para Alunos de ADS',
        'tipo' => 'Oficina',
        'descricao' => 'Hands-on sobre modelagem orientada a documentos, consultas e agregações.',
        'palestrante' => ['nome' => 'Prof. Dr. Ricardo Oliveira', 'empresa' => 'IFMG Ouro Preto'],
        'requisitos' => ['Notebook próprio'],
        'vagas_totais' => 25,
        'vagas_disponiveis' => 2,
        'local' => 'Laboratório 03',
        'status' => 'ativa'
    ],
    [
        '_id' => 'ativ_104',
        'evento_id' => 'evt_conads_2026',
        'titulo' => 'Oficina de Caching de Alta Performance com Redis',
        'tipo' => 'Oficina',
        'descricao' => 'Aprenda a utilizar Hashes, Sorted Sets, Expiração TTL e Filas no Redis com PHP.',
        'palestrante' => ['nome' => 'Juliana Rocha', 'empresa' => 'DataCorp Soluções'],
        'requisitos' => ['Lógica e PHP básico'],
        'vagas_totais' => 20,
        'vagas_disponiveis' => 0, // ESGOTADA
        'local' => 'Laboratório 01',
        'status' => 'esgotada'
    ]
]);

// 5. Se o Redis estiver ativo, inicializa estruturas
if ($redis) {
    // String com TTL
    $redis->setex('cache:vagas:atividade:101', 60, "15");
    
    // Hash
    $redis->hset('resumo:atividade:101', 'titulo', 'Cross-Fire: Docker vs Podman');
    $redis->hset('resumo:atividade:101', 'tipo', 'Cross-fire');
    $redis->hset('resumo:atividade:101', 'palestrante', 'Eng. Gabriel Torres');

    // Sorted Set (Ranking)
    $redis->zadd('ranking:atividades:populares', [
        'ativ_104 - Oficina de Caching com Redis' => 310,
        'ativ_103 - Oficina Prática de MongoDB' => 250,
        'ativ_102 - Cross-Fire React vs Vue' => 195,
        'ativ_101 - Cross-Fire Docker vs Podman' => 120
    ]);

    // List (Fila de espera)
    $redis->rpush('fila:espera:atividade:104', 'part_002 - Carlos Santos');
    $redis->rpush('fila:espera:atividade:104', 'part_004 - Beatriz Lima');
}

echo "✅ Dados iniciais populados com sucesso no MongoDB e Redis!";
