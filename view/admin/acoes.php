<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Ações - Painel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-dark text-light">

    <!-- Topbar Padronizado -->
    <nav class="navbar navbar-dark bg-black border-bottom border-secondary border-opacity-25 py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold text-white fs-5" href="/index.php?url=admin/dashboard">
                <i class="fa-solid fa-gauge-high me-2 text-cyan"></i>PAINEL <span class="text-cyan">ADMINISTRATIVO</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">Olá, <b class="text-white"><?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Admin'); ?></b></span>
                <a href="/index.php?url=auth/logout" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-semibold">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-0">
        <?php if (!empty($_GET['sucesso'])): ?>
            <div class="alert alert-success bg-dark border-success text-success alert-dismissible fade show p-3 rounded-3 shadow-lg mb-0" role="alert" style="backdrop-filter: blur(8px);">
                <i class="fa-solid fa-circle-check me-2"></i><strong>Sucesso!</strong> <?php echo htmlspecialchars($_GET['sucesso']); ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['erro'])): ?>
            <div class="alert alert-danger bg-dark border-danger text-danger alert-dismissible fade show p-3 rounded-3 shadow-lg mb-0" role="alert" style="backdrop-filter: blur(8px);">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><strong>Atenção:</strong> <?php echo htmlspecialchars($_GET['erro']); ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Conteúdo Principal -->
    <div class="container my-5">
        
        <!-- Cabeçalho com Alinhamento Perfeito -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-white mb-1">Gestão de Ações da ONG</h1>
                <p class="text-muted small mb-0">Cadastre eventos, campanhas e projetos para exibição no feed público.</p>
            </div>
            
            <button class="btn btn-cyan-gradient btn-sm fw-bold px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalNovaAcao">
                <i class="fa-solid fa-plus me-1"></i> Nova Ação
            </button>
        </div>

        <!-- Abas de Navegação -->
        <div class="d-flex gap-2 mb-4">
            <a href="/index.php?url=admin/dashboard" class="btn btn-dark-outline px-4 py-2 btn-sm">FAMÍLIAS</a>
            <a href="/index.php?url=solicitacao/index" class="btn btn-dark-outline px-4 py-2 btn-sm">PEDIDOS</a>
            <a href="/index.php?url=doacao/index" class="btn btn-dark-outline px-4 py-2 btn-sm">DOAÇÕES</a>
            <a href="/index.php?url=admin/acoes" class="btn btn-cyan-gradient px-4 py-2 btn-sm">AÇÕES</a>
        </div>

        <!-- Tabela com Larguras Proporcionais -->
        <div class="card-impact p-0 overflow-hidden text-start shadow-lg" style="border-radius: 16px;">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle" style="table-layout: fixed; width: 100%;">
                    <thead class="table-secondary bg-opacity-10 text-muted small">
                        <tr>
                            <th class="py-3 ps-4" style="width: 30%;">TÍTULO</th>
                            <th style="width: 45%;">DESCRIÇÃO</th>
                            <th style="width: 15%;">DATA</th>
                            <th class="text-end pe-4" style="width: 10%;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($acoes)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Nenhuma ação cadastrada até o momento.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($acoes as $a): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-white text-truncate"><?php echo htmlspecialchars($a['titulo']); ?></td>
                                    <td class="text-muted text-truncate"><?php echo htmlspecialchars($a['descricao']); ?></td>
                                    <td class="text-cyan small fw-semibold"><?php echo date('d/m/Y', strtotime($a['criado_em'])); ?></td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-dark-outline py-1 px-2 text-danger" title="Excluir Ação">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Modal Nova Ação -->
    <div class="modal fade" id="modalNovaAcao" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-black border border-secondary text-light rounded-4 shadow-lg text-start">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-cyan"><i class="fa-solid fa-bullhorn me-2"></i>Cadastrar Nova Ação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="/index.php?url=admin/cadastrarAcao" method="POST" enctype="multipart/form-data">
                    <div class="modal-body row g-3">
                        <div class="col-12">
                            <label class="form-label text-white small">Título da Ação:</label>
                            <input type="text" name="titulo" class="form-control bg-dark text-light border-secondary" required placeholder="Ex: Campanha contra a fome">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-white small">Foto de Capa (Arquivo local):</label>
                            <input type="file" name="imagem" class="form-control bg-dark text-light border-secondary" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-white small">Descrição Detalhada:</label>
                            <textarea name="descricao" class="form-control bg-dark text-light border-secondary" rows="3" required placeholder="Explique os detalhes da ação..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-cyan-gradient btn-sm px-4 fw-bold">Salvar Ação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>