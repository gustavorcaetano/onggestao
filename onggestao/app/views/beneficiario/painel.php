<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Assistido - Gestão ONG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $_SESSION['url']['root']; ?>public/assets/css/style.css">
    <link rel="icon" href="<?php echo $_SESSION['url']['root']; ?>public/assets/img/logo.png" type="image/png">
</head>
<body class="bg-dark text-light">

    <!-- Topbar -->
    <nav class="navbar navbar-dark bg-black border-bottom border-secondary border-opacity-25 py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold text-white fs-5" href="#">
                <i class="fa-solid fa-hands-holding-child me-2 text-cyan"></i>ÁREA DO <span class="text-cyan">BENEFICIÁRIO</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">Olá, <b class="text-white"><?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Beneficiário'); ?></b></span>
                <a href="<?php echo $_SESSION['url']['root']; ?>auth/logout" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-semibold">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="container my-5">

        <!-- Se certo -->
        <?php if (!empty($_GET['sucesso'])): ?>
            <div class="alert alert-success bg-dark border-success text-success alert-dismissible fade show p-3 rounded-3 shadow-lg mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i><strong>Sucesso!</strong> <?php echo htmlspecialchars($_GET['sucesso']); ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Se der errado -->
        <?php if (!empty($_GET['erro'])): ?>
            <div class="alert alert-danger bg-dark border-danger text-danger alert-dismissible fade show p-3 rounded-3 shadow-lg mb-4" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i><strong>Atenção!</strong> <?php echo htmlspecialchars($_GET['erro']); ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-white mb-1">Minhas Solicitações e Atendimentos</h1>
                <p class="text-muted small mb-0">Acompanhe os pedidos de apoio enviados e faça novos pedidos quando necessário.</p>
            </div>
            <div class="d-flex gap-2">
                <!-- Botão Meus Dados (Visualização) -->
                <button class="btn btn-dark-outline border-secondary text-light btn-sm fw-semibold px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalMeusDados">
                    <i class="fa-solid fa-id-card me-1 text-cyan"></i> Meus Dados
                </button>
                <!-- Botão Nova Solicitação -->
                <button class="btn btn-cyan-gradient btn-sm fw-bold px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalNovaSolicitacao">
                    <i class="fa-solid fa-plus me-1"></i> Nova Solicitação
                </button>
            </div>
        </div>
        
        <!-- Cálculo dos Contadores de Resumo -->
        <?php
            $totalPedidos = count($solicitacoes ?? []);
            $emAndamento = 0;
            $concluidos = 0;
        
            // Verifica se há solicitações e conta quantas estão em andamento e quantas foram concluídas, para exibir nos cards de resumo.
            if (!empty($solicitacoes)) // Se houver solicitações cadastradas, percorre cada uma para determinar seu status e atualizar os contadores de acordo
                {
                foreach ($solicitacoes as $item) {
                    $st = $item['status'] ?? 'Pendente'; // Obtém o status da solicitação, considerando "Pendente" como padrão caso não esteja definido
                    if ($st === 'Pendente' || $st === 'Aprovado') {
                        $emAndamento++;
                    } elseif ($st === 'Entregue') {
                        $concluidos++;
                    }
                }
            }
        ?>
        
        <!-- Cards de Resumo -->
        <div class="row g-3 mb-4">
            <!-- Card 1: Total de Pedidos -->
            <div class="col-md-4">
                <div class="bg-black border border-secondary border-opacity-25 rounded-4 p-3 shadow-sm d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block mb-1">TOTAL DE PEDIDOS</span>
                        <h3 class="fw-bold text-white mb-0"><?php echo $totalPedidos; ?></h3>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-dark border border-secondary border-opacity-50" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-layer-group text-cyan fs-5"></i>
                    </div>
                </div>
            </div>
        
            <!-- Card 2: Em Análise / Aprovados -->
            <div class="col-md-4">
                <div class="bg-black border border-secondary border-opacity-25 rounded-4 p-3 shadow-sm d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block mb-1">EM ANDAMENTO</span>
                        <h3 class="fw-bold text-warning mb-0"><?php echo $emAndamento; ?></h3>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-dark border border-secondary border-opacity-50" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-clock text-warning fs-5"></i>
                    </div>
                </div>
            </div>
        
            <!-- Card 3: Entregues / Concluídos -->
            <div class="col-md-4">
                <div class="bg-black border border-secondary border-opacity-25 rounded-4 p-3 shadow-sm d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block mb-1">ATENDIMENTOS CONCLUÍDOS</span>
                        <h3 class="fw-bold text-success mb-0"><?php echo $concluidos; ?></h3>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-dark border border-secondary border-opacity-50" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-circle-check text-success fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Histórico de Solicitações -->
        <div class="card-impact p-0 overflow-hidden text-start shadow-lg" style="border-radius: 16px;">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead class="table-secondary bg-opacity-10 text-muted small">
                        <tr>
                            <th class="py-3 ps-4">CATEGORIA</th>
                            <th>DESCRIÇÃO / NECESSIDADE</th>
                            <th>DATA</th>
                            <th>STATUS</th>
                            <th class="text-end pe-4">AÇÃO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($solicitacoes)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Você ainda não fez nenhuma solicitação de apoio.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($solicitacoes as $s): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-white"><span class="badge bg-secondary border border-secondary"><?php echo htmlspecialchars($s['categoria'] ?? 'Geral'); ?></span></td>
                                    <td>
                                        <div><?php echo htmlspecialchars($s['descricao'] ?? ''); ?></div>
                                        <?php if (!empty($s['observacao_admin'])): ?>
                                            <div class="mt-2 p-2 rounded-3 bg-black border border-info border-opacity-25 small">
                                                <i class="fa-solid fa-reply text-cyan me-1"></i>
                                                <strong class="text-cyan">Resposta da ONG:</strong>
                                                <span class="text-light"><?php echo htmlspecialchars($s['observacao_admin']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small"><?php echo isset($s['criado_em']) ? date('d/m/Y H:i', strtotime($s['criado_em'])) : '-'; ?></td>
                                    <td class="text-end pe-4">
                                        <?php 
                                            $status = $s['status'] ?? 'Pendente'; 
                                        ?>
                                        <?php if ($status === 'Aprovado'): ?>
                                            <span class="badge bg-primary">Aprovado</span>
                                        <?php elseif ($status === 'Entregue'): ?>
                                            <span class="badge bg-success">Entregue</span>
                                        <?php elseif ($status === 'Cancelado'): ?>
                                            <span class="badge bg-secondary text-dark fw-semibold" style="background-color: #adb5bd !important;">Cancelado</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Em Análise</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <?php if ($status === 'Pendente'): ?>
                                            <form action="<?php echo $_SESSION['url']['root']; ?>beneficiario/cancelarSolicitacao" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja cancelar esta solicitação?');">
                                                <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm px-2 py-1" title="Cancelar pedido">
                                                    <i class="fa-solid fa-xmark me-1"></i> Cancelar
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Seção de Próximas Ações e Campanhas da ONG -->
        <div class="mt-5 pt-4 border-top border-secondary border-opacity-25">
            <div class="mb-4">
                <h2 class="h4 fw-bold text-white mb-1">
                    <i class="fa-solid fa-bullhorn text-cyan me-2"></i>Próximas Ações e Campanhas
                </h2>
                <p class="text-muted small mb-0">Fique por dentro dos mutirões, entregas especiais e eventos organizados pela ONG.</p>
            </div>
        
            <!-- Lista de Ações e Campanhas -->
            <?php if (empty($acoesOng)): ?> <!-- Se não houver ações ou campanhas cadastradas, exibe uma mensagem de aviso em vez de cards -->
                <div class="bg-black border border-secondary border-opacity-25 rounded-4 p-4 text-center text-muted">
                    <i class="fa-regular fa-calendar-xmark fs-4 mb-2 d-block text-secondary"></i>
                    Nenhuma ação ou campanha programada no momento.
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <!-- Cards de Ações e Campanhas -->
                    <?php foreach ($acoesOng as $acao): ?> <!-- Itera sobre cada ação ou campanha cadastrada e exibe os detalhes em um card -->
                        <div class="col-md-4">
                            <div class="bg-black border border-secondary border-opacity-25 rounded-4 p-4 h-100 d-flex flex-column justify-content-between shadow-sm">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-info bg-opacity-10 text-cyan border border-info border-opacity-25">
                                            Campanha / Evento
                                        </span>
                                        <small class="text-muted">
                                            <i class="fa-regular fa-calendar me-1"></i>
                                            <?php echo isset($acao['criado_em']) ? date('d/m/Y', strtotime($acao['criado_em'])) : '-'; ?>
                                        </small>
                                    </div>
                                    <h5 class="fw-bold text-white mb-2"><?php echo htmlspecialchars($acao['titulo'] ?? ''); ?></h5>
                                    <p class="text-muted small mb-0" style="line-height: 1.5;">
                                        <?php echo nl2br(htmlspecialchars($acao['descricao'] ?? '')); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    

    <!-- Modal Nova Solicitação -->
    <div class="modal fade" id="modalNovaSolicitacao" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-black border border-secondary text-light rounded-4 shadow-lg text-start">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-cyan"><i class="fa-solid fa-hand-holding-heart me-2"></i>Solicitar Apoio / Mantimento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo $_SESSION['url']['root']; ?>beneficiario/solicitar" method="POST">
                    <div class="modal-body row g-3">
                        <div class="col-12">
                            <label class="form-label text-muted small">Categoria do Pedido:</label>
                            <select name="categoria" class="form-select bg-dark text-light border-secondary" required>
                                <option value="Cesta Básica / Alimentos">Cesta Básica / Alimentos</option>
                                <option value="Roupas / Agasalhos">Roupas / Agasalhos</option>
                                <option value="Medicamento / Saúde">Medicamento / Saúde</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Descreva sua Necessidade:</label>
                            <textarea name="descricao" class="form-control bg-dark text-light border-secondary" rows="3" required placeholder="Explique brevemente o motivo e o que precisa..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-cyan-gradient btn-sm px-4 fw-bold">Enviar Pedido</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Meus Dados (Somente Visualização) -->
    <div class="modal fade" id="modalMeusDados" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-black border border-secondary text-light rounded-4 shadow-lg text-start">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-cyan"><i class="fa-solid fa-id-card me-2"></i>Meus Dados Cadastrais</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if (empty($dadosFamilia)): ?>
                        <p class="text-muted text-center py-4 mb-0">Não foi possível carregar os dados cadastrais da sua família.</p>
                    <?php else: ?>
                        <div class="alert bg-dark border border-secondary border-opacity-50 text-muted small d-flex align-items-center gap-2 mb-4">
                            <i class="fa-solid fa-lock text-cyan"></i>
                            <span>Por segurança, seus dados cadastrais são somente para visualização. Caso precise alterar alguma informação, abra uma solicitação na categoria <strong>"Outro"</strong>.</span>
                        </div>
    
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label text-muted small mb-1">Nome do Responsável</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($dadosFamilia['nome_responsavel'] ?? $_SESSION['usuario_nome'] ?? ''); ?>" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">E-mail de Acesso</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($dadosFamilia['email'] ?? $dadosFamilia['email_login'] ?? ''); ?>" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">CPF</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($dadosFamilia['cpf'] ?? 'Não informado'); ?>" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Telefone / WhatsApp</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($dadosFamilia['telefone'] ?? 'Não informado'); ?>" readonly disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small mb-1">Membros na Família</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($dadosFamilia['quantidade_membros'] ?? '1'); ?>" readonly disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small mb-1">Renda Familiar</label>
                                <input type="text" class="form-control bg-dark text-success fw-bold border-secondary" value="R$ <?php echo number_format($dadosFamilia['renda_familiar'] ?? 0, 2, ',', '.'); ?>" readonly disabled>
                            </div>
    
                            <hr class="border-secondary border-opacity-25 my-2">
    
                            <div class="col-md-4">
                                <label class="form-label text-muted small mb-1">CEP</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($dadosFamilia['cep'] ?? 'Não informado'); ?>" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Rua / Logradouro</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($dadosFamilia['rua'] ?? 'Não informado'); ?>" readonly disabled>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-muted small mb-1">Número</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($dadosFamilia['numero'] ?? 'S/N'); ?>" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Bairro</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($dadosFamilia['bairro'] ?? 'Não informado'); ?>" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Cidade - UF</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($dadosFamilia['cidade'] ?? 'Não informado'); ?>" readonly disabled>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer border-secondary justify-content-between">
                    <button type="button" class="btn btn-outline-info btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalNovaSolicitacao">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Solicitar Alteração de Dados
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>