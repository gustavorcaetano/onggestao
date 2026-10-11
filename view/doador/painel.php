<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Doador - Gestão ONG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-dark text-light">

    <!-- Topbar -->
    <nav class="navbar navbar-dark bg-black border-bottom border-secondary border-opacity-25 py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold text-white fs-5" href="#">
                <i class="fa-solid fa-heart me-2 text-cyan"></i>ÁREA DO <span class="text-cyan">DOADOR</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">Olá, <b class="text-white"><?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Doador'); ?></b></span>
                <a href="/index.php?url=auth/logout" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-semibold">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container my-5">

        <?php if (!empty($_GET['sucesso'])): ?>
            <div class="alert alert-success bg-dark border-success text-success alert-dismissible fade show p-3 rounded-3 shadow-lg mb-4" role="alert" style="backdrop-filter: blur(8px);">
                <i class="fa-solid fa-heart me-2 text-cyan"></i><strong>Muito Obrigado!</strong> <?php echo htmlspecialchars($_GET['sucesso']); ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Cabeçalho e Botão de Nova Doação -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-black text-white mb-1">Meu Histórico de Doações</h1>
                <p class="text-muted small mb-0">Acompanhe seu impacto e contribua com novas doações para as famílias assistidas.</p>
            </div>
            <button class="btn btn-cyan-gradient btn-sm fw-bold px-4" data-bs-toggle="modal" data-bs-target="#modalFazerDoacao">
                <i class="fa-solid fa-hand-holding-dollar me-1"></i> Fazer Nova Doação
            </button>
        </div>

        <!-- Tabela do Histórico de Doações -->
        <div class="card-impact p-0 overflow-hidden text-start shadow-lg mb-5" style="border-radius: 16px;">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead class="table-secondary bg-opacity-10 text-muted small">
                        <tr>
                            <th class="py-3 ps-4">TIPO DE DOAÇÃO</th>
                            <th>QUANTIDADE / VALOR</th>
                            <th>OBSERVAÇÃO / DESCRIÇÃO</th>
                            <th class="text-end pe-4">DATA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($doacoes)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Você ainda não registrou doações em seu nome. Clique acima para doar!</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($doacoes as $d): ?>
                                <tr>
                                    <td class="ps-4"><span class="badge bg-secondary border border-secondary"><?php echo htmlspecialchars($d['tipo_doacao']); ?></span></td>
                                    <td class="text-cyan fw-bold"><?php echo htmlspecialchars($d['quantidade_ou_valor']); ?></td>
                                    <td><?php echo htmlspecialchars($d['descricao']); ?></td>
                                    <td class="text-end pe-4 text-muted small"><?php echo date('d/m/Y H:i', strtotime($d['data_doacao'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Seção de Mensagens dos Beneficiários -->
        <div class="row g-4">
            <div class="col-md-12">
                <div class="card-impact p-4 text-start" style="background: rgba(18, 20, 28, 0.7); border: 1px solid rgba(45, 192, 255, 0.25);">
                    <h5 class="fw-bold text-cyan mb-3"><i class="fa-solid fa-comment-dots me-2"></i>Recados e Agradecimentos da Comunidade</h5>
                    <div class="alert bg-dark border-secondary text-light mb-0 small">
                        <i class="fa-solid fa-quote-left text-cyan me-2"></i> "Agradecemos imensamente a todos os doadores por manterem a nossa cesta básica garantida este mês. Que Deus abençoe cada um de vocês!" — <i>Família Assistida</i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal para Fazer Doação com PIX Estático -->
    <div class="modal fade" id="modalFazerDoacao" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-black border border-secondary text-light rounded-4 shadow-lg text-start">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-cyan"><i class="fa-solid fa-heart me-2"></i>Realizar Doação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="/index.php?url=doador/doar" method="POST">
                    <div class="modal-body row g-3">
                        <div class="col-12">
                            <label class="form-label text-muted small">Tipo de Doação</label>
                            <select name="tipo_doacao" id="tipoDoacaoSelect" class="form-select bg-dark text-light border-secondary" onchange="verificarTipoDoacao()">
                                <option value="Alimento / Cesta Básica">Alimento / Cesta Básica</option>
                                <option value="Financeiro (PIX)">Financeiro (PIX)</option>
                                <option value="Roupas / Agasalhos">Roupas / Agasalhos</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>

                        <!-- Seção PIX Estático (Aparece apenas quando PIX é selecionado) -->
                        <div class="col-12 text-center d-none" id="secaoPix">
                            <div class="p-3 bg-dark border border-secondary rounded-3 mb-3">
                                <p class="text-muted small mb-2">Escaneie o QR Code abaixo com o aplicativo do seu banco:</p>
                                <!-- QR Code Estático via API pública baseada em chave fixa -->
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=00020126580014br.gov.bcb.pix0136gestao-ong-suporte@ong.com5204000053039865802BR5909Gestao ONG6009Sao Paulo6304E2CA" alt="QR Code PIX" class="img-fluid rounded bg-white p-2 mb-2" style="width: 150px; height: 150px;">
                                
                                <label class="form-label text-muted small d-block mt-2">Chave PIX (E-mail):</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" id="chavePixInput" class="form-control bg-black text-light border-secondary text-center" value="gestao-ong-suporte@ong.com" readonly>
                                    <button class="btn btn-outline-info" type="button" onclick="copiarChavePix()"><i class="fa-solid fa-copy"></i> Copiar</button>
                                </div>
                                <small class="text-cyan d-block mt-2" id="msgCopiado" style="display: none !important;">Chave copiada com sucesso!</small>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small">Quantidade ou Valor (R$)</label>
                            <input type="text" name="quantidade_ou_valor" class="form-control bg-dark text-light border-secondary" required placeholder="Ex: 2 Cestas ou R$ 100,00">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Observações / Comprovante</label>
                            <textarea name="descricao" class="form-control bg-dark text-light border-secondary" rows="2" placeholder="Detalhes da doação..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-cyan-gradient btn-sm px-4 fw-bold">Registrar Doação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script para Alternar Exibição do PIX -->
    <script>
        function verificarTipoDoacao() {
            let select = document.getElementById('tipoDoacaoSelect');
            let secaoPix = document.getElementById('secaoPix');
            if (select.value === 'Financeiro (PIX)') {
                secaoPix.classList.remove('d-none');
            } else {
                secaoPix.classList.add('d-none');
            }
        }

        function copiarChavePix() {
            let input = document.getElementById('chavePixInput');
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value);
            
            let msg = document.getElementById('msgCopiado');
            msg.style.display = 'block';
            setTimeout(() => { msg.style.display = 'none'; }, 2500);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>