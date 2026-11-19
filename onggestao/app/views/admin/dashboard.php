<!DOCTYPE html>
<?php
use core\database\DBConnection;
// Buscar o número de beneficiários pendentes para exibir na notificação
$beneficiariosPendentes = []; // Inicializa a variável como um array vazio para evitar erros caso a consulta falhe
// Tenta buscar os beneficiários pendentes do banco de dados
try {
    $db = (new DBConnection())->getConn();
    $beneficiariosPendentes = $db->query("SELECT * FROM usuarios WHERE tipo = 'beneficiario' AND (status = 'pendente' OR status = 'Em Análise') ORDER BY id DESC")->fetchAll();
} catch (Exception $e) {
    $beneficiariosPendentes = [];
}
$totalPendentes = count($beneficiariosPendentes); // Conta o total de beneficiários pendentes para exibir na notificação
?>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Gestão ONG</title>
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

    <!-- Conteúdo Principal -->
    <div class="container my-5">
        
        <!-- Cabeçalho com Título e Botões de Ação Agrupados -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-white mb-1">Gestão de Famílias</h1>
                <p class="text-muted small mb-0">Visualize, pesquise e cadastre as famílias assistidas pela ONG.</p>
            </div>
            
            <!-- Botões Lado a Lado Próximos -->
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-cyan-gradient btn-sm fw-bold px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalNovaFamilia">
                    <i class="fa-solid fa-plus me-1"></i> Nova Família
                </button>
                <button class="btn btn-dark-outline btn-sm fw-bold text-cyan px-3 py-2 border-cyan" data-bs-toggle="modal" data-bs-target="#modalNovoAdmin" style="border-color: rgba(45, 192, 255, 0.4);">
                    <i class="fa-solid fa-user-shield me-1"></i> Novo Administrador
                </button>
            </div>
        </div>

        <!-- Abas de Navegação -->
        <div class="d-flex gap-2 mb-4">
            <a href="<?php echo $_SESSION['url']['root']; ?>admin/dashboard" class="btn btn-cyan-gradient px-4 py-2 btn-sm">FAMÍLIAS</a>
            <a href="<?php echo $_SESSION['url']['root']; ?>solicitacao/index" class="btn btn-dark-outline px-4 py-2 btn-sm position-relative">
                PEDIDOS
                <?php if (isset($pedidosPendentesCount) && $pedidosPendentesCount > 0): ?>
                    <span class="position-absolute translate-middle rounded-circle border border-white shadow-sm" style="top: 2px; left: 95%; width: 12px; height: 12px; background-color: #2dc0ff;"></span>
                <?php endif; ?>
            </a>
            <a href="<?php echo $_SESSION['url']['root']; ?>doacao/index" class="btn btn-dark-outline px-4 py-2 btn-sm">DOAÇÕES</a>
            <a href="<?php echo $_SESSION['url']['root']; ?>admin/acoes" class="btn btn-dark-outline px-4 py-2 btn-sm">AÇÕES</a>
        </div>

        <!-- Campo de Pesquisa por Nome (Busca em Tempo Real) -->
        <div class="mb-4">
            <div class="input-group" style="max-width: 450px;">
                <span class="input-group-text bg-dark border-secondary text-muted"><i class="fa-solid fa-search"></i></span>
                <input type="text" id="inputPesquisa" class="form-control bg-dark text-light border-secondary py-2" placeholder="Pesquisar por nome do responsável..." onkeyup="filtrarTabela()">
            </div>
        </div>

        <!-- Tabela Carregada com Todas as Famílias -->
        <div class="card-impact p-0 overflow-hidden text-start shadow-lg" style="border-radius: 16px;">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle" id="tabelaFamilias">
                    <thead class="table-secondary bg-opacity-10 text-muted small">
                        <tr>
                            <th class="py-3 ps-4">RESPONSÁVEL</th>
                            <th>CPF</th>
                            <th>TELEFONE</th>
                            <th>DEPENDENTES</th>
                            <th>RENDA MENSAL</th>
                            <th>STATUS</th>
                            <th class="text-end pe-4">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($familias)): ?> <!-- Se não houver famílias cadastradas, exibe uma mensagem -->
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">Nenhuma família cadastrada até o momento.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($familias as $f): ?> <!-- Loop para exibir cada família na tabela -->
                                <tr>
                                    <td class="ps-4 fw-bold text-white"><?php echo htmlspecialchars($f['nome_responsavel']); ?></td>
                                    <td><?php echo htmlspecialchars($f['cpf']); ?></td>
                                    <td><?php echo htmlspecialchars($f['telefone']); ?></td>
                                    <td><?php echo htmlspecialchars($f['quantidade_membros']); ?></td>
                                    <td class="text-cyan fw-bold">R$ <?php echo number_format($f['renda_familiar'], 2, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge bg-success text-dark">Ativo</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <!-- Botão Editar (Abre Modal com dados preenchidos) -->
                                        <button class="btn btn-sm btn-dark-outline py-1 px-2 text-cyan" title="Editar Informações" data-bs-toggle="modal" data-bs-target="#modalEditarFamilia<?php echo $f['id']; ?>">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <!-- Botão Excluir (Abre Modal com justificativa) -->
                                        <button class="btn btn-sm btn-dark-outline py-1 px-2 text-danger" title="Excluir Registro" data-bs-toggle="modal" data-bs-target="#modalExcluirFamilia<?php echo $f['id']; ?>">
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


        <!-- Modal Novo Administrador -->
        <div class="modal fade" id="modalNovoAdmin" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content bg-black border border-secondary text-light rounded-4 shadow-lg text-start">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title fw-bold text-cyan"><i class="fa-solid fa-user-shield me-2"></i>Cadastrar Novo Administrador</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?php echo $_SESSION['url']['root']; ?>admin/cadastrarAdmin" method="POST">
                        <div class="modal-body row g-3">
                            <div class="col-12">
                                <label class="form-label text-white small">Nome Completo:</label>
                                <input type="text" name="nome" class="form-control bg-dark text-light border-secondary" required placeholder="Nome do Administrador">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-white small">E-mail de Acesso:</label>
                                <input type="email" name="email" class="form-control bg-dark text-light border-secondary" required placeholder="admin@ong.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-white small">Senha:</label>
                                <input type="password" name="senha" class="form-control bg-dark text-light border-secondary" required placeholder="******">
                            </div>
                        </div>
                        <div class="modal-footer border-secondary">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-cyan-gradient btn-sm px-4 fw-bold">Criar Administrador</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <?php foreach ($familias as $f): ?> <!-- Loop para criar modais de edição e exclusão para cada família -->
            <!-- Modal de Edição -->
            <div class="modal fade" id="modalEditarFamilia<?php echo $f['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content bg-black border border-secondary text-light rounded-4 shadow-lg text-start">
                        <div class="modal-header border-secondary">
                            <h5 class="modal-title fw-bold text-cyan"><i class="fa-solid fa-pen-to-square me-2"></i>Editar Família / Beneficiário</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="<?php echo $_SESSION['url']['root']; ?>familia/atualizar" method="POST">
                            <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
                            <div class="modal-body row g-3">
                                <div class="col-md-12">
                                    <label class="form-label text-white small">Nome do Responsável</label>
                                    <input type="text" name="nome_responsavel" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($f['nome_responsavel'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white small">E-mail</label>
                                    <input type="email" name="email" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($f['email'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white small">CPF</label>
                                    <input type="text" name="cpf" class="form-control bg-dark text-light border-secondary mask-cpf" maxlength="14" value="<?php echo htmlspecialchars($f['cpf'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white small">Telefone</label>
                                    <input type="text" name="telefone" class="form-control bg-dark text-light border-secondary mask-telefone" maxlength="19" value="<?php echo htmlspecialchars($f['telefone'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-white small">Membros</label>
                                    <input type="number" name="quantidade_membros" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($f['quantidade_membros'] ?? 1); ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-white small">Renda (R$)</label>
                                    <input type="text" name="renda_familiar" class="form-control bg-dark text-light border-secondary mask-renda" value="<?php echo htmlspecialchars($f['renda_familiar'] ?? ''); ?>" placeholder="0,00" required>
                                </div>
            
                                <!-- Campos de Endereço Separados + ViaCEP -->
                                <div class="col-md-4">
                                    <label class="form-label text-white small">CEP</label>
                                    <input type="text" name="cep" class="form-control bg-dark text-light border-secondary mask-cep" maxlength="9" placeholder="00000-000" value="<?php echo htmlspecialchars($f['cep'] ?? ''); ?>" onblur="consultarCepModal(this)">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white small">Rua / Logradouro</label>
                                    <input type="text" name="rua" class="form-control bg-dark text-light border-secondary campo-rua" value="<?php echo htmlspecialchars($f['rua'] ?? ''); ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-white small">Número</label>
                                    <input type="text" name="numero" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($f['numero'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white small">Bairro</label>
                                    <input type="text" name="bairro" class="form-control bg-dark text-light border-secondary campo-bairro" value="<?php echo htmlspecialchars($f['bairro'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white small">Cidade / Estado</label>
                                    <input type="text" name="cidade" class="form-control bg-dark text-light border-secondary campo-cidade" value="<?php echo htmlspecialchars($f['cidade'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="modal-footer border-secondary">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-cyan-gradient btn-sm px-4 fw-bold">Salvar Alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal de Exclusão com Justificativa -->
            <div class="modal fade" id="modalExcluirFamilia<?php echo $f['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content bg-black border border-secondary text-light rounded-4 shadow-lg text-start">
                        <div class="modal-header border-secondary">
                            <h5 class="modal-title fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Excluir Registro</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="<?php echo $_SESSION['url']['root']; ?>familia/excluir" method="POST">
                            <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
                            <div class="modal-body">
                                <p class="text-white small">Tem certeza que deseja remover o cadastro de <b class="text-cyan"><?php echo htmlspecialchars($f['nome_responsavel']); ?></b>?</p>
                                <div class="mb-3">
                                    <label class="form-label text-white small">Justificativa da Exclusão *</label>
                                    <input type="text" name="justificativa" class="form-control bg-dark text-light border-secondary" required placeholder="Digite o motivo da remoção...">
                                </div>
                            </div>
                            <div class="modal-footer border-secondary">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold">Confirmar Exclusão</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>

    <!-- Modal para Cadastrar Nova Família -->
    <div class="modal fade" id="modalNovaFamilia" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-black border border-secondary text-light rounded-4 shadow-lg">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-cyan"><i class="fa-solid fa-house-chimney-user me-2"></i>Cadastrar Nova Família</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo $_SESSION['url']['root']; ?>familia/cadastrar" method="POST">
                    <div class="modal-body row g-3">
                        <div class="col-md-12">
                            <label class="form-label text-white small">Nome do Responsável</label>
                            <input type="text" name="nome_responsavel" class="form-control bg-dark text-light border-secondary" required placeholder="Nome completo">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-white small">E-mail (para futuros comunicados)</label>
                            <input type="email" name="email" class="form-control bg-dark text-light border-secondary" placeholder="exemplo@email.com">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-white small">CPF</label>
                            <input type="text" name="cpf" id="cpf" class="form-control bg-dark text-light border-secondary mask-cpf" required placeholder="000.000.000-00" maxlength="14">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-white small">Telefone / WhatsApp</label>
                            <input type="text" name="telefone" class="form-control bg-dark text-light border-secondary mask-telefone" placeholder="+55 (11) 97072-5000" maxlength="19" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-white small">Quantidade de Membros</label>
                            <input type="number" name="quantidade_membros" class="form-control bg-dark text-light border-secondary" required placeholder="Ex: 4">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-white small">Renda Familiar (R$)</label>
                            <input type="text" name="renda_familiar" class="form-control bg-dark text-light border-secondary mask-renda" required placeholder="Ex: 1.200,00">
                        </div>

                        <!-- Campos de Endereço via ViaCEP -->
                        <div class="col-md-4">
                            <label class="form-label text-white small">CEP</label>
                            <input type="text" name="cep" id="cep" class="form-control bg-dark text-light border-secondary mask-cep" placeholder="00000-000" maxlength="9" onblur="consultarCep(this.value)">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-white small">Rua / Logradouro</label>
                            <input type="text" name="rua" id="rua" class="form-control bg-dark text-light border-secondary" placeholder="Nome da rua">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label text-white small">Número</label>
                            <input type="text" name="numero" class="form-control bg-dark text-light border-secondary" placeholder="Nº">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-white small">Bairro</label>
                            <input type="text" name="bairro" id="bairro" class="form-control bg-dark text-light border-secondary" placeholder="Bairro">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-white small">Cidade / Estado</label>
                            <input type="text" name="cidade" id="cidade" class="form-control bg-dark text-light border-secondary" placeholder="Cidade - UF">
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-cyan-gradient btn-sm px-4 fw-bold">Salvar Família</button>
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
                                        <th>DEPENDENTES</th>
                                        <th>DATA</th>
                                        <th class="text-end pe-3">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($beneficiariosPendentes as $bp): ?>
                                        <?php 
                                            $dbConn = (new DBConnection())->getConn();
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
    <script src="<?php echo $_SESSION['url']['root']; ?>public/assets/js/mascaras.js"></script>

   <!-- Script de Consulta de CEP -->
   <script>
        // Função para filtrar a tabela de famílias com base no input de pesquisa
        function filtrarTabela() {
            let input = document.getElementById("inputPesquisa").value.toLowerCase();
            let rows = document.querySelectorAll("#tabelaFamilias tbody tr");
            rows.forEach(row => {
                let texto = row.children[0]?.textContent.toLowerCase() || "";
                row.style.display = texto.includes(input) ? "" : "none";
            });
        }

        // Consulta ViaCEP para o modal de Nova Família (por ID)
        function consultarCep(cep) {
            let valorLimpo = cep.replace(/\D/g, '');
            if (valorLimpo.length === 8) {
                fetch(`https://viacep.com.br/ws/${valorLimpo}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        // Preenche os campos de endereço com os dados retornados pelo ViaCEP
                        if (!data.erro) {
                            document.getElementById('rua').value = data.logradouro || '';
                            document.getElementById('bairro').value = data.bairro || '';
                            document.getElementById('cidade').value = (data.localidade && data.uf) ? `${data.localidade} - ${data.uf}` : '';
                        } else {
                            alert("CEP não encontrado.");
                        }
                    })
                    .catch(error => console.error('Erro ao buscar CEP:', error));
            }
        }

        // Consulta ViaCEP isolada para cada Modal de Edição (evita conflito de IDs repetidos)
        function consultarCepModal(inputCep) {
            let valorLimpo = inputCep.value.replace(/\D/g, '');
            let form = inputCep.closest('form');
            if (valorLimpo.length === 8 && form) {
                fetch(`https://viacep.com.br/ws/${valorLimpo}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            form.querySelector('.campo-rua').value = data.logradouro || '';
                            form.querySelector('.campo-bairro').value = data.bairro || '';
                            form.querySelector('.campo-cidade').value = (data.localidade && data.uf) ? `${data.localidade} - ${data.uf}` : '';
                        } else {
                            alert("CEP não encontrado.");
                        }
                    })
                    .catch(error => console.error('Erro ao buscar CEP:', error));
            }
        }
    </script>
</body>
</html>