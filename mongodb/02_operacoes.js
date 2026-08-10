/**
 * ==============================================================================
 * TRABALHO PRÁTICO - BANCO DE DADOS 2 (IFMG CAMPUS OURO PRETO)
 * CURSO: Análise e Desenvolvimento de Sistemas (ADS)
 * CENÁRIO: Congresso Acadêmico de ADS (CONADS 2026)
 * ARQUIVO: mongodb/02_operacoes.js
 * OBJETIVO: Operações CRUD, Consultas com Filtros, Aggregation Pipeline e Índices
 * ==============================================================================
 */

use congresso_ads;

print("\n======================================================================");
print("1. OPERAÇÕES CRUD BÁSICAS (INSERÇÃO, CONSULTA, ATUALIZAÇÃO, DESATIVAÇÃO)");
print("======================================================================");

// 1.1 Inserção (Create)
print("\n--- [1.1 Inserção] Novo participante cadastrado no sistema ---");
db.participantes.insertOne({
  _id: "part_009",
  nome: "Gabriel Matos",
  matricula: "20240999",
  tipo: "Estudante ADS",
  instituicao: "IFMG Campus Ouro Preto",
  contato: {
    email: "gabriel.matos@aluno.ifmg.edu.br",
    telefone: "(31) 98888-9999"
  },
  endereco: {
    cidade: "Ouro Preto",
    estado: "MG"
  },
  data_cadastro: new Date(),
  ativo: true
});
print("Novo participante registrado com _id 'part_009'.");

// 1.2 Consulta (Read)
print("\n--- [1.2 Consulta] Buscar dados do participante recém inserido ---");
var partBuscado = db.participantes.findOne({ _id: "part_009" });
printjson(partBuscado);

// 1.3 Atualização (Update)
print("\n--- [1.3 Atualização] Reduzir 1 vaga disponível da oficina de Redis ---");
db.atividades.updateOne(
  { _id: "ativ_103" },
  { $inc: { vagas_disponiveis: -1 } }
);
print("Vagas da atividade 'ativ_103' decrementadas via $inc.");

// 1.4 Desativação Lógica / Remocao (Delete/Soft Delete)
print("\n--- [1.4 Desativação Lógica] Cancelar inscrição insc_006 (Boas Práticas: Desativação Lógica) ---");
db.inscricoes.updateOne(
  { _id: "insc_006" },
  { 
    $set: { 
      status: "cancelada", 
      data_cancelamento: new Date() 
    } 
  }
);
print("Inscrição 'insc_006' marcada como 'cancelada'.");


print("\n======================================================================");
print("2. CONSULTAS COM FILTROS E OPERADORES VARIADOS (PELO MENOS 4 CONSULTAS)");
print("======================================================================");

// Consulta 1: Filtro com operador $gte (Maior ou igual)
// Objetivo: Encontrar atividades que ainda possuem pelo menos 15 vagas disponíveis.
print("\n--- [Consulta 1 - Operador $gte] Atividades com 15 ou mais vagas disponíveis ---");
var consulta1 = db.atividades.find({
  vagas_disponiveis: { $gte: 15 }
}, { titulo: 1, tipo: 1, vagas_disponiveis: 1, local: 1 }).toArray();
printjson(consulta1);

// Consulta 2: Filtro com operador $in (Contido na lista)
// Objetivo: Listar atividades do tipo 'Cross-fire' ou 'Oficina'.
print("\n--- [Consulta 2 - Operador $in] Atividades do tipo 'Cross-fire' ou 'Oficina' ---");
var consulta2 = db.atividades.find({
  tipo: { $in: ["Cross-fire", "Oficina"] }
}, { titulo: 1, tipo: 1, palestrante: 1 }).toArray();
printjson(consulta2);

