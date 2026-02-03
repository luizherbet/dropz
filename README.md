# Dropz

Aplicação web para registro de clientes e demandas, com gerenciamento do fluxo de execução por meio de um quadro Kanban e geração de relatório mensal por cliente.

---

## Descrição

Dropz é um sistema web desenvolvido com Laravel, Vue e Quasar para organização de demandas de clientes em um fluxo Kanban, simulando o funcionamento de uma software house.

Desafio técnico proposto pela SoftPar.

---

## Dúvidas e Premissas

Dúvidas que surgiram na leitura do desafio e as decisões que adotei.

### 1. Organização do Backlog

**Dúvida:**  
Como organizar o backlog? Por data de adição ou por prioridade?

**Premissa adotada:**  
O backlog será organizado por dois critérios:

1. Prioridade, exibida em três níveis:
   - Alta
   - Normal
   - Baixa

2. Dentro de cada nível de prioridade, as demandas serão ordenadas pela data de criação.

Esse mesmo padrão de ordenação será aplicado às demais colunas do Kanban.


### 2. Critérios de Autorização da Demanda

**Dúvida:**  
De que depende a autorização de uma demanda? Quais os requisitos para que um card possa entrar na fila? É necessário pagamento parcial ou total?
Quais campos devem ser definidos no processo de aprovação para que o fluxo possa avançar?

**Premissa adotada:**  
Para que uma demanda possa ser movida para a coluna **Fila**, os seguintes critérios devem ser atendidos:

- Pagamento parcial mínimo de 50%
- O campo `descricao_detalhada` deve conter mais de 50 caracteres
- Os campos **responsavel**, **setor** e **quem_deve_testar** devem estar definidos

Essas regras simulam critérios mínimos de validação antes do início da execução da demanda.


### 3. Relação entre Tempo Estimado e Tempo Gasto

**Dúvida:**  
Quando o tempo gasto for maior que o tempo estimado, o card deve mudar de prioridade ou apenas sinalizar um alerta visual?

**Premissa adotada:**  
A prioridade da demanda não será alterada automaticamente.  
Quando o tempo gasto ultrapassar o tempo estimado, o card apenas mudará de cor como forma de alerta visual, indicando que a demanda necessita de atenção da equipe.

A definição ou alteração da prioridade permanece como responsabilidade da equipe.


### 4. Significado do Campo `flag_retornou`

**Dúvida:**  
O campo `flag_retornou` indica que a demanda foi reaberta após ter sido concluída?

**Premissa adotada:**  
Sim. O campo `flag_retornou` será utilizado para identificar demandas que retornaram ao fluxo após terem sido marcadas como concluídas.


### 5. Entidade Demandas – Controle de Pagamento

**Dúvida:**  
É interessante adicionar atributos de valor pago e valor total para deixar claro o valor ainda devido, considerando que para autorizar uma demanda é necessário um pagamento mínimo de 50%?

**Premissa adotada:**  
Foram adicionados os atributos `valor_total` e `valor_pago` à entidade de demandas para permitir esse controle.


### 6. Entidade Demandas – Atributo data_cadastro e cliente

**Dúvida:**  
O atributo `data_cadastro` da entidade Demandas fica duplicado ao adicionar `timestamps`, que já geram as colunas `created_at` e `updated_at`.  
O atributo `cliente` (FK) não é descritivo, visto que a relação é estabelecida utilizando o id do cliente.

**Premissa adotada:**  
Removi o atributo `data_cadastro`, pois o campo `created_at` gerado pelos `timestamps` será utilizado como data de cadastro da demanda.  
Renomeei o atributo `cliente` para `cliente_id`.


### 7. Relatório mensal – Período permitido

**Dúvida:**  
Regra de negócio para que o relatório por cliente não aceite mês superior ao atual.

**Premissa adotada:**  
O mês/ano selecionado no relatório não pode ser superior ao mês/ano atual. Ou seja, não é permitido gerar relatório para período futuro; apenas meses já decorridos ou o mês corrente.


### 8. Relatório – Campo de feedback (texto)

**Dúvida:**  
No relatório mensal deve haver “campo de feedback (texto)”, mas a entidade Demanda não tem esse atributo. Quem deve fornecer esse feedback, em qual etapa? É requisito para concluir a demanda?

**Premissa adotada:**  
Foi adicionado o atributo `feedback` (texto, opcional) à entidade Demandas. Faz sentido que o feedback seja preenchido por **quem testa** a demanda (`quem_deve_testar`), na etapa **Teste** do Kanban — obrigatório para avançar para a próxima etapa, deploy.

---

## Planejamento

A estrutura inicial das entidades no banco de dados é composta por **Cliente** e **Demanda**.  
Um cliente pode possuir várias demandas, caracterizando um relacionamento **1:N**.

A modelagem foi mantida propositalmente simples para respeitar o escopo do MVP e evitar complexidade desnecessária nesta etapa do desafio.

Campos de *timestamps* foram adicionados às entidades com o objetivo de permitir, em uma evolução futura do sistema, a implementação de funcionalidades como **histórico de movimentações** ou **auditoria**.

