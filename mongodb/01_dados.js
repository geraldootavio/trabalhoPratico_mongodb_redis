/**
 * ==============================================================================
 * TRABALHO PRÁTICO - BANCO DE DADOS 2 (IFMG CAMPUS OURO PRETO)
 * CURSO: Análise e Desenvolvimento de Sistemas (ADS)
 * CENÁRIO: Congresso Acadêmico de ADS (CONADS 2026)
 * ARQUIVO: mongodb/01_dados.js
 * OBJETIVO: Inserção de dados iniciais no MongoDB (mais de 20 documentos).
 * ==============================================================================
 */

// Seleciona ou cria o banco de dados 'congresso_ads'
use congresso_ads;

// Limpa as coleções antes de popular (para evitar duplicados em re-execuções)
db.eventos.drop();
db.participantes.drop();
db.atividades.drop();
db.inscricoes.drop();

print("--- Iniciando inserção dos dados no MongoDB ---");

// ==============================================================================
// 1. COLEÇÃO: eventos
// Armazena as informações gerais do congresso acadêmico.
// ==============================================================================
db.eventos.insertOne({
  _id: "evt_conads_2026",
  nome: "I Congresso Nacional de Análise e Desenvolvimento de Sistemas - CONADS",
  edicao: 1,
  ano: 2026,
  tema_principal: "Inovação, Arquitetura NoSQL e Ferramentas Modernas de Software",
  local: "IFMG Campus Ouro Preto - Auditório Principal e Laboratórios",
  data_inicio: ISODate("2026-08-10T08:00:00Z"),
  data_fim: ISODate("2026-08-14T18:00:00Z"),
  status: "ativo"
});

print("1. Coleção 'eventos' populada com sucesso!");

// ==============================================================================
// 2. COLEÇÃO: participantes
// Estratégia de modelagem: DOCUMENTO EMBUTIDO para 'contato' e 'endereco'
// Justificativa: Contato e endereço são dados de cardinalidade fixa que pertencem 
// exclusivamente ao participante, reduzindo a necessidade de joins (joins em NoSQL).
// ==============================================================================
db.participantes.insertMany([
  {
    _id: "part_001",
    nome: "Ana Silva",
    matricula: "20240101",
    tipo: "Estudante ADS",
    instituicao: "IFMG Campus Ouro Preto",
    contato: {
      email: "ana.silva@aluno.ifmg.edu.br",
      telefone: "(31) 98888-1111"
    },
    endereco: {
      cidade: "Ouro Preto",
      estado: "MG"
    },
    data_cadastro: ISODate("2026-07-01T10:00:00Z"),
    ativo: true
  },
  {
    _id: "part_002",
    nome: "Carlos Eduardo Santos",
    matricula: "20240102",
    tipo: "Estudante ADS",
    instituicao: "IFMG Campus Ouro Preto",
    contato: {
      email: "carlos.santos@aluno.ifmg.edu.br",
      telefone: "(31) 98888-2222"
    },
    endereco: {
      cidade: "Mariana",
      estado: "MG"
    },
    data_cadastro: ISODate("2026-07-02T14:30:00Z"),
    ativo: true
  },
  {
    _id: "part_003",
    nome: "Prof. Dr. Ricardo Oliveira",
    matricula: "SERV9901",
    tipo: "Professor / Pesquisador",
    instituicao: "IFMG Campus Ouro Preto",
    contato: {
      email: "ricardo.oliveira@ifmg.edu.br",
      telefone: "(31) 98888-3333"
    },
    endereco: {
      cidade: "Ouro Preto",
      estado: "MG"
    },
    data_cadastro: ISODate("2026-06-15T09:00:00Z"),
    ativo: true
  },
  {
    _id: "part_004",
    nome: "Beatriz Lima",
    matricula: "20240210",
    tipo: "Estudante ADS",
    instituicao: "UFOP",
    contato: {
      email: "beatriz.lima@aluno.ufop.br",
      telefone: "(31) 98888-4444"
    },
    endereco: {
      cidade: "Ouro Preto",
      estado: "MG"
    },
    data_cadastro: ISODate("2026-07-05T11:20:00Z"),
    ativo: true
  },
  {
    _id: "part_005",
    nome: "Fernando Souza",
    matricula: "DEV_EXT_55",
    tipo: "Desenvolvedor de Software",
    instituicao: "Tech Minas LTDA",
    contato: {
      email: "fernando@techminas.com.br",
      telefone: "(31) 98888-5555"
    },
    endereco: {
      cidade: "Belo Horizonte",
      estado: "MG"
    },
    data_cadastro: ISODate("2026-07-10T16:00:00Z"),
    ativo: true
  },
  {
    _id: "part_006",
    nome: "Mariana Costa",
    matricula: "20250105",
    tipo: "Estudante ADS",
    instituicao: "IFMG Campus Ouro Preto",
    contato: {
      email: "mariana.costa@aluno.ifmg.edu.br",
      telefone: "(31) 98888-6666"
    },
    endereco: {
      cidade: "Itabirito",
      estado: "MG"
    },
    data_cadastro: ISODate("2026-07-12T08:45:00Z"),
    ativo: true
  },
  {
    _id: "part_007",
    nome: "Lucas Pereira",
    matricula: "20240330",
    tipo: "Estudante Ciência da Computação",
    instituicao: "UFMG",
    contato: {
      email: "lucas.pereira@dcc.ufmg.br",
      telefone: "(31) 98888-7777"
    },
    endereco: {
      cidade: "Belo Horizonte",
      estado: "MG"
    },
    data_cadastro: ISODate("2026-07-15T13:10:00Z"),
    ativo: true
  },
  {
    _id: "part_008",
    nome: "Juliana Rocha",
    matricula: "DEV_EXT_88",
    tipo: "Engenheira de Dados",
    instituicao: "DataCorp Soluções",
    contato: {
      email: "juliana@datacorp.com.br",
      telefone: "(31) 98888-8888"
    },
    endereco: {
      cidade: "Ouro Preto",
      estado: "MG"
    },
    data_cadastro: ISODate("2026-07-20T17:00:00Z"),
    ativo: true
  }
]);

