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

---

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

---

### 3. Atributos Tempo Estimado e Tempo Gasto

**Dúvida:**  
Quando o tempo gasto for maior que o tempo estimado, o card deve mudar de prioridade? O tempo gasto deve ser atualizado em cada etapa do fluxo ou apenas quando for concluído?

**Premissa adotada:**  
A prioridade da demanda não será alterada automaticamente.  
O cálculo de tempo gasto será feito apenas quando o card for marcado como concluído.  
A definição ou alteração da prioridade permanece como responsabilidade da equipe.

---

### 4. Significado do Campo `flag_retornou`

**Dúvida:**  
O campo `flag_retornou` indica que a demanda foi reaberta após ter sido concluída?

**Premissa adotada:**  
Sim. O campo `flag_retornou` será utilizado para identificar demandas que retornaram ao fluxo após terem sido marcadas como concluídas.

---

### 5. Entidade Demandas – Controle de Pagamento

**Dúvida:**  
É interessante adicionar atributos de valor pago e valor total para deixar claro o valor ainda devido, considerando que para autorizar uma demanda é necessário um pagamento mínimo de 50%?

**Premissa adotada:**  
Foram adicionados os atributos `valor_total` e `valor_pago` à entidade de demandas para permitir esse controle.

---

### 6. Entidade Demandas – Atributo data_cadastro e cliente

**Dúvida:**  
O atributo `data_cadastro` da entidade Demandas fica duplicado ao adicionar `timestamps`, que já geram as colunas `created_at` e `updated_at`.  
O atributo `cliente` (FK) não é descritivo, visto que a relação é estabelecida utilizando o id do cliente.

**Premissa adotada:**  
Removi o atributo `data_cadastro`, pois o campo `created_at` gerado pelos `timestamps` será utilizado como data de cadastro da demanda.  
Renomeei o atributo `cliente` para `cliente_id`.

---

### 7. Relatório mensal – Período permitido

**Dúvida:**  
Regra de negócio para que o relatório por cliente não aceite mês superior ao atual.

**Premissa adotada:**  
O mês/ano selecionado no relatório não pode ser superior ao mês/ano atual. Ou seja, não é permitido gerar relatório para período futuro; apenas meses já decorridos ou o mês corrente.

---

### 8. Relatório – Campo de feedback (texto)

**Dúvida:**  
No relatório mensal deve haver “campo de feedback (texto)”, mas a entidade Demanda não tem esse atributo. Quem deve fornecer esse feedback, em qual etapa? É requisito para concluir a demanda?

**Premissa adotada:**  
Foi adicionado o atributo `feedback` (texto, opcional) à entidade Demandas. Faz sentido que o feedback seja preenchido por **quem testa** a demanda (`quem_deve_testar`), na etapa **Teste** do Kanban — obrigatório para avançar para a próxima etapa, deploy.

---

### 9. Atributo cobrado_do_cliente

**Dúvida:**  
Qual o sentido de ter esse atributo? Qual sua utilidade no contexto já que foram incluídos os atributos valor_pago e valor_total?

**Premissa adotada:**  
O atributo `cobrada_do_cliente` é calculado no backend:  
- **Demanda cobrada** quando `valor_pago` == `valor_total`  
- **Demanda com valor a cobrar** quando `valor_pago` < `valor_total`  
No frontend, a informação é apenas exibida.

---

## Planejamento

A estrutura inicial das entidades no banco de dados é composta por **Cliente** e **Demanda**.  
Um cliente pode possuir várias demandas, caracterizando um relacionamento **1:N**.

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
17. `feat: frontend-backend integration + rule adjustments (time/paid_amount)`

---

## Branches e integração

- **`main`**: branch estável (versão final do projeto)
- **`frontend`**: desenvolvimento isolado do frontend
- **`backend`**: desenvolvimento isolado do backend
- **`frontend-backend-integration`**: branch usada para integrar frontend e backend
  
O commit de integração (**passo 17**) inclui:
- a integração completa front ↔ API
- ajustes das regras de negócio de **tempo gasto** e **valor pago**

Após validar tudo na branch de integração, será feito **merge** para a `main`.

---

## Como rodar o projeto

### Backend (Laravel)
```bash
cd backend
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

### Frontend (Vue + Quasar)
```bash
cd frontend
npm install
npm run dev
```

**Portas padrão:**
- Backend: `http://localhost:8000`
- Frontend: `http://localhost:9000`

---

## Variáveis de ambiente

### Frontend
No `.env` do frontend:
```
VITE_API_BASE_URL=http://localhost:8000/api
```

### Backend
No `.env` do backend:
- `APP_KEY` (gerado com `php artisan key:generate`)
- Configuração do banco de dados (DB_*)

---

## Integração Front ↔ API

- O frontend consome a API via Axios (`src/services/api.js`).
- A base da API é definida por `VITE_API_BASE_URL`.
- Respostas seguem o padrão:

Sucesso:
```json
{ "data": ..., "message": "..." }
```

Erro:
```json
{ "message": "...", "errors": { "campo": ["mensagem"] } }
```

Regras críticas (pagamento, requisitos para mudança de status, feedback obrigatório, etc.) são **validadas no backend**.  
O frontend apenas **consome e exibe** essas informações.

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
- **Card na coluna:** exibe título, cliente, prioridade, setor, responsável e um ícone “i” quando a demanda foi reaberta (`flag_retornou`). Ao clicar no card, abre um modal com os dados completos.  
- **Modal de erros (movimentação):** ao tentar mover ou avançar um card sem cumprir as regras, é exibido um **modal com a lista de erros retornada pela API**.  
- **Avançar status:** no modal existe um botão “Avançar” que envia a demanda para o próximo status, seguindo as regras do backend.  
- **Feedback (etapa Teste):** ao avançar de Teste para Deploy, o campo feedback é obrigatório.  
- **Tempo gasto:** é calculado automaticamente pelo backend **quando a demanda é concluída** e exibido no front em modo somente leitura.  
- **Cobrança:** o status de cobrança é **exibido** no modal como “Demanda cobrada” ou “Demanda com valor a cobrar” (calculado no backend).  
- **Relatório:** solicita seleção do cliente e do mês/ano; exibe os dados conforme os filtros. O mês/ano selecionado não pode ser superior ao mês/ano atual. O relatório pode ser **exportado em PDF e em CSV**.  
- **Rodapé:** fixo na parte inferior da página, com meu nome.
