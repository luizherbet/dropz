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

### 3. Relação entre Tempo Estimado e Tempo Gasto

**Dúvida:**  
Quando o tempo gasto for maior que o tempo estimado, o card deve mudar de prioridade ou apenas sinalizar um alerta visual?

**Premissa adotada:**  
A prioridade da demanda não será alterada automaticamente.  
Quando o tempo gasto ultrapassar o tempo estimado, o card apenas mudará de cor como forma de alerta visual, indicando que a demanda necessita de atenção da equipe.

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
5. `docs: document frontend component organization and interface decisions`
6. `feat: create frontend base layout and navigation`
7. `feat: add clients and demands pages structure`
8. `docs: document backend rules and frontend-backend API integration`
9. `feat: implement clients API endpoints`
10. `feat: implement demands API endpoints`
11. `feat: implement kanban status update flow`
12. `feat: implement monthly client report endpoint`
13. `feat: add monthly client report page`

