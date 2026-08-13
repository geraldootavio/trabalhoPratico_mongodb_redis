# 🎓 Trabalho Prático - Banco de Dados 2 (MongoDB e Redis)
**Instituto Federal de Minas Gerais (IFMG) - Campus Ouro Preto**  
**Curso:** Análise e Desenvolvimento de Sistemas (ADS)  
**Disciplina:** Banco de Dados 2  
**Cenário Escolhido:** Evento Acadêmico - Congresso de Análise e Desenvolvimento de Sistemas (CONADS 2026)
**Alunos:** Alessandro Oliveira de Jesus; Geraldo Otávio Figueiredo Pereira
**Professor:** João Paulo Fernandes de Cerqueira César


---

## 📌 1. Visão Geral do Projeto

Este projeto consiste em uma solução prática integrada de bancos de dados NoSQL utilizando **MongoDB** (para persistência de dados permanentes e documentos estruturados) e **Redis** (para dados temporários de alta velocidade, cache com expiração TTL, ranking de popularidade e fila de espera).

Além dos scripts de bancos de dados exigidos no trabalho principal, o projeto inclui um **protótipo web funcional completo em PHP** (Desafio Opcional valendo até 8 pontos extras).

---

## 👥 2. Integrantes do Grupo e Divisão de Tarefas

- **Estudante 1: Geraldo** Modelagem NoSQL no MongoDB, elaboração de documentos embutidos/referências, criação dos scripts de carga (`01_dados.js`) e índice. Integração dos drivers MongoDB/Redis. Relatório final.
- **Estudante 2: Alessandro** Definição da arquitetura no Redis, padrões de chaves, comandos CLI (`comandos_redis.txt`), fluxos de Cache (TTL) e Ranking. Desenvolvimento da aplicação Web em PHP, integração dos drivers MongoDB/Redis, interface visual.

---

## 🛠️ 3. Ferramentas e Tecnologias Utilizadas

- **MongoDB & mongosh:** Banco NoSQL orientado a documentos (Local ou MongoDB Atlas Cloud).
- **Redis & redis-cli:** Banco NoSQL chave-valor em memória (Local ou Redis Cloud).
- **PHP 8.x (XAMPP):** Linguagem para o protótipo web com extensoes `zip` e `mongodb`.
- **Composer:** Gerenciador de dependências PHP (`mongodb/mongodb` e `predis/predis`).

---

## 📁 4. Estrutura dos Arquivos do Repositório

```text
grupo_01_trabalho_bd2/
├── README.md                           # Guia completo de instalação e execução
├── RELATORIO.md                        # Relatório técnico completo (Pronto para exportar PDF)
├── docker-compose.yml                  # Docker para subir MongoDB e Redis localmente
├── mongodb/
│   ├── 01_dados.js                     # Script de criação e carga inicial (+20 documentos)
│   └── 02_operacoes.js                 # CRUD, 4+ consultas, Aggregation Pipeline e Índice
├── redis/
│   └── comandos_redis.txt              # Comandos Redis (String TTL, Hash, ZSET e List)
└── php/
    ├── composer.phar                   # Gerenciador de dependências Composer executável
    ├── config/
    │   ├── database.php                # Conexão transparente com MongoDB e Redis
    │   ├── env_config.json             # Configuração de URIs (MongoDB Atlas Cloud & Redis Cloud)
    │   └── init_data.php               # Script auxiliar em PHP para inicializar dados automaticamente
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

### Passo 1: Subir os Bancos de Dados (Duas Opções)

#### Opção A: MongoDB Atlas + Redis Cloud (Já pré-configurado no `env_config.json`)
A aplicação se conecta diretamente à nuvem sem precisar instalar contêineres locais.

#### Opção B: Docker Local
Abra o Docker Desktop e rode na raiz do projeto:
```bash
docker compose up -d
```

---

### Passo 2: Executar a Aplicação Web em PHP
1. No terminal do PowerShell, entre na pasta `php/public`:
```powershell
cd php/public
```

2. Inicie o servidor embutido do PHP (utilizando a instalação do XAMPP):
```powershell
C:\xampp\php\php.exe -S localhost:8000
```
> **Nota:** Se o parâmetro `php` já estiver configurado no PATH do seu sistema, você pode rodar simplesmente `php -S localhost:8000`.

3. Acesse no navegador: **`http://localhost:8000`**

---

### Passo 3: Executar Scripts CLI (Mongosh / Redis CLI) - Opcional

* **MongoDB (`mongosh`):**
  ```bash
  mongosh < mongodb/01_dados.js
  mongosh < mongodb/02_operacoes.js
  ```

* **Redis (`redis-cli`):**
  ```bash
  redis-cli < redis/comandos_redis.txt
  ```

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
