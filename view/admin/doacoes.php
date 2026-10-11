<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Doações - Gestão ONG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-dark text-light">

    <!-- Topbar -->
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
            <a href="/index.php?url=admin/dashboard" class="btn btn-dark-outline px-4 py-2 btn-sm">FAMÍLIAS</a>
            <a href="/index.php?url=solicitacao/index" class="btn btn-dark-outline px-4 py-2 btn-sm">PEDIDOS</a>
            <a href="/index.php?url=doacao/index" class="btn btn-cyan-gradient px-4 py-2 btn-sm">DOAÇÕES</a>
            <a href="/index.php?url=admin/acoes" class="btn btn-dark-outline px-4 py-2 btn-sm">AÇÕES</a>
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

    <!-- Modal Nova Doação -->
    <div class="modal fade" id="modalNovaDoacao" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-black border border-secondary text-light rounded-4 shadow-lg">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-cyan"><i class="fa-solid fa-hand-holding-dollar me-2"></i>Registrar Nova Doação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="/index.php?url=doacao/cadastrar" method="POST">
                    <div class="modal-body row g-3">
                        <div class="col-12">
                            <label class="form-label text-muted small">Nome do Doador (ou Deixe "Anônimo")</label>
                            <input type="text" name="doador_nome" class="form-control bg-dark text-light border-secondary" placeholder="Anônimo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Tipo de Doação</label>
                            <select name="tipo_doacao" class="form-select bg-dark text-light border-secondary">
                                <option value="Alimento / Cesta Básica">Alimento / Cesta Básica</option>
                                <option value="Financeiro">Financeiro</option>
                                <option value="Roupas / Agasalhos">Roupas / Agasalhos</option>
                                <option value="Medicamento">Medicamento</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Quantidade ou Valor</label>
                            <input type="text" name="quantidade_ou_valor" class="form-control bg-dark text-light border-secondary" required placeholder="Ex: 5 cestas ou R$ 150,00">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Descrição / Observações</label>
                            <textarea name="descricao" class="form-control bg-dark text-light border-secondary" rows="2" placeholder="Detalhes opcionais..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-cyan-gradient btn-sm px-4 fw-bold">Salvar Doação</button>
                    </div>
                </form>
            </div>
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
                    <form action="/index.php?url=admin/cadastrarAdmin" method="POST">
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

        <!-- Modal para Cadastrar Nova Família -->
    <div class="modal fade" id="modalNovaFamilia" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-black border border-secondary text-light rounded-4 shadow-lg">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-cyan"><i class="fa-solid fa-house-chimney-user me-2"></i>Cadastrar Nova Família</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="/index.php?url=familia/cadastrar" method="POST">
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
                            <input type="text" name="cpf" id="cpf" class="form-control bg-dark text-light border-secondary" required placeholder="000.000.000-00" maxlength="14">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-white small">Telefone / WhatsApp</label>
                            <input type="text" name="telefone" id="telefone" class="form-control bg-dark text-light border-secondary" required placeholder="(00) 00000-0000" maxlength="15">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-white small">Quantidade de Membros</label>
                            <input type="number" name="quantidade_membros" class="form-control bg-dark text-light border-secondary" required placeholder="Ex: 4">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-white small">Renda Familiar (R$)</label>
                            <input type="text" name="renda_familiar" class="form-control bg-dark text-light border-secondary" required placeholder="Ex: 1200.00">
                        </div>

                        <!-- Campos de Endereço via ViaCEP -->
                        <div class="col-md-4">
                            <label class="form-label text-white small">CEP</label>
                            <input type="text" name="cep" id="cep" class="form-control bg-dark text-light border-secondary" placeholder="00000-000" maxlength="9" onblur="consultarCep(this.value)">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script para filtrar doações -->
    <script>
        function filtrarDoacoes() {
            let input = document.getElementById("inputPesquisaDoacoes").value.toLowerCase();
            let rows = document.querySelectorAll("table tbody tr");
            rows.forEach(row => {
                let texto = row.children[0]?.textContent.toLowerCase() || "";
                row.style.display = texto.includes(input) ? "" : "none";
            });
        }
    </script>
</body>
</html>