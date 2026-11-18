<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Famílias - Gestão ONG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" href="assets/img/logo.png" type="image/png">
</head>
<body class="bg-dark text-light">

    <!-- Topbar -->
    <nav class="navbar navbar-dark bg-black border-bottom border-secondary border-opacity-25 py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold text-white fs-5" href="<?php echo $_SESSION['url']['root']; ?>admin/dashboard">
                <i class="fa-solid fa-gauge-high me-2 text-cyan"></i>PAINEL <span class="text-cyan">ADMINISTRATIVO</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <!-- Botão de Sino Ajustado (Menor, Perfeitamente Circular e Bolinha Azul) -->
                <button class="btn btn-dark-outline position-relative border-secondary text-light p-0 d-flex align-items-center justify-content-center shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNotificacoes" title="Cadastros Pendentes" style="width: 36px; height: 36px; border-radius: 50%;">
                    <i class="fa-solid fa-bell text-cyan" style="font-size: 14px;"></i>
                    <?php if ($totalPendentes > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle border border-white rounded-circle shadow-sm" style="width: 13px; height: 13px; background-color: #2dc0ff;">
                            <span class="visually-hidden">Notificações pendentes</span>
                        </span>
                    <?php endif; ?>
                </button>

                <span class="text-muted small">Olá, <b class="text-white"><?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Admin'); ?></b></span>
                <a href="<?php echo $_SESSION['url']['root']; ?>auth/logout" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-semibold">
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

    <!-- Conteúdo Principal-->
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-white mb-1"><i class="fa-solid fa-users me-2 text-success"></i>Gestão de Famílias</h1>
                <p class="text-muted small mb-0">Cadastre e acompanhe as famílias assistidas pela ONG.</p>
            </div>
            <button class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#modalNovaFamilia">
                <i class="fa-solid fa-plus me-1"></i> Nova Família
            </button>
        </div>

        <!-- Tabela de Famílias -->
        <div class="card bg-black border border-secondary shadow">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead class="table-secondary bg-opacity-10 text-muted small">
                            <tr>
                                <th class="py-3 ps-4">Responsável</th>
                                <th>CPF</th>
                                <th>Telefone</th>
                                <th>Membros</th>
                                <th>Renda</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($familias)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Nenhuma família cadastrada até o momento.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($familias as $f): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold"><?php echo htmlspecialchars($f['nome_responsavel']); ?></td>
                                        <td><?php echo htmlspecialchars($f['cpf']); ?></td>
                                        <td><?php echo htmlspecialchars($f['telefone']); ?></td>
                                        <td><?php echo htmlspecialchars($f['quantidade_membros']); ?></td>
                                        <td>R$ <?php echo number_format($f['renda_familiar'], 2, ',', '.'); ?></td>
                                        <td>
                                            <?php if ($f['status'] == 'ativo'): ?>
                                                <span class="badge bg-success">Ativo</span>
                                            <?php elseif ($f['status'] == 'analise'): ?>
                                                <span class="badge bg-warning text-dark">Em Análise</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inativo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-outline-light" title="Detalhes"><i class="fa-solid fa-eye"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nova Família -->
    <div class="modal fade" id="modalNovaFamilia" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-black border border-secondary text-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-success"><i class="fa-solid fa-user-plus me-2"></i>Cadastrar Nova Família</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="/index.php?url=familia/cadastrar" method="POST">
                    <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nome do Responsável</label>
                            <input type="text" name="nome_responsavel" class="form-control bg-dark text-light border-secondary" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">CPF</label>
                            <input type="text" name="cpf" class="form-control bg-dark text-light border-secondary" required placeholder="000.000.000-00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Telefone / WhatsApp</label>
                            <input type="text" name="telefone" class="form-control bg-dark text-light border-secondary" placeholder="(11) 99999-9999">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">Qtd. Membros</label>
                            <input type="number" name="quantidade_membros" class="form-control bg-dark text-light border-secondary" value="1" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">Renda Familiar (R$)</label>
                            <input type="number" step="0.01" name="renda_familiar" class="form-control bg-dark text-light border-secondary" placeholder="0.00">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Endereço Completo</label>
                            <textarea name="endereco" class="form-control bg-dark text-light border-secondary" rows="2" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Status do Cadastro</label>
                            <select name="status" class="form-select bg-dark text-light border-secondary">
                                <option value="analise">Em Análise</option>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success btn-sm px-4 fw-bold">Salvar Família</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal de Notificações / Aprovação de Beneficiários -->
    <div class="modal fade" id="modalNotificacoes" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content bg-black border border-secondary text-light rounded-4 shadow-lg text-start">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-cyan"><i class="fa-solid fa-bell me-2"></i>Cadastros de Beneficiários Pendentes (Análise)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if (empty($beneficiariosPendentes)): ?>
                        <p class="text-muted text-center py-4 mb-0">Nenhuma solicitação de cadastro pendente no momento.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0 align-middle">
                                <thead class="table-secondary bg-opacity-10 text-muted small">
                                    <tr>
                                        <th class="ps-3">SOLICITANTE</th>
                                        <th>CONTATO / CPF</th>
                                        <th>ENDEREÇO</th>
                                        <th>RENDA</th>
                                        <th>DATA</th>
                                        <th class="text-end pe-3">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($beneficiariosPendentes as $bp): ?>
                                        <?php 
                                            // Busca dados detalhados da família vinculada a este usuário pendente
                                            $db = (new DBConnection())->getConn();
                                            $stmtFam = $db->prepare("SELECT * FROM familias WHERE responsavel_id = :uid LIMIT 1");
                                            $stmtFam->execute([':uid' => $bp['id']]);
                                            $famData = $stmtFam->fetch();
                                        ?>
                                        <tr>
                                            <td class="ps-3 fw-bold text-white">
                                                <?php echo htmlspecialchars($bp['nome']); ?><br>
                                                <small class="text-muted fw-normal"><?php echo htmlspecialchars($bp['email']); ?></small>
                                            </td>
                                            <td>
                                                <small class="text-light">CPF: <?php echo htmlspecialchars($famData['cpf'] ?? 'Não informado'); ?></small><br>
                                                <small class="text-muted">Tel: <?php echo htmlspecialchars($famData['telefone'] ?? 'Não informado'); ?></small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php 
                                                        if ($famData) {
                                                            echo htmlspecialchars(($famData['rua'] ?? '') . ', ' . ($famData['numero'] ?? '') . ' - ' . ($famData['bairro'] ?? '') . ' (' . ($famData['cidade'] ?? '') . ')');
                                                        } else {
                                                            echo "Endereço não preenchido";
                                                        }
                                                    ?>
                                                </small>
                                            </td>
                                            <td class="fw-bold text-success">
                                                R$ <?php echo number_format($famData['renda_familiar'] ?? 0, 2, ',', '.'); ?>
                                            </td>
                                            <td class="text-muted small"><?php echo date('d/m/Y H:i', strtotime($bp['criado_em'] ?? 'now')); ?></td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <form action="<?php echo $_SESSION['url']['root']; ?>admin/aprovarBeneficiario" method="POST" class="d-inline">
                                                        <input type="hidden" name="id" value="<?php echo $bp['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-success px-2 py-1" title="Aprovar Cadastro">
                                                            <i class="fa-solid fa-check"></i> Aprovar
                                                        </button>
                                                    </form>
                                                    <form action="<?php echo $_SESSION['url']['root']; ?>admin/rejeitarBeneficiario" method="POST" class="d-inline">
                                                        <input type="hidden" name="id" value="<?php echo $bp['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger px-2 py-1" title="Rejeitar e Remover">
                                                            <i class="fa-solid fa-xmark"></i> Rejeitar
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>