O diagrama do modelo entidade-relacionamento está em [docs/dropz_MER.png](docs/dropz_MER.png).

---

## Ordem de Execução

1. `docs: add MER and project planning`
2. `chore: initialize project structure with backend and frontend`
3. `feat: create database migrations for clients and demands`
4. `feat: add Eloquent models and relationships`
5. `feat: add feedback field to demands (required for Teste→Deploy)`
6. `docs: document frontend component organization and interface decisions`
7. `feat: create frontend base layout and navigation`
8. `feat: add Clients page structure`
9. `feat: add Demands page structure`
10. `feat: add Kanban page structure`
11. `feat: add Report page and UX adjustments`
12. `docs: document backend rules and frontend-backend API integration`
13. `feat: implement clients API endpoints`
14. `feat: implement demands API endpoints`
15. `feat: implement kanban status update flow`
16. `feat: implement monthly client report endpoint`
17. `feat: add monthly client report page`

---

## Frontend – Organização e decisões de interface

### Recursos e bibliotecas

- **Vue 3** (`^3.5.22`) – Framework JavaScript reativo.
- **Quasar Framework** (`^2.16.0`) – Framework Vue para componentes UI e layout.
- **Vue Router** (`^4.0.0`) – Roteamento para aplicações Vue.
- **Bootstrap Icons** (`^1.13.1`) – Biblioteca de ícones utilizada no projeto (importada em `src/css/app.scss`).


### Organização de componentes

- **`src/pages/`** – Páginas: Clientes, Demandas, Kanban, Relatório mensal.
- **`src/components/`** – Componentes reutilizáveis (ex.: card do Kanban, modal de detalhe, formulários).
- **`src/layouts/`** – Layout principal com header, menu e rodapé.
- **`src/services/`** – Serviço centralizado de chamadas à API do backend.
- **`src/router/`** – Definição das rotas do frontend.

### Decisões de interface

- **Header (topo):** logo à esquerda; ícone do Git à direita.
- **Menu:** logo abaixo do header, com os itens: Kanban, Clientes, Demandas, Relatório.
- **Área principal:** conteúdo da página selecionada (colunas do Kanban, listagens ou relatório).
- **Clientes:** exibe todos os clientes; permite buscar e tem opção “Cadastrar novo cliente”.
- **Demandas:** exibe todas as demandas; permite buscar e tem botão para criar nova demanda.
- **Kanban:** colunas por status; cards ordenados por prioridade (Alta, Normal, Baixa) e, dentro de cada prioridade, por data de criação. Os cards podem ser movidos de uma coluna para outra por **arrastar e soltar (drag and drop)**.
- **Card na coluna:** exibe título, cliente, prioridade, setor, responsável e um ícone “i” quando a demanda foi reaberta (`flag_retornou`). Ao clicar no card, abre sobre a tela um modal com os dados completos da demanda. Ao **arrastar o card para outra coluna**, abre-se o modal para preencher o que faltar (ex.: campos obrigatórios para avançar naquele status). No modal há também um **botão para avançar para a próxima etapa do fluxo** (ex.: “Avançar para Fila”, “Marcar em desenvolvimento”).
- **Feedback (etapa Teste):** ao avançar da etapa Teste para Deploy, o modal exige o preenchimento do campo feedback; quem testa (`quem_deve_testar`) preenche antes de avançar.
- **Formulários:** nas telas de Clientes e de Demandas, uma lista dos itens; botão para cadastrar novo; ao clicar em um item, abre formulário ou modal para editar.
- **Relatório:** solicita seleção do cliente e do mês/ano; exibe os dados conforme os filtros. O mês/ano selecionado não pode ser superior ao mês/ano atual (regra documentada em Dúvidas e Premissas). O relatório pode ser **exportado em PDF e em CSV**.
- **Alerta de tempo:** quando tempo gasto > tempo estimado, o card exibe cor de alerta (prioridade não é alterada automaticamente).
- **Rodapé:** fixo na parte inferior da página, com meu nome.

# Backend rules e integração frontend-backend (API)

Regras e padrões do backend Laravel e como o frontend Vue/Quasar deve integrar com a API.

---

## 1. Estrutura do backend

### 1.1. Stack e contexto

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 + Quasar 2 (Vite)
- **Banco**: Postgres (alvo), mas `.env.example` hoje aponta SQLite como default
- **Formato**: JSON (`Content-Type: application/json`, `Accept: application/json`)

### 1.2. Organização geral

- `backend/app/Http/Controllers` – Controllers da API (a serem criados):
   - `ClientController`
   - `DemandController`
   - `ReportController`
- `backend/app/Models` – Models Eloquent:
   - `Client`
   - `Demand`
- `backend/routes/api.php` – Rotas da API (a serem usadas para os endpoints descritos abaixo)
- `backend/database/migrations` – Migrations para:
   - `clients`
   - `demands`
   - tabelas padrão do Laravel (users, cache, jobs etc.)
  
### 1.3. Convenções de código

