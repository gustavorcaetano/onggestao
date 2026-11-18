<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha - Gestão ONG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="card-impact p-4 shadow-lg text-start" style="width: 100%; max-width: 400px;">
        <h4 class="text-white fw-bold mb-3">Recuperar Senha</h4>
        <p class="text-muted small">Informe seu e-mail cadastrado para receber as instruções de redefinição.</p>
        
        <form action="index.php?url=auth/esqueci-senha" method="POST">
            <div class="mb-3">
                <input type="email" name="email" class="form-control bg-dark text-light border-secondary" required placeholder="seu@email.com">
            </div>
            <button type="submit" class="btn btn-cyan-gradient w-100 py-2 fw-bold">Enviar E-mail</button>
        </form>
        <div class="text-center mt-3">
            <a href="index.php?url=auth/login" class="text-white small text-decoration-none"><i class="fa-solid fa-arrow-left"></i> Voltar ao Login</a>
        </div>
    </div>
</body>
</html>