// Consulta 3: Filtro com operador $regex (Busca por expressão regular / texto parcial)
// Objetivo: Buscar participantes cujo e-mail seja do domínio do IFMG (@aluno.ifmg.edu.br ou @ifmg.edu.br).
print("\n--- [Consulta 3 - Operador $regex] Participantes com e-mail do IFMG ---");
var consulta3 = db.participantes.find({
  "contato.email": { $regex: /@.*ifmg\.edu\.br$/, $options: "i" }
}, { nome: 1, tipo: 1, "contato.email": 1 }).toArray();
printjson(consulta3);

// Consulta 4: Filtro em campos de Documentos Embutidos / Arrays ($elemMatch ou array direto)
// Objetivo: Encontrar Cross-fires que comparam especificamente a ferramenta 'Docker'.
print("\n--- [Consulta 4 - Filtro em Array Embutido] Cross-fires que envolvem 'Docker' ---");
var consulta4 = db.atividades.find({
  tipo: "Cross-fire",
  ferramentas_comparadas: "Docker"
}, { titulo: 1, ferramentas_comparadas: 1, palestrante: 1 }).toArray();
printjson(consulta4);


print("\n======================================================================");
print("3. AGGREGATION PIPELINE (COM 3 ESTÁGIOS COERENTES COM O CENÁRIO)");
print("======================================================================");

/**
 * AGGREGATION PIPELINE EXPLICADA:
 * Estágio 1: $match -> Filtra apenas as atividades ativas no evento.
 * Estágio 2: $group -> Agrupa por 'tipo' de atividade (Palestra, Oficina, Cross-fire)
 *                      e calcula a soma total de vagas oferecidas e média de vagas livres.
 * Estágio 3: $sort  -> Ordena os grupos em ordem decrescente do total de vagas oferecidas.
 */
print("\n--- Pipeline de Agregação: Estatísticas de Vagas por Tipo de Atividade ---");
var pipelineEstatisticas = [
  // Estágio 1: $match - Filtrar atividades ativas
  { 
    $match: { 
      status: { $ne: "cancelada" } 
    } 
  },
  // Estágio 2: $group - Agrupar por tipo de atividade e calcular métricas
  { 
    $group: { 
      _id: "$tipo",
      total_atividades: { $sum: 1 },
      total_vagas_oferecidas: { $sum: "$vagas_totais" },
      total_vagas_livres: { $sum: "$vagas_disponiveis" }
    } 
  },
  // Estágio 3: $sort - Ordenar pelo total de vagas oferecidas (maior para o menor)
  { 
    $sort: { 
      total_vagas_oferecidas: -1 
    } 
  }
];

var resultadoAgregacao = db.atividades.aggregate(pipelineEstatisticas).toArray();
printjson(resultadoAgregacao);


print("\n======================================================================");
print("4. CRIAÇÃO E JUSTIFICATIVA DE ÍNDICES NO MONGODB");
print("======================================================================");

/**
 * JUSTIFICATIVA DO ÍNDICE:
 * No sistema do congresso, a consulta por tipo de atividade combinada com a disponibilidade
 * de vagas (`tipo` e `vagas_disponiveis`) é executada constantemente pela página inicial e 
 * pela busca de alunos na aplicação web.
 *
 * A criação do índice composto `{ tipo: 1, vagas_disponiveis: -1 }` permite que o MongoDB
 * encontre instantaneamente atividades de determinado tipo ordenadas por vagas sem precisar
 * realizar um scan completo na coleção (COLLSCAN -> IXSCAN).
 */

print("\n--- Criando índice composto na coleção 'atividades' ---");
db.atividades.createIndex(
  { tipo: 1, vagas_disponiveis: -1 },
  { name: "idx_tipo_vagas" }
);
print("Índice 'idx_tipo_vagas' criado com sucesso!");

print("\n--- Verificando os índices existentes na coleção 'atividades' ---");
var indicesExistentes = db.atividades.getIndexes();
printjson(indicesExistentes);

print("\n======================================================================");
print("FIM DO SCRIPT DE OPERAÇÕES MONGODB");
print("======================================================================\n");
