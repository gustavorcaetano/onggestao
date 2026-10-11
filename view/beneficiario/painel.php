<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Assistido - Gestão ONG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-dark text-light">

    <!-- Topbar -->
    <nav class="navbar navbar-dark bg-black border-bottom border-secondary border-opacity-25 py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold text-white fs-5" href="#">
                <i class="fa-solid fa-hands-holding-child me-2 text-cyan"></i>ÁREA DO <span class="text-cyan">ASSISTIDO</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">Olá, <b class="text-white"><?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Beneficiário'); ?></b></span>
                <a href="/index.php?url=auth/logout" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-semibold">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="container my-5">

        <?php if (!empty($_GET['sucesso'])): ?>
            <div class="alert alert-success bg-dark border-success text-success alert-dismissible fade show p-3 rounded-3 shadow-lg mb-4" role="alert" style="backdrop-filter: blur(8px);">
                <i class="fa-solid fa-circle-check me-2"></i><strong>Solicitação Registrada!</strong> <?php echo htmlspecialchars($_GET['sucesso']); ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-black text-white mb-1">Minhas Solicitações e Atendimentos</h1>
                <p class="text-muted small mb-0">Acompanhe os pedidos de apoio enviados e faça novos pedidos quando necessário.</p>
            </div>
            <button class="btn btn-cyan-gradient btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalNovaSolicitacao">
                <i class="fa-solid fa-plus me-1"></i> Nova Solicitação
            </button>
        </div>

        <!-- Tabela Glassmorphism -->
        <div class="card-impact p-0 overflow-hidden text-start shadow-lg" style="border-radius: 16px;">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead class="table-secondary bg-opacity-10 text-muted small">
                        <tr>
                            <th class="py-3 ps-4">CATEGORIA</th>
                            <th>DESCRIÇÃO / NECESSIDADE</th>
                            <th>DATA</th>
                            <th class="text-end pe-4">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($solicitacoes)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Você ainda não fez nenhuma solicitação de apoio.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($solicitacoes as $s): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-white"><span class="badge bg-secondary border border-secondary"><?php echo htmlspecialchars($s['categoria'] ?? 'Geral'); ?></span></td>
                                    <td><?php echo htmlspecialchars($s['descricao']); ?></td>
                                    <td class="text-muted small"><?php echo isset($s['criado_em']) ? date('d/m/Y H:i', strtotime($s['criado_em'])) : '-'; ?></td>
                                    <td class="text-end pe-4">
                                        <?php if (($s['status'] ?? '') == 'aprovado'): ?>
                                            <span class="badge bg-primary">Aprovado</span>
                                        <?php elseif (($s['status'] ?? '') == 'entregue'): ?>
                                            <span class="badge bg-success">Entregue</span>
                                        <?php elseif (($s['status'] ?? '') == 'pendente'): ?>
                                            <span class="badge bg-warning text-dark">Pendente</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Em Análise</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
                <form action="/index.php?url=solicitacao/cadastrar" method="POST">
                    <div class="modal-body row g-3">
                        <div class="col-12">
                            <label class="form-label text-muted small">Categoria do Pedido</label>
                            <select name="categoria" class="form-select bg-dark text-light border-secondary" required>
                                <option value="Cesta Básica / Alimentos">Cesta Básica / Alimentos</option>
                                <option value="Roupas / Agasalhos">Roupas / Agasalhos</option>
                                <option value="Medicamento / Saúde">Medicamento / Saúde</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Descreva sua Necessidade</label>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>