# Roteiro de Apresentação — Gestão ONG
**Fluxo guiado: Home → Perfis → Administração → Encerramento na Home**

---

## 1. Preparação Rápida Antes de Começar
- **Não altere o código** no dia da apresentação para evitar imprevistos.
- **Tenha contas de teste prontas** para alternar sem perder tempo:
  - 1 conta de **Doador** ativa.
  - 1 conta de **Beneficiário** aprovada e ativa.
  - 1 conta de **Administrador** (ex.: `admin@ong.com`)[cite: 10, 27].
- **Foque nos fluxos principais** que já estão funcionando de ponta a ponta.

---

## 2. Roteiro Passo a Passo (Início na Home → Fim na Home)

### Etapa 1: Abertura na Home (`home.php`)
- **O que mostrar na tela:**
  - Cabeçalho, título *Gestão Social Inteligente* e botões de acesso[cite: 18].
  - Cards de impacto, carrossel dinâmico com as ações cadastradas e seção institucional (*Quem Somos*)[cite: 18].
- **O que falar:**
  > *"Bom dia, professores. Este é o **Gestão ONG**, um sistema web desenvolvido para organizar o atendimento a famílias assistidas, o registro de doações e a divulgação das ações sociais da ONG[cite: 18]. A aplicação usa uma arquitetura baseada em MVC com entrada única pelo `index.php`, roteamento pelo `routes.json`, configurações centralizadas e conexão ao banco MySQL via PDO na classe `DBConnection`[cite: 1, 2, 18]. Aqui na Home, o visitante já acompanha as ações cadastradas no banco e acessa as áreas do sistema[cite: 18]."*

---

### Etapa 2: Login, Cadastro e Recuperação de Senha (`login.php`, `registrar.php`, `nova_senha.php`)
- **O que mostrar na tela:**
  - Tela de **Login** (`login.php`)[cite: 22].
  - Tela de **Criar Conta** (`registrar.php`), mostrando a diferença entre escolher **Doador** ou **Beneficiário** (com campos extras, máscaras e preenchimento automático de endereço pelo ViaCEP)[cite: 19].
  - Modal de **Recuperar Senha** no login[cite: 22].
- **O que falar:**
  > *"Na tela de login, o sistema autentica o usuário conferindo o hash da senha e envia cada perfil para o seu painel[cite: 9, 10, 22]. No cadastro, se a pessoa escolhe ser **Doador**, preenche os dados básicos; se escolhe ser **Beneficiário**, o formulário exibe informações complementares como CPF, telefone, membros da família, renda familiar e endereço com consulta automática via ViaCEP e máscaras em JavaScript[cite: 19, 30]. Também estruturamos o fluxo de recuperação de senha com geração de token de redefinição[cite: 9, 20, 22]."*

---

### Etapa 3: Área do Doador (`painel.php` do Doador e `DoadorController.php`)
- **O que mostrar na tela:**
  - Login com conta de **Doador**.
  - Card de total de contribuições e tabela com o histórico de doações[cite: 24].
  - Modal **Fazer Nova Doação**, alternando para exibir QR Code e chave PIX ao selecionar *Financeiro (PIX)*[cite: 24].
- **O que falar:**
  > *"Ao acessar a área do Doador, o `DoadorController` confere a sessão e o perfil e carrega o histórico de contribuições daquele usuário[cite: 5, 24]. Ele consegue acompanhar o que já doou e registrar novas doações de alimentos, roupas, outros itens ou via PIX, com QR Code e chave prontos para copiar no modal[cite: 5, 24]."*
- **Transição:** Clicar em **Sair** para encerrar a sessão do doador[cite: 5, 24].

---

### Etapa 4: Área do Beneficiário (`painel.php` do Beneficiário e `BeneficiarioController.php`)
- **O que mostrar na tela:**
  - Login com conta de **Beneficiário** ativa.
  - Cards de resumo (*Total de Pedidos*, *Em Andamento*, *Atendimentos Concluídos*)[cite: 23].
  - Tabela de solicitações com status, respostas da administração e opção de cancelar pedidos que ainda estejam *Pendente*[cite: 23].
  - Modal **Meus Dados** (somente leitura) e seção **Próximas Ações e Campanhas**[cite: 23].
- **O que falar:**
  > *"No painel do Beneficiário, o usuário acompanha o andamento dos seus pedidos de apoio, vê respostas da ONG quando houver observações e pode cancelar uma solicitação enquanto ela ainda estiver pendente[cite: 7, 23]. Ele também pode abrir uma nova solicitação por categoria, consultar seus dados cadastrais em modo somente leitura e ver as campanhas divulgadas pela ONG[cite: 7, 23]."*
- **Transição:** Clicar em **Sair** para ir para o painel administrativo[cite: 7, 23].

---

### Etapa 5: Painel Administrativo (`dashboard.php`, `solicitacoes.php`, `doacoes.php`, `acoes.php`)
- **O que mostrar na tela:**
  - **Sino de Notificações:** Listagem de beneficiários pendentes com ações de **Aprovar** ou **Rejeitar**[cite: 8, 27].
  - **Aba Famílias (`dashboard.php`):** Campo de busca por responsável, cadastro de nova família, edição (com conversão da renda familiar para formato decimal) e exclusão com justificativa[cite: 8, 27].
  - **Aba Pedidos (`solicitacoes.php`):** Alteração de status (*Pendente*, *Aprovado*, *Entregue*, *Cancelado*) e envio de observação/horário[cite: 2, 29].
  - **Aba Doações (`doacoes.php`):** Listagem e filtro das doações recebidas[cite: 6, 26].
  - **Aba Ações (`acoes.php`):** Cadastro, edição e exclusão de campanhas com upload de imagem[cite: 8, 28].
- **O que falar:**
  > *"O painel administrativo centraliza a gestão da ONG[cite: 8, 27]. Por aqui, a equipe analisa cadastros pendentes de beneficiários, gerencia as famílias — incluindo o tratamento da renda familiar para salvar corretamente no banco —, responde e atualiza o status dos pedidos, consulta as doações registradas e publica as ações da ONG[cite: 2, 6, 8, 26, 27, 28, 29]."*

---

### Etapa 6: Encerramento na Home (`home.php`)
- **O que mostrar na tela:**
  - Sair do painel administrativo (`auth/logout`) e retornar à **Home** (`home.php`), destacando como as ações cadastradas ficam visíveis na vitrine inicial[cite: 8, 18].
- **O que falar:**
  > *"Ao sair do painel, voltamos para a **Home**, fechando o ciclo completo da plataforma entre quem visita, quem doa, quem recebe apoio e quem administra[cite: 8, 18]. O sistema já apresenta o fluxo principal funcional para avaliação do TCC, com pontos de evolução mapeados para etapas posteriores, como o envio automático de e-mails[cite: 8, 18]. Muito obrigado e fico à disposição para as perguntas."*

---

## 3. Pontos Rápidos para Responder à Banca
- **Arquitetura:** Combina classes orientadas a objetos (Controllers, Models e conexão PDO) com despacho de rotas e renderização de views em HTML/PHP[cite: 1, 2, 8, 10, 18].
- **Segurança:** Uso de hash de senha, consultas preparadas com PDO (*prepared statements*) e verificação de sessão e perfil nos controllers[cite: 2, 5, 7, 8, 9, 10].
- **Próximos passos:** Concluir refinamentos pendentes, como a integração completa de envio automático de e-mails na recuperação de senha[cite: 8, 18].