- **Controllers:** PascalCase com sufixo `Controller` (ex.: `ClientController`).
- **Models:** PascalCase, singular (ex.: `Client`, `Demand`).
- **Rotas:** usar prefixo `/api` e nomes em minúsculas (ex.: `/api/clients`, `/api/demands`).
- **Respostas JSON:**
   - Em sucesso, retornar objeto com `data` (e opcionalmente `message`).
   - Em erro de validação (422), retornar `message` + `errors` por campo.
   - Em erro genérico, retornar `message` descritiva.

---

## 2. Convenções de API

- **Base URL (dev)**: `http://localhost:8000/api`
- **Nomenclatura**:
   - Campos em **snake_case** (ex.: `cliente_id`, `quem_deve_testar`)
   - Coleções no plural (ex.: `/clients`, `/demands`)
- **Respostas**:
   - Sucesso: `{ "data": ..., "message": "..."? }`
   - Erro: `{ "message": "...", "errors": { ... }? }`

### Códigos HTTP usados

- **200** OK (GET/PUT/PATCH)
- **201** Created (POST)
- **400** Bad Request (payload inválido/sintaxe)
- **404** Not Found
- **422** Unprocessable Entity (validação e regras de negócio)
- **500** Internal Server Error

---

## 3. Regras de negócio (backend)

### 3.1. Ordenação do Kanban (todas as colunas)

- Ordenar por:
   1. **prioridade**: `Alta > Normal > Baixa`
   2. Dentro da prioridade: `created_at` asc (mais antigo primeiro)
- Essa ordenação deve existir no backend para o frontend só renderizar.

### 3.2. Critérios para mover Autorização → Fila

Para uma demanda entrar em **Fila**, precisam ser verdadeiros:

- `valor_pago >= 0.5 * valor_total`
- `descricao_detalhada` com **mais de 50 caracteres**
- Campos definidos (não vazios):
   - `responsavel`
   - `setor`
   - `quem_deve_testar`

Se falhar, retornar **422** com `errors` por campo (ou um erro geral se for regra composta).

### 3.3. Feedback obrigatório em Teste → Deploy

- Ao mover de **Teste** para **Deploy**, `feedback` deve estar preenchido (texto não vazio).
- Se falhar, retornar **422**.

### 3.4. Alerta "tempo gasto > tempo estimado"

- Regra **não altera prioridade automaticamente**.
- Backend deve expor `tempo_estimado` e `tempo_gasto` para o frontend sinalizar alerta visual.

### 3.5. `flag_retornou`

- Indica que a demanda **já foi concluída** e depois **voltou ao fluxo** (reaberta).
- Quando uma demanda sair de `concluido` para qualquer status anterior, setar `flag_retornou = true`.

### 3.6. Relatório mensal (período permitido)

- Parâmetros `mes` e `ano` não podem representar data futura.
- Se futuro, retornar **422**.

---

## 4. Modelo de dados (contrato)

### 4.1. Cliente

```json
{
"id": 1,
"nome": "Tech Solutions Ltda",
"email": "contato@techsolutions.com",
"avisar_por_email": true,
"whatsapp": "(11) 99999-1111",
"avisar_por_whatsapp": true,
"observacoes": "Cliente corporativo",
"created_at": "2026-02-03T10:15:30Z",
"updated_at": "2026-02-03T10:15:30Z"
}
```

### 4.2. Demanda

```json
{
"id": 10,
"cliente_id": 1,
"titulo": "API relatório mensal",
"prioridade": "Normal",
"status": "autorizacao",
"setor": "Financeiro",
"responsavel": "Luiz",
"quem_deve_testar": "Ana",
"descricao_detalhada": "Texto detalhado com mais de 50 caracteres...",
"midia": "https://...",
"cobrada_do_cliente": false,
"valor_total": 1000.00,
"valor_pago": 500.00,
"tempo_estimado": 12,
"tempo_gasto": 8,
"feedback": "Opcional, obrigatório no Teste→Deploy",
"flag_retornou": false,
"created_at": "2026-02-03T10:15:30Z",
"updated_at": "2026-02-03T10:15:30Z"
}
```
### 4.3. Enums/ domínios

- prioridade: Alta | Normal | Baixa
- status: backlog | autorizacao | fila | desenvolvimento | teste | deploy | concluido
- Unidades:
  tempo_estimado e tempo_gasto: horas (inteiro)
  valor_total e valor_pago: decimal (2 casas)

## 5. Endpoints (MVP)
- Prefixo: /api

### 5.1. Clientes - /clients
- GET /clients
- POST /clients
- PUT /clients/{id}

### 5.2. Demandas - /demands
- GET /demands
- POST /demands
- PATCH /demands/{id}
- PATCH /demands/{id}/status

### 5.3. Relatório - /reports
- GET /reports/clients/{id}?month=YYYY-MM

## 6. Integração com frontend

- Variável de ambiente:
  VITE_API_BASE_URL=http://localhost:8000/api
- Comunicação via Axios
- Frontend apenas consome e renderiza
- Regras críticas são validadas exclusivamente no backend

## 7. CORS
Durante o desenvolvimento, o backend permite requisições originadas de:

http://localhost:9000 (Quasar dev server)

Métodos permitidos:
- GET
- POST
- PUT
- PATCH

Headers permitidos:
Content-Type
Accept