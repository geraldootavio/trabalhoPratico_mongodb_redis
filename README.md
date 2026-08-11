# 🎓 Trabalho Prático - Banco de Dados 2 (MongoDB e Redis)
**Instituto Federal de Minas Gerais (IFMG) - Campus Ouro Preto**  
**Curso:** Análise e Desenvolvimento de Sistemas (ADS)  
**Disciplina:** Banco de Dados 2  
**Cenário Escolhido:** Evento Acadêmico - Congresso de Análise e Desenvolvimento de Sistemas (CONADS 2026)

---

## 📌 1. Visão Geral do Projeto

Este projeto consiste em uma solução prática integrada de bancos de dados NoSQL utilizando **MongoDB** (para persistência de dados permanentes e documentos estruturados) e **Redis** (para dados temporários de alta velocidade, cache com expiração TTL, ranking de popularidade e fila de espera).

Além dos scripts de bancos de dados exigidos no trabalho principal, o projeto inclui um **protótipo web funcional completo em PHP** (Desafio Opcional valendo até 8 pontos extras).

---

## 👥 2. Integrantes do Grupo e Divisão de Tarefas

- **Estudante 1:** Modelagem NoSQL no MongoDB, elaboração de documentos embutidos/referências, criação dos scripts de carga (`01_dados.js`) e índice.
- **Estudante 2:** Definição da arquitetura no Redis, padrões de chaves, comandos CLI (`comandos_redis.txt`), fluxos de Cache (TTL) e Ranking.
- **Estudante 3:** Desenvolvimento da aplicação Web em PHP, integração dos drivers MongoDB/Redis, interface visual e relatório final.

---

## 🛠️ 3. Ferramentas e Tecnologias Utilizadas

- **MongoDB & mongosh:** Banco NoSQL orientado a documentos (Porta 27017).
- **Redis & redis-cli:** Banco NoSQL chave-valor em memória (Porta 6379).
- **PHP 7.4+ ou 8.x:** Linguagem para o protótipo web.
- **Composer (Opcional):** Gerenciador de dependências PHP (pacotes `mongodb/mongodb` e `predis/predis`).

---

## 📁 4. Estrutura dos Arquivos do Repositório

```text
grupo_01_trabalho_bd2/
├── README.md                           # Guia completo de instalação e execução
├── RELATORIO.md                        # Relatório técnico completo (Pronto para exportar PDF)
├── mongodb/
│   ├── 01_dados.js                     # Script de criação e carga inicial (+20 documentos)
│   └── 02_operacoes.js                 # CRUD, 4+ consultas, Aggregation Pipeline e Índice
├── redis/
│   └── comandos_redis.txt              # Comandos Redis (String TTL, Hash, ZSET e List)
└── php/
    ├── config/
    │   ├── database.php                # Conexão transparente com MongoDB e Redis
    │   └── init_data.php               # Script auxiliar em PHP para inicializar dados via web
    ├── includes/
    │   ├── header.php                  # Barra de navegação e layout CSS
    │   └── footer.php                  # Rodapé padrão
    ├── public/
    │   ├── index.php                   # Dashboard principal da aplicação
    │   ├── atividades.php              # Listagem + Demonstração visual de Cache Miss e Cache Hit
    │   ├── participante_cadastrar.php  # Formulário de Cadastro (MongoDB)
    │   ├── inscricao.php               # Inscrição integrada (Mongo + Invalidação + Ranking + Fila)
    │   └── ranking.php                 # Exibição do Ranking (ZSET) e Filas de Espera (List)
    └── composer.json                   # Dependências do PHP
```

---

## 🗄️ 5. Nomes do Banco, Coleções e Padrão de Chaves

### MongoDB (`congresso_ads`):
- `eventos`: Cadastro do Congresso/Semana ADS.
- `participantes`: Dados dos alunos e professores (Contatos e endereço **embutidos**).
- `atividades`: Palestras, Oficinas, Rodas de Conversa e **Cross-Fires** entre desenvolvedores e ferramentas (Docker vs Podman, React vs Vue, etc.).
- `inscricoes`: Relação entre participante e atividade (Documentos com **referências**).

### Redis (Padrão de Nomenclatura):
- `cache:vagas:atividade:<id>` -> **String com TTL** (Cache de vagas com expiração de 60s).
- `resumo:atividade:<id>` -> **Hash** (Resumo rápido de entidade em memória).
- `ranking:atividades:populares` -> **Sorted Set (ZSET)** (Ranking de popularidade por pontuação).
- `fila:espera:atividade:<id>` -> **List** (Fila FIFO de espera quando as vagas esgotam).
- `sessao:participante:<id>` -> **String com TTL** (Controle de sessão de usuários).

---

## 🚀 6. Ordem de Execução e Passo a Passo

### Passo 1: Executar o MongoDB (`mongosh`)
Abra o terminal e execute os scripts de carga e operações:
```bash
# 1. Carregar os dados iniciais (+20 documentos)
mongosh < mongodb/01_dados.js

# 2. Executar as consultas, CRUD, Aggregation Pipeline e criar o Índice
mongosh < mongodb/02_operacoes.js
```

### Passo 2: Executar o Redis (`redis-cli`)
Abra o terminal do Redis para testar os comandos e fluxos de cache:
```bash
redis-cli < redis/comandos_redis.txt
```

### Passo 3: Executar a Aplicação Web em PHP (Desafio Opcional)
Se você possui o PHP instalado localmente, inicie o servidor embutido na pasta `php/public`:
```bash
cd php/public
php -S localhost:8000
```
Acesse no navegador: `http://localhost:8000`

> **Dica:** Se preferir popular o banco direto pela Web, acesse `http://localhost:8000/../config/init_data.php`.

---

## 📊 7. Como Reproduzir a Demonstração da Apresentação (20 a 30 Minutos)

1. **Apresentar o Cenário:** Explicar o Congresso ADS, mostrando a diferença de papéis entre MongoDB (permanente) e Redis (temporário/rápido).
2. **Demonstração do MongoDB:**
   - Mostrar a coleção `atividades` e a modelagem com documentos embutidos (palestrantes/ferramentas) no Compass ou mongosh.
   - Executar a consulta com filtro e o **Aggregation Pipeline** em `mongodb/02_operacoes.js`.
   - Mostrar a criação do índice `idx_tipo_vagas`.
3. **Demonstração do Redis:**
   - Mostrar no terminal ou na tela PHP o fluxo de **Cache Miss** (amarelo) -> Busca Mongo -> Gravação no Redis -> **Cache Hit** (verde) com contagem regressiva do **TTL**.
   - Mostrar a inserção de um aluno na **Fila de Espera FIFO** (List) de uma oficina esgotada.
   - Exibir o **Ranking de Popularidade** (Sorted Set) sendo incrementado a cada ação.
