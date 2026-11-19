<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - Gestão ONG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $_SESSION['url']['root']; ?>public/assets/css/style.css">
    <link rel="icon" href="<?php echo $_SESSION['url']['root']; ?>public/assets/img/logo2.png" type="image/png">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-5 bg-dark text-light">

    <div class="card-impact p-4 p-md-5 shadow-lg" style="width: 100%; max-width: 550px; border-color: rgba(45, 192, 255, 0.25);">
        
        <div class="text-center mb-4">
            <a href="<?php echo $_SESSION['url']['root']; ?>home" class="text-white text-decoration-none fs-4 fw-bold">
                <img src="<?php echo $_SESSION['url']['root']; ?>public/assets/img/logo2.png" alt="Logo" style="width: 32px;" class="me-1">GESTÃO<span class="text-cyan">ONG</span>
            </a>
            <p class="text-muted small mt-2">Preencha os dados para criar sua conta</p>
        </div>

        <!-- Mensagem de Erro -->
        <?php if (!empty($_GET['erro'])): ?>
            <div class="alert alert-danger bg-dark border-danger text-danger alert-dismissible fade show p-2 mb-3 rounded-3 shadow-sm text-start small" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-1"></i><strong>Atenção:</strong> <?php echo htmlspecialchars($_GET['erro']); ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Formulário de Registro -->
        <form action="<?php echo $_SESSION['url']['root']; ?>auth/registrar" method="POST">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label text-white small fw-bold">Tipo de Conta:</label>
                    <select name="tipo" id="tipoConta" class="form-select bg-dark text-light border-secondary py-2" onchange="alternarCampos()" required>
                        <option value="doador">Quero ser Doador</option>
                        <option value="beneficiario">Quero ser Beneficiário (Passará por Análise)</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label text-white small fw-bold">Nome Completo / Responsável:</label>
                    <input type="text" name="nome" class="form-control bg-dark text-light border-secondary py-2" required placeholder="Seu nome completo">
                </div>

                <div class="col-md-6">
                    <label class="form-label text-white small fw-bold">E-mail:</label>
                    <input type="email" name="email" class="form-control bg-dark text-light border-secondary py-2" required placeholder="exemplo@email.com">
                </div>

                <div class="col-md-6">
                    <label class="form-label text-white small fw-bold">Senha:</label>
                    <input type="password" name="senha" class="form-control bg-dark text-light border-secondary py-2" required placeholder="Mínimo 6 caracteres">
                </div>

                <!-- Campos Exclusivos para Beneficiário (Solicitados) -->
                <div id="blocoBeneficiario" class="row g-3 mt-1" style="display: none;">
                    <div class="col-md-6">
                        <label class="form-label text-white small fw-bold">CPF:</label>
                        <input type="text" name="cpf" class="form-control bg-dark text-light border-secondary mask-cpf" placeholder="000.000.000-00" maxlength="14" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white small fw-bold">Telefone / WhatsApp:</label>
                        <input type="text" name="telefone" class="form-control bg-dark text-light border-secondary mask-telefone" placeholder="+55 (11) 97072-5000" maxlength="19" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white small fw-bold">Quantidade de Membros:</label>
                        <input type="number" name="quantidade_membros" class="form-control bg-dark text-light border-secondary py-2" placeholder="Ex: 4">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white small fw-bold">Renda Familiar (R$):</label>
                        <input type="text" name="renda_familiar" class="form-control bg-dark text-light border-secondary mask-renda" placeholder="R$ 1.200,00" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-white small fw-bold">CEP:</label>
                        <input type="text" name="cep" id="cep" class="form-control bg-dark text-light border-secondary mask-cep py-2" placeholder="00000-000" onblur="consultarCep(this.value)" maxlength="9">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label text-white small fw-bold">Logradouro / Rua:</label>
                        <input type="text" name="rua" id="rua" class="form-control bg-dark text-light border-secondary py-2" placeholder="Nome da rua">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-white small fw-bold">Número:</label>
                        <input type="text" name="numero" class="form-control bg-dark text-light border-secondary py-2" placeholder="Nº">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label text-white small fw-bold">Bairro:</label>
                        <input type="text" name="bairro" id="bairro" class="form-control bg-dark text-light border-secondary py-2" placeholder="Bairro">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label text-white small fw-bold">Cidade - UF:</label>
                        <input type="text" name="cidade" id="cidade" class="form-control bg-dark text-light border-secondary py-2" placeholder="Cidade - UF">
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-cyan-gradient w-100 py-2 fw-bold">Concluir Cadastro</button>
                </div>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="<?php echo $_SESSION['url']['root']; ?>auth/login" class="text-cyan small text-decoration-none">
                <i class="fa-solid fa-arrow-left me-1"></i> Já tem uma conta? Faça login
            </a>
        </div>
    </div>

    <script>
        // Função para alternar a exibição dos campos do beneficiário com base no tipo de conta selecionado
        function alternarCampos() {
            let tipo = document.getElementById('tipoConta').value;
            let bloco = document.getElementById('blocoBeneficiario');
            bloco.style.display = (tipo === 'beneficiario') ? 'flex' : 'none';
        }

        // Função para consultar o CEP e preencher automaticamente os campos de endereço
        function consultarCep(cep) {
            let valor = cep.replace(/\D/g, '');
            if (valor.length === 8) {
                fetch(`https://viacep.com.br/ws/${valor}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('rua').value = data.logradouro || '';
                            document.getElementById('bairro').value = data.bairro || '';
                            document.getElementById('cidade').value = (data.localidade && data.uf) ? `${data.localidade} - ${data.uf}` : '';
                        }
                    });
            }
        }
    </script>
    <script src="<?php echo $_SESSION['url']['root']; ?>public/assets/js/mascaras.js"></script> // Inclui o arquivo de máscaras para formatação de campos como CPF, telefone, CEP e renda
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>