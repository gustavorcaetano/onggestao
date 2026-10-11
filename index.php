<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão Social Inteligente - ONG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Header / Navbar Minimalista -->
    <nav class="navbar navbar-dark py-4">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 text-white" href="/">
                <i class="fa-solid fa-hands-holding-child me-2 text-cyan"></i>GESTÃO<span class="text-cyan">ONG</span>
            </a>
            <a href="#projeto" class="text-white text-decoration-none small fw-semibold" text-color="white  ">
                <i class="fa-solid fa-circle-info me-1 text-cyan"></i> Sobre a ONG
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="container my-5 py-4">
        <div class="row align-items-center g-5" >
            <!-- Coluna de Texto e Ações -->
            <div class="col-lg-6">
                <h1 class="display-1 fw-bold text-white mb-3" style="line-height: 0.9;">
                    GESTÃO<br>SOCIAL<br><span class="text-cyan">INTELIGENTE</span>
                </h1>
                <p class="text-white fs-6 mb-4 pe-lg-4" style="max-width: 480px;">
                    Transformando a realidade de famílias através de transparência e tecnologia humanizada.
                </p>

                <div class="d-flex flex-column gap-3 mb-4" style="max-width: 360px;">
                    <a href="auth/login" class="btn btn-cyan-gradient text-center text-decoration-none">
                        <i class="fa-solid fa-heart me-2"></i> QUERO DOAR AGORA
                    </a>
                    <div class="d-flex gap-2">
                        <a href="auth/login" class="btn btn-dark-outline flex-fill text-center text-decoration-none">Painel ADM</a>
                        <a href="auth/login" class="btn btn-dark-outline flex-fill text-center text-decoration-none">Sou Beneficiário</a>
                    </div>
                </div>

                <a href="#projeto" class="text-white fw-bold small text-decoration-none d-inline-flex align-items-center gap-2 mt-2">
                    <i class="fa-solid fa-arrow-down text-cyan"></i> Saiba mais sobre nosso projeto
                </a>
            </div>

            <!-- Coluna dos 4 Círculos de Destaque -->
          <div class="col-lg-6 d-flex justify-content-center">
            <div class="atom-orbit-container">
                <div class="circle-glow" style="background-image: url('https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=400&q=80');">
                    <span>União</span>
                </div>
                <div class="circle-glow" style="background-image: url('https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=400&q=80');">
                    <span>Amor</span>
                </div>
                <div class="circle-glow" style="background-image: url('https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=400&q=80');">
                    <span>Educação</span>
                </div>
                <div class="circle-glow" style="background-image: url('https://images.unsplash.com/photo-1531206715517-5c0ba140b2b8?auto=format&fit=crop&w=400&q=80');">
                    <span>Acolhimento</span>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- Seção Nossas Ações e Impacto (Cards Indicativos) -->
    <section id="projeto" class="container py-5 my-5 border-top border-secondary border-opacity-25">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-white">Nossas <span class="text-cyan fw-bold">AÇÕES e IMPACTO</span></h2>
            <p class="text-white">Acompanhe em tempo real os resultados gerados pelas doações e voluntariado.</p>
        </div>

       <div class="row g-4">
            <div class="col-md-4">
                <div class="card-impact">
                    <div class="icon-glow">
                        <i class="fa-solid fa-house-chimney"></i>
                    </div>
                    <h3 class="display-4 fw-bold text-white">1.2k</h3>
                    <p class="text-white small fw-semibold mb-0">Famílias Auxiliadas</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-impact">
                    <div class="icon-glow">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <h3 class="display-4 fw-bold text-white">15t</h3>
                    <p class="text-white small fw-semibold mb-0">Alimentos Distribuidos</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-impact">
                    <div class="icon-glow">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <h3 class="display-4 fw-bold text-white">100%</h3>
                    <p class="text-white small fw-semibold mb-0">Transparência Total</p>
                </div>
            </div>
        </div>

        <!-- Seção do Carrossel de Ações Dinâmicas -->
        <div class="container my-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-white">Nossas <span class="text-cyan">AÇÕES e PROJETOS</span></h2>
                <p class="text-muted small">Acompanhe o impacto do nosso trabalho em tempo real.</p>
            </div>

            <?php if (empty($acoes)): ?>
                <div class="card-impact bg-black border border-secondary text-light text-center py-5 rounded-4 shadow-lg">
                    <p class="text-muted mb-0">Nenhuma ação cadastrada no momento.</p>
                </div>
            <?php else: ?>
                <!-- Componente Carousel do Bootstrap -->
                <div id="carouselAcoes" class="carousel slide shadow-lg rounded-4 overflow-hidden" data-bs-ride="carousel" style="max-width: 900px; margin: 0 auto; border: 1px solid rgba(45, 192, 255, 0.3);">
                    
                    <div class="carousel-inner bg-black">
                        <?php foreach ($acoes as $index => $acao): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>" data-bs-interval="5000">
                                <?php if (!empty($acao['imagem'])): ?>
                                    <img src="<?php echo htmlspecialchars($acao['imagem']); ?>" class="d-block w-100" alt="Ação ONG" style="height: 420px; object-fit: cover; opacity: 0.85;">
                                <?php else: ?>
                                    <div class="d-block w-100 bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center" style="height: 420px;">
                                        <i class="fa-solid fa-hands-holding-child text-cyan fs-1"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Legenda / Informações da Ação sobre a imagem -->
                                <div class="carousel-caption d-none d-md-block text-start p-4 rounded-3" style="background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(10px); bottom: 20px; left: 20px; right: 20px; border: 1px solid rgba(255, 255, 255, 0.1);">
                                    <span class="badge bg-secondary border border-secondary mb-2"><?php echo date('d/m/Y', strtotime($acao['criado_em'])); ?></span>
                                    <h4 class="fw-bold text-cyan mb-1"><?php echo htmlspecialchars($acao['titulo']); ?></h4>
                                    <p class="text-light small mb-0"><?php echo htmlspecialchars($acao['descricao']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Botões de Avançar e Retroceder -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselAcoes" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark bg-opacity-50 rounded-circle p-3 border border-secondary" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselAcoes" data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-dark bg-opacity-50 rounded-circle p-3 border border-secondary" aria-hidden="true"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Seção Institucional: Sobre a ONG -->
    <section class="container py-5 my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card-impact p-4 p-md-5 text-start" style="background: rgba(18, 20, 28, 0.7); border: 1px solid rgba(45, 192, 255, 0.25);">
                    
                    <div class="text-center mb-4">
                        <span class="badge bg-dark text-cyan border border-secondary px-3 py-2 rounded-pill mb-2">QUEM SOMOS</span>
                        <h2 class="h2 fw-black text-white">CONSTRUINDO UM FUTURO COM <span class="text-cyan fw-bold">SOLIDARIEDADE E TECNOLOGIA</span></h2>
                    </div>

                    <div class="text-white d-flex flex-column gap-3 fs-6" style="line-height: 1.8;">
                        <p class="mb-0">
                            Fundada com o firme propósito de transformar a realidade de comunidades em vulnerabilidade social, a nossa ONG atua como uma ponte sólida entre a generosidade de doadores e famílias que necessitam de amparo urgente. A creditamos que a dignidade humana deve ser o centro de qualquer iniciativa social, e por isso estruturamos nossas operações com base em valores inegociáveis como transparência, empatia e compromisso coletivo.
                        </p>
                        <p class="mb-0">
                            O funcionamento do nosso projeto é fundamentado em uma plataforma digital integrada de Gestão Social Inteligente. Por meio deste ecossistema tecnológico, conseguimos mapear cadastros de famílias, registrar doações de mantimentos ou recursos financeiros em tempo real e processar pedidos de apoio de forma ágil e segura. Cada item doado — seja uma cesta básica, vestuário ou insumo médico — possui rastreabilidade garantida, assegurando que o auxílio chegue exatamente a quem mais precisa.
                        </p>
                        <p class="mb-0">
                            Nossa atuação diária envolve frentes essenciais de apoio humanitário, incluindo campanhas sazonais de arrecadação de agasalhos, distribuição contínua de alimentos, apoio educacional e orientação social estruturada. Trabalhamos lado a lado com voluntários dedicados e líderes comunitários para identificar prioridades e garantir que o atendimento seja humanizado e adaptado à realidade específica de cada núcleo familiar assistido.
                        </p>
                        <p class="mb-0">
                            Para mantermos a proximidade com a sociedade e com os nossos parceiros, disponibilizamos canais de comunicação diretos e eficientes. Você pode entrar em contato conosco através do nosso e-mail oficial de atendimento (<span class="text-cyan fw-semibold">contato@gestaoong.com</span>), por meio das nossas redes sociais oficiais ou visitando diretamente a nossa sede administrativa. A nossa central de atendimento está sempre aberta para esclarecer dúvidas, receber sugestões de melhorias ou orientar novos voluntários que desejem somar forças à nossa causa.
                        </p>
                        <p class="mb-0">
                            Convidamos você a fazer parte desta rede de transformação. Seja realizando uma doação pontual ou recorrente, acompanhando o nosso feed de ações públicas ou compartilhando o nosso propósito com amigos e familiares, a sua participação faz toda a diferença. Juntos, podemos construir um futuro onde a esperança e a solidariedade caminham lado a lado com a inovação social.
                        </p>
                    </div>

                    <div class="text-center mt-4 pt-3 border-top border-secondary border-opacity-25">
                        <a href="/index.php?url=auth/login" class="btn btn-cyan-gradient px-4 py-2 fs-6">
                            <i class="fa-solid fa-hands-holding-child me-2"></i> Junte-se ao Projeto
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4 text-center border-top border-secondary border-opacity-25 text-muted small">
        <p class="mb-0">&copy; 2026 GESTÃO ONG. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>