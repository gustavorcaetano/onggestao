<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Senha - Gestão ONG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $_SESSION['url']['root']; ?>public/assets/css/style.css">
</head>
<body class="bg-dark text-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card bg-black border border-secondary p-4 rounded-4 shadow-lg text-start" style="width: 100%; max-width: 400px;">
        <h4 class="fw-bold text-cyan mb-3"><i class="fa-solid fa-lock-open me-2"></i>Nova Senha</h4>
        <p class="text-muted small mb-4">Digite sua nova senha abaixo para recuperar o acesso à sua conta.</p>
        
        <!-- Formulário de Nova Senha -->
        <form action="<?php echo $_SESSION['url']['root']; ?>auth/salvarNovaSenha" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="mb-3">
                <label class="form-label text-muted small">Nova Senha</label>
                <input type="password" name="senha" class="form-control bg-dark text-light border-secondary" required placeholder="******">
            </div>
            <button type="submit" class="btn btn-cyan-gradient w-100 fw-bold py-2">Salvar Nova Senha</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>