print("2. Coleção 'participantes' populada com 8 documentos!");

// ==============================================================================
// 3. COLEÇÃO: atividades
// Tipos cobertos: Palestra, Oficina, Roda de Conversa, Cross-Fire
// Estratégia de modelagem:
// - REFERÊNCIA para 'evento_id' (para ligar ao evento principal).
// - DOCUMENTO EMBUTIDO para 'palestrante' e 'ferramentas_comparadas' / 'requisitos'.
// ==============================================================================
db.atividades.insertMany([
  {
    _id: "ativ_101",
    evento_id: "evt_conads_2026",
    titulo: "Cross-Fire: Docker vs Podman - Qual containerizador utilizar?",
    tipo: "Cross-fire",
    descricao: "Debate e comparação prática entre desenvolvedores sobre contêineres e infraestrutura moderna.",
    palestrante: {
      nome: "Eng. Gabriel Torres",
      mini_bio: "DevOps Specialist na CloudMinas",
      empresa: "CloudMinas"
    },
    ferramentas_comparadas: ["Docker", "Podman", "Containerd"],
    vagas_totais: 30,
    vagas_disponiveis: 15,
    local: "Auditório Master - Bloco A",
    data_horario: ISODate("2026-08-10T14:00:00Z"),
    duracao_minutos: 120,
    status: "ativa"
  },
  {
    _id: "ativ_102",
    evento_id: "evt_conads_2026",
    titulo: "Cross-Fire: React vs Vue vs Angular - A Batalha dos Frameworks Front-End",
    tipo: "Cross-fire",
    descricao: "Desenvolvedores sêniores defendem e comparam a arquitetura dos frameworks web mais populares.",
    palestrante: {
      nome: "Camila Fernandes & Dev Team",
      mini_bio: "Frontend Lead",
      empresa: "WebStudio"
    },
    ferramentas_comparadas: ["React", "Vue.js", "Angular"],
    vagas_totais: 40,
    vagas_disponiveis: 5,
    local: "Auditório Master - Bloco A",
    data_horario: ISODate("2026-08-11T10:00:00Z"),
    duracao_minutos: 90,
    status: "ativa"
  },
  {
    _id: "ativ_103",
    evento_id: "evt_conads_2026",
    titulo: "Oficina Prática de MongoDB para Alunos de ADS",
    tipo: "Oficina",
    descricao: "Hands-on sobre modelagem orientada a documentos, consultas com operadores e agregações.",
    palestrante: {
      nome: "Prof. Dr. Ricardo Oliveira",
      mini_bio: "Professor de Banco de Dados 2 no IFMG",
      empresa: "IFMG Ouro Preto"
    },
    requisitos: ["Notebook próprio", "MongoDB instalado ou Docker"],
    vagas_totais: 25,
    vagas_disponiveis: 2,
    local: "Laboratório de Informática 03",
    data_horario: ISODate("2026-08-11T14:00:00Z"),
    duracao_minutos: 180,
    status: "ativa"
  },
  {
    _id: "ativ_104",
    evento_id: "evt_conads_2026",
    titulo: "Oficina de Caching de Alta Performance com Redis",
    tipo: "Oficina",
    descricao: "Aprenda a utilizar Hashes, Sorted Sets, Expiração TTL e Filas no Redis com PHP e Python.",
    palestrante: {
      nome: "Juliana Rocha",
      mini_bio: "Engenheira de Dados na DataCorp",
      empresa: "DataCorp Soluções"
    },
    requisitos: ["Conhecimentos básicos em lógica e PHP"],
    vagas_totais: 20,
    vagas_disponiveis: 0, // ESGOTADA para testar fila de espera!
    local: "Laboratório de Informática 01",
    data_horario: ISODate("2026-08-12T09:00:00Z"),
    duracao_minutos: 180,
    status: "esgotada"
  },
  {
    _id: "ativ_105",
    evento_id: "evt_conads_2026",
    titulo: "Palestra: O Futuro da Carreira em Análise e Desenvolvimento de Sistemas",
    tipo: "Palestra",
    descricao: "Visão de mercado, soft skills e habilidades técnicas essenciais para o mercado em 2026.",
    palestrante: {
      nome: "Dra. Patricia Medeiros",
      mini_bio: "Tech Recruiter & CTO",
      empresa: "InovaTech Talent"
    },
    vagas_totais: 100,
    vagas_disponiveis: 45,
    local: "Auditório Central",
    data_horario: ISODate("2026-08-10T09:00:00Z"),
    duracao_minutos: 60,
    status: "ativa"
  },
  {
    _id: "ativ_106",
    evento_id: "evt_conads_2026",
    titulo: "Roda de Conversa: Experiências de Estágio e Primeiro Emprego em Ti",
    tipo: "Roda de conversa",
    descricao: "Bate-papo informal entre egressos do IFMG e alunos atuais sobre entrevistas e desafios iniciais.",
    palestrante: {
      nome: "Grupo de Egressos ADS",
      mini_bio: "Desenvolvedores Júnior e Pleno",
      empresa: "Diversas"
    },
    vagas_totais: 35,
    vagas_disponiveis: 12,
    local: "Espaço Convivência Bloco B",
    data_horario: ISODate("2026-08-12T16:00:00Z"),
    duracao_minutos: 90,
    status: "ativa"
  },
  {
    _id: "ativ_107",
    evento_id: "evt_conads_2026",
    titulo: "Cross-Fire: PostgreSQL vs MongoDB - Relacional ou NoSQL para Novos Projetos?",
    tipo: "Cross-fire",
    descricao: "Discussão comparativa entre arquiteturas relacionais e de documentos em sistemas modernos.",
    palestrante: {
      nome: "Marcos Vinicius & Helena Ramos",
      mini_bio: "DBAs e Arquitetos de Software",
      empresa: "DBExperts"
    },
    ferramentas_comparadas: ["PostgreSQL", "MongoDB", "MySQL"],
    vagas_totais: 50,
    vagas_disponiveis: 22,
    local: "Auditório Master - Bloco A",
    data_horario: ISODate("2026-08-13T10:00:00Z"),
    duracao_minutos: 120,
    status: "ativa"
  },
  {
    _id: "ativ_108",
    evento_id: "evt_conads_2026",
    titulo: "Palestra: Arquitetura de Microserviços e Mensageria",
    tipo: "Palestra",
    descricao: "Como estruturar sistemas escaláveis desacoplados com filas de mensagens.",
    palestrante: {
      nome: "Eng. Roberto Mendes",
      mini_bio: "Arquitetura de Soluções",
      empresa: "Enterprise Systems"
    },
    vagas_totais: 80,
    vagas_disponiveis: 30,
    local: "Auditório Central",
    data_horario: ISODate("2026-08-13T14:00:00Z"),
    duracao_minutos: 90,
    status: "ativa"
  }
]);

