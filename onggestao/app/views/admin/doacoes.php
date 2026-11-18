<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Doações - Gestão ONG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $_SESSION['url']['root']; ?>public/assets/css/style.css">
    <link rel="icon" href="<?php echo $_SESSION['url']['root']; ?>public/assets/img/logo.png" type="image/png">
</head>
<body class="bg-dark text-light">

    <!-- Topbar -->
    <nav class="navbar navbar-dark bg-black border-bottom border-secondary border-opacity-25 py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold text-white fs-5" href="<?php echo $_SESSION['url']['root']; ?>admin/dashboard">
                <i class="fa-solid fa-gauge-high me-2 text-cyan"></i>PAINEL <span class="text-cyan">ADMINISTRATIVO</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <!-- Botão de Sino -->
                <button class="btn btn-dark-outline position-relative border-secondary text-light p-0 d-flex align-items-center justify-content-center shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNotificacoes" title="Cadastros Pendentes" style="width: 36px; height: 36px; border-radius: 50%;">
                    <i class="fa-solid fa-bell text-cyan" style="font-size: 14px;"></i>
                    <?php if (isset($totalPendentes) && $totalPendentes > 0): ?>
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

    <!-- Conteúdo Principal -->
    <div class="container my-5">
        
        <!-- Cabeçalho -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-white mb-1">Gestão de Doações</h1>
                <p class="text-muted small mb-0">Visualize e pesquise as doações recebidas pela ONG.</p>
            </div>
        </div>

        <!-- Abas de Navegação -->
        <div class="d-flex gap-2 mb-4">
            <a href="<?php echo $_SESSION['url']['root']; ?>admin/dashboard" class="btn btn-dark-outline px-4 py-2 btn-sm">FAMÍLIAS</a>
            <a href="<?php echo $_SESSION['url']['root']; ?>solicitacao/index" class="btn btn-dark-outline px-4 py-2 btn-sm position-relative">
                PEDIDOS
                <?php if (isset($pedidosPendentesCount) && $pedidosPendentesCount > 0): ?>
                    <span class="position-absolute translate-middle rounded-circle border border-white shadow-sm" style="top: 2px; left: 95%; width: 12px; height: 12px; background-color: #2dc0ff;"></span>
                <?php endif; ?>
            </a>
            <a href="<?php echo $_SESSION['url']['root']; ?>doacao/index" class="btn btn-cyan-gradient px-4 py-2 btn-sm">DOAÇÕES</a>
            <a href="<?php echo $_SESSION['url']['root']; ?>admin/acoes" class="btn btn-dark-outline px-4 py-2 btn-sm">AÇÕES</a>
        </div>

        <div class="mb-4">
            <div class="input-group" style="max-width: 450px;">
                <span class="input-group-text bg-dark border-secondary text-muted"><i class="fa-solid fa-search"></i></span>
                <input type="text" id="inputPesquisaDoacoes" class="form-control bg-dark text-light border-secondary py-2" placeholder="Pesquisar por doador..." onkeyup="filtrarDoacoes()">
            </div>
        </div>

        <!-- Tabela -->
        <div class="card-impact p-0 overflow-hidden text-start shadow-lg" style="border-radius: 16px;" id="tabelaDoacoes">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead class="table-secondary bg-opacity-10 text-muted small">
                        <tr>
                            <th class="py-3 ps-4">DOADOR</th>
                            <th>TIPO DE DOAÇÃO</th>
                            <th>QTD / VALOR</th>
                            <th>DESCRIÇÃO</th>
                            <th>DATA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($doacoes)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Nenhuma doação registrada até o momento.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($doacoes as $d): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-white"><?php echo htmlspecialchars($d['doador_nome']); ?></td>
                                    <td><span class="badge bg-secondary border border-secondary"><?php echo htmlspecialchars($d['tipo_doacao']); ?></span></td>
                                    <td class="text-cyan fw-bold"><?php echo htmlspecialchars($d['quantidade_ou_valor']); ?></td>
                                    <td><?php echo htmlspecialchars($d['descricao']); ?></td>
                                    <td class="text-muted small"><?php echo date('d/m/Y H:i', strtotime($d['data_doacao'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
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
                                        <th>DEPENDENTES</th>
                                        <th>DATA</th>
                                        <th class="text-end pe-3">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($beneficiariosPendentes as $bp): ?>
                                        <?php 
                                            $dbConn = (new \core\database\DBConnection())->getConn();
                                            $stmtFam = $dbConn->prepare("SELECT * FROM familias WHERE responsavel_id = :uid LIMIT 1");
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
                                            <td class="text-center fw-bold text-light">
                                                <?php echo htmlspecialchars($famData['quantidade_membros'] ?? 1); ?>
                                            </td>
                                            <td class="text-muted small"><?php echo date('d/m/Y H:i', strtotime($bp['criado_em'] ?? 'now')); ?></td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <form action="<?php echo $_SESSION['url']['root']; ?>admin/aprovarBeneficiario" method="POST" class="d-inline">
                                                        <input type="hidden" name="id" value="<?php echo $bp['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-success px-2 py-1" title="Aprovar">
                                                            <i class="fa-solid fa-check"></i> Aprovar
                                                        </button>
                                                    </form>
                                                    <form action="<?php echo $_SESSION['url']['root']; ?>admin/rejeitarBeneficiario" method="POST" class="d-inline">
                                                        <input type="hidden" name="id" value="<?php echo $bp['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Rejeitar">
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
    <script>
        function filtrarDoacoes() {
            let input = document.getElementById("inputPesquisaDoacoes").value.toLowerCase();
            let rows = document.querySelectorAll("#tabelaDoacoes tbody tr");
            rows.forEach(row => {
                let texto = row.children[0]?.textContent.toLowerCase() || "";
                row.style.display = texto.includes(input) ? "" : "none";
            });
        }
    </script>
    
    <!-- Bootstrap JS Bundle (Essencial para abrir modais) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>