<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestão ONG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="card-impact p-4 p-md-5 shadow-lg" style="width: 100%; max-width: 440px; border-color: rgba(45, 192, 255, 0.25);">
    
    <div class="text-center mb-4">
        <a href="/index.php?url=home" class="text-white text-decoration-none fs-4 fw-bold">
            <i class="fa-solid fa-hands-holding-child me-2 text-cyan"></i>GESTÃO<span class="text-cyan">ONG</span>
        </a>
    </div>

    <!-- ===== BLOCO DE ALERTAS VISUAIS DE RETORNO (SOLUÇÃO A) ===== -->
    <?php if (!empty($_GET['sucesso'])): ?>
        <div class="alert alert-success bg-dark border-success text-success alert-dismissible fade show p-2.5 mb-3 rounded-3 shadow-sm text-start small" role="alert" style="backdrop-filter: blur(8px);">
            <i class="fa-solid fa-circle-check me-1.5"></i><strong>Sucesso!</strong> <?php echo htmlspecialchars($_GET['sucesso']); ?>
            <button type="button" class="btn-close btn-close-white p-2.5" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['erro']) || !empty($erro)): ?>
        <div class="alert alert-danger bg-dark border-danger text-danger alert-dismissible fade show p-2.5 mb-3 rounded-3 shadow-sm text-start small" role="alert" style="backdrop-filter: blur(8px);">
            <i class="fa-solid fa-triangle-exclamation me-1.5"></i><strong>Atenção:</strong> <?php echo htmlspecialchars($_GET['erro'] ?? $erro); ?>
            <button type="button" class="btn-close btn-close-white p-2.5" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <!-- ============================================================ -->

    <!-- Seletor de Abas (Entrar / Cadastrar) -->
    <ul class="nav nav-pills nav-fill mb-4 bg-dark p-1 rounded-3 border border-secondary" id="authTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active py-1.5 text-xs fw-bold text-light" id="login-tab" data-bs-toggle="tab" data-bs-target="#loginPane" type="button">Entrar</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-1.5 text-xs fw-bold text-light" id="register-tab" data-bs-toggle="tab" data-bs-target="#registerPane" type="button">Criar Conta</button>
        </li>
    </ul>

    <div class="tab-content" id="authTabContent">
        <!-- ABA LOGIN -->
        <div class="tab-pane fade show active text-start" id="loginPane" role="tabpanel">
            <form action="/index.php?url=auth/login" method="POST">
                <div class="mb-3">
                    <label class="form-label text-white small fw-bold">E-mail:</label>
                    <input type="email" name="email" class="form-control bg-dark text-light border-secondary rounded-3 py-2" required placeholder="seu@email.com">
                </div>
                <div class="mb-4">
                    <label class="form-label text-white small fw-bold">Senha:</label>
                    <input type="password" name="senha" class="form-control bg-dark text-light border-secondary rounded-3 py-2" required placeholder="******">
                </div>
                <button type="submit" class="btn btn-cyan-gradient w-100 py-2 fw-bold">Entrar</button>
            </form>
        </div>

        <!-- ABA CADASTRO (Doadores e Beneficiários) -->
        <div class="tab-pane fade text-start" id="registerPane" role="tabpanel">
            <form action="/index.php?url=auth/registrar" method="POST">
                <div class="mb-2">
                    <label class="form-label text-white small fw-bold">Nome Completo:</label>
                    <input type="text" name="nome" class="form-control form-control-sm bg-dark text-light border-secondary" required placeholder="Seu nome">
                </div>
                <div class="mb-2">
                    <label class="form-label text-white small fw-bold">E-mail:</label>
                    <input type="email" name="email" class="form-control form-control-sm bg-dark text-light border-secondary" required placeholder="seu@email.com">
                </div>
                <div class="mb-2">
                    <label class="form-label text-white small fw-bold">Senha:</label>
                    <input type="password" name="senha" class="form-control form-control-sm bg-dark text-light border-secondary" required placeholder="******">
                </div>
                <div class="mb-3">
                    <label class="form-label text-white small fw-bold">Tipo de Conta:</label>
                    <select name="tipo" class="form-select form-select-sm bg-dark text-light border-secondary" required>
                        <option value="doador">Quero ser Doador</option>
                        <option value="beneficiario">Quero ser Beneficiário</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-cyan-gradient w-100 py-2 fw-bold btn-sm">Cadastrar Conta</button>
            </form>
        </div>
    </div>

    <div class="text-center mt-3">
        <a href="/index.php?url=home" class="text-white small text-decoration-none">
            <i class="fa-solid fa-arrow-left"></i> Voltar para a Home
        </a>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>