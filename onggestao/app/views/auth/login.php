<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestão ONG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $_SESSION['url']['root']; ?>public/assets/css/style.css">
    <link rel="icon" href="<?php echo $_SESSION['url']['root']; ?>public/assets/img/logo2.png" type="image/png">
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-dark text-light">

    <div class="card-impact p-4 p-md-5 shadow-lg" style="width: 100%; max-width: 440px; border-color: rgba(45, 192, 255, 0.25);">
    
        <div class="text-center mb-4">
            <a href="<?php echo $_SESSION['url']['root']; ?>home" class="text-white text-decoration-none fs-4 fw-bold">
                <img src="<?php echo $_SESSION['url']['root']; ?>public/assets/img/logo2.png" alt="Logo" style="width: 32px;" class="me-1">GESTÃO<span class="text-cyan">ONG</span>
            </a>
        </div>

        <?php if (!empty($_GET['sucesso'])): ?>
            <div class="alert alert-success bg-dark border-success text-success alert-dismissible fade show p-2 mb-3 rounded-3 shadow-sm text-start small" role="alert">
                <i class="fa-solid fa-circle-check me-1"></i><strong>Sucesso!</strong> <?php echo htmlspecialchars($_GET['sucesso']); ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['erro']) || !empty($erro)): ?>
            <div class="alert alert-danger bg-dark border-danger text-danger alert-dismissible fade show p-2 mb-3 rounded-3 shadow-sm text-start small" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-1"></i><strong>Atenção:</strong> <?php echo htmlspecialchars($_GET['erro'] ?? $erro); ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- FORMULÁRIO DE LOGIN -->
        <form action="<?php echo $_SESSION['url']['root']; ?>auth/login" method="POST" class="text-start">
            <div class="mb-3">
                <label class="form-label text-white small fw-bold">E-mail:</label>
                <input type="email" name="email" class="form-control bg-dark text-light border-secondary rounded-3 py-2" required placeholder="exemplo@email.com">
            </div>
            
            <div class="mb-2">
                <label class="form-label text-white small fw-bold">Senha:</label>
                <div class="position-relative">
                    <input type="password" name="senha" id="senhaLogin" class="form-control bg-dark text-light border-secondary rounded-3 py-2 pe-5" required placeholder="Digite sua senha">
                    <i class="fa-solid fa-eye" id="toggleSenha" onclick="alternarSenha()" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: #A0A5B5;"></i>
                </div>
            </div>

            <div class="text-end mb-4">
                <a href="#" data-bs-toggle="modal" data-bs-target="#modalEsqueciSenha" class="text-cyan text-decoration-none small">Esqueci minha senha</a>
            </div>

            <button type="submit" class="btn btn-cyan-gradient w-100 py-2 fw-bold">Entrar</button>
        </form>

        <div class="text-center mt-3 pt-3 border-top border-secondary border-opacity-25">
            <p class="text-muted small mb-2">Não tem uma conta?</p>
            <a href="<?php echo $_SESSION['url']['root']; ?>auth/registrar" class="btn btn-outline-light btn-sm w-100 fw-semibold">Criar Nova Conta</a>
        </div>

        <div class="text-center mt-3">
            <a href="<?php echo $_SESSION['url']['root']; ?>home" class="text-white small text-decoration-none">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar para a Home
            </a>
        </div>
    </div>
    
    <!-- Modal Esqueci a Senha (DEVE FICAR FORA DO FORMULÁRIO DE LOGIN PRINCIPAL) -->
    <div class="modal fade" id="modalEsqueciSenha" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-black border border-secondary text-light rounded-4 shadow-lg text-start">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-cyan"><i class="fa-solid fa-key me-2"></i>Recuperar Senha</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <!-- Formulário exclusivo do modal -->
                <form action="<?php echo $_SESSION['url']['root']; ?>auth/enviarRecuperacao" method="POST">
                    <div class="modal-body row g-3">
                        <div class="col-12">
                            <p class="text-muted small">Digite o e-mail cadastrado na sua conta. Vamos gerar o link de redefinição para você.</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">E-mail</label>
                            <input type="email" name="email" class="form-control bg-dark text-light border-secondary" required placeholder="seu@email.com">
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-cyan-gradient btn-sm px-4 fw-bold">Gerar Link de Recuperação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function alternarSenha() {
            const inputSenha = document.getElementById('senhaLogin');
            const iconeOlho = document.getElementById('toggleSenha');
            if (inputSenha.type === "password") {
                inputSenha.type = "text";
                iconeOlho.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                inputSenha.type = "password";
                iconeOlho.classList.replace("fa-eye-slash", "fa-eye");
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>