print("3. Coleção 'atividades' populada com 8 documentos!");

// ==============================================================================
// 4. COLEÇÃO: inscricoes
// Estratégia de modelagem: REFERÊNCIA aos IDs de participante e atividade.
// Justificativa: Inscrições são uma entidade de associação (M:N) que evolui
// independentemente, registrando histórico, data da inscrição e status.
// ==============================================================================
db.inscricoes.insertMany([
  {
    _id: "insc_001",
    participante_id: "part_001",
    atividade_id: "ativ_101",
    data_inscricao: ISODate("2026-07-20T10:00:00Z"),
    status: "confirmada",
    presenca_confirmada: false
  },
  {
    _id: "insc_002",
    participante_id: "part_001",
    atividade_id: "ativ_103",
    data_inscricao: ISODate("2026-07-20T10:05:00Z"),
    status: "confirmada",
    presenca_confirmada: false
  },
  {
    _id: "insc_003",
    participante_id: "part_002",
    atividade_id: "ativ_101",
    data_inscricao: ISODate("2026-07-21T11:30:00Z"),
    status: "confirmada",
    presenca_confirmada: false
  },
  {
    _id: "insc_004",
    participante_id: "part_002",
    atividade_id: "ativ_104",
    data_inscricao: ISODate("2026-07-21T11:32:00Z"),
    status: "confirmada",
    presenca_confirmada: false
  },
  {
    _id: "insc_005",
    participante_id: "part_004",
    atividade_id: "ativ_102",
    data_inscricao: ISODate("2026-07-22T09:15:00Z"),
    status: "confirmada",
    presenca_confirmada: false
  },
  {
    _id: "insc_006",
    participante_id: "part_006",
    atividade_id: "ativ_105",
    data_inscricao: ISODate("2026-07-23T15:40:00Z"),
    status: "confirmada",
    presenca_confirmada: false
  }
]);

print("4. Coleção 'inscricoes' populada com 6 documentos!");

// ==============================================================================
// RESUMO DE CONTAGEM TOTAL
// ==============================================================================
var totalEventos = db.eventos.countDocuments();
var totalParticipantes = db.participantes.countDocuments();
var totalAtividades = db.atividades.countDocuments();
var totalInscricoes = db.inscricoes.countDocuments();
var totalGeral = totalEventos + totalParticipantes + totalAtividades + totalInscricoes;

print("======================================================================");
print("SUCESSO: Inserção de dados finalizada!");
print("Total de documentos inseridos: " + totalGeral + " (Exigido pelo TP: pelo menos 20)");
print("  - Eventos: " + totalEventos);
print("  - Participantes: " + totalParticipantes);
print("  - Atividades: " + totalAtividades);
print("  - Inscrições: " + totalInscricoes);
print("======================================================================");
