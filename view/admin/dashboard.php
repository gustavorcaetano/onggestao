<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Gestão ONG</title>
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
            <a href="/index.php?url=admin/dashboard" class="btn btn-cyan-gradient px-4 py-2 btn-sm">FAMÍLIAS</a>
            <a href="/index.php?url=solicitacao/index" class="btn btn-dark-outline px-4 py-2 btn-sm">PEDIDOS</a>
            <a href="/index.php?url=doacao/index" class="btn btn-dark-outline px-4 py-2 btn-sm">DOAÇÕES</a>
            <a href="/index.php?url=admin/acoes" class="btn btn-dark-outline px-4 py-2 btn-sm">AÇÕES</a>
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
                        <?php if (empty($familias)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">Nenhuma família cadastrada até o momento.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($familias as $f): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-white"><?php echo htmlspecialchars($f['nome_responsavel']); ?></td>
                                    <td><?php echo htmlspecialchars($f['cpf']); ?></td>
                                    <td><?php echo htmlspecialchars($f['telefone']); ?></td>
                                    <td><?php echo htmlspecialchars($f['quantidade_membros']); ?></td>
                                    <td class="text-cyan fw-bold">R$ <?php echo number_format($f['renda_familiar'], 2, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge bg-warning text-dark">Em Análise</span>
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


        <?php foreach ($familias as $f): ?>
            <!-- Modal de Edição -->
            <div class="modal fade" id="modalEditarFamilia<?php echo $f['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content bg-black border border-secondary text-light rounded-4 shadow-lg text-start">
                        <div class="modal-header border-secondary">
                            <h5 class="modal-title fw-bold text-cyan"><i class="fa-solid fa-pen-to-square me-2"></i>Editar Família / Beneficiário</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="/index.php?url=familia/atualizar" method="POST">
                            <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
                            <div class="modal-body row g-3">
                                <div class="col-md-12">
                                    <label class="form-label text-white small">Nome do Responsável</label>
                                    <input type="text" name="nome_responsavel" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($f['nome_responsavel']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white small">E-mail</label>
                                    <input type="email" name="email" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($f['email'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white small">CPF</label>
                                    <input type="text" name="cpf" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($f['cpf']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white small">Telefone</label>
                                    <input type="text" name="telefone" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($f['telefone']); ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-white small">Membros</label>
                                    <input type="number" name="quantidade_membros" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($f['quantidade_membros']); ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-white small">Renda (R$)</label>
                                    <input type="text" name="renda_familiar" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($f['renda_familiar']); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-white small">Endereço</label>
                                    <textarea name="endereco" class="form-control bg-dark text-light border-secondary" rows="2" required><?php echo htmlspecialchars($f['endereco']); ?></textarea>
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
                        <form action="/index.php?url=familia/excluir" method="POST">
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

    <!-- Script de Filtro por Nome em Tempo Real -->
    <script>
        function filtrarTabela() {
            let input = document.getElementById("inputPesquisa").value.toLowerCase();
            let rows = document.querySelectorAll("#tabelaFamilias tbody tr");
            rows.forEach(row => {
                let texto = row.children[0]?.textContent.toLowerCase() || "";
                row.style.display = texto.includes(input) ? "" : "none";
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script de Máscaras e Consulta de CEP -->
    <script>
        // Função para aplicar máscaras nos inputs
        function aplicarMascara(input, tipo) {
            let v = input.value.replace(/\D/g, "");
            if (tipo === 'cpf') {
                v = v.replace(/(\d{3})(\d)/, "$1.$2");
                v = v.replace(/(\d{3})(\d)/, "$1.$2");
                v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
            } else if (tipo === 'telefone') {
                v = v.replace(/^(\d{2})(\d)/g, "($1) $2");
                v = v.replace(/(\d)(\d{4})$/, "$1-$2");
            } else if (tipo === 'cep') {
                v = v.replace(/^(\d{5})(\d)/, "$1-$2");
            }
            input.value = v;
        }

        // Atribuir eventos de máscara
        document.getElementById('cpf').addEventListener('input', function() { aplicarMascara(this, 'cpf'); });
        document.getElementById('telefone').addEventListener('input', function() { aplicarMascara(this, 'telefone'); });
        document.getElementById('cep').addEventListener('input', function() { aplicarMascara(this, 'cep'); });

        // Consulta automática ViaCEP
        function consultarCep(cep) {
            let valorLimpo = cep.replace(/\D/g, '');
            if (valorLimpo.length === 8) {
                fetch(`https://viacep.com.br/ws/${valorLimpo}/json/`)
                    .then(response => response.json())
                    .then(data => {
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
    </script>
</body>
</html>