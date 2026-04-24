<?php
/**
 * Interface Profissional NexoWriter - Design Focado em Conversão e Suíte Premium
 */
?><!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | Sua escrita profissional</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        section { scroll-margin-top: 5rem; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        .hero-gradient {
            background: radial-gradient(circle at 0% 0%, rgba(30, 64, 175, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 100% 100%, rgba(124, 58, 237, 0.05) 0%, transparent 50%);
        }
        .text-gradient {
            background: linear-gradient(135deg, #1e40af 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .card-hover { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-8px); box-shadow: 0 30px 60px -12px rgba(0,0,0,0.08); }
        
        .office-card {
            background: white;
            border: 1px solid #f1f5f9;
        }
        .btn-primary {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            box-shadow: 0 10px 25px -5px rgba(30, 64, 175, 0.4);
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="bg-white text-slate-900 antialiased">

<nav class="glass border-b border-slate-100 sticky top-0 z-[100]">
    <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center">
        <a href="<?= BASE_URL ?>/" class="flex items-center group">
            <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="NexoWriter Logo" class="h-10 md:h-12 w-auto object-contain transition-transform group-hover:scale-105">
        </a>

        <div class="hidden md:flex items-center gap-8">
            <a href="#recursos" class="text-sm font-semibold text-slate-600 hover:text-blue-700 transition">Recursos</a>
            <a href="#premium" class="text-sm font-semibold text-slate-600 hover:text-blue-700 transition">Premium</a>
            <a href="#planos" class="text-sm font-semibold text-slate-600 hover:text-blue-700 transition">Preços</a>
            <div class="h-5 w-px bg-slate-200"></div>
            <a href="<?= BASE_URL ?>/login" class="text-sm font-bold text-slate-700 hover:text-blue-700 transition">Entrar</a>
            <a href="<?= BASE_URL ?>/register"
               class="px-6 py-2.5 btn-primary text-white text-sm font-bold rounded-xl shadow-lg transition-all active:scale-95">
                Começar Grátis
            </a>
        </div>
    </div>
</nav>

<section class="relative hero-gradient overflow-hidden pt-12 pb-20 md:pt-20 md:pb-32">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <div>
            <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-8 text-[11px] font-bold tracking-widest text-blue-700 bg-blue-50 border border-blue-100 rounded-full uppercase">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                </span>
                Suíte Office na Nuvem
            </div>
            <h1 class="text-5xl md:text-7xl font-extrabold leading-[1.1] mb-8 tracking-tight text-slate-900">
                A evolução da sua <br><span class="text-gradient">produtividade.</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-500 max-w-lg mb-10 leading-relaxed">
                Crie, edite e colabore em documentos, planilhas e apresentações com a tecnologia OnlyOffice. Formatação impecável em qualquer lugar.
            </p>

            <div class="flex flex-col sm:flex-row gap-5 items-center">
                <a href="<?= BASE_URL ?>/register"
                   class="w-full sm:w-auto px-8 py-4 btn-primary text-white rounded-2xl text-lg font-bold shadow-2xl flex items-center justify-center gap-3 group">
                    Criar conta grátis 
                    <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <div class="flex -space-x-3 items-center">
                    <div class="flex -space-x-2">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?u=1" alt="User">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?u=2" alt="User">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?u=3" alt="User">
                    </div>
                    <span class="pl-4 text-xs font-bold text-slate-500 uppercase tracking-wider">+5k usuários ativos</span>
                </div>
            </div>
        </div>

        <div class="relative">
            <div class="absolute -inset-4 bg-gradient-to-tr from-blue-100 to-purple-100 rounded-[3rem] blur-2xl opacity-50"></div>
            <div class="relative z-10 rounded-[2.5rem] bg-white p-3 shadow-2xl border border-slate-100 overflow-hidden group">
                <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&q=80&w=1200" 
                     alt="Dashboard Preview" class="rounded-[2rem] grayscale-[20%] group-hover:grayscale-0 transition-all duration-700">
                
                <div class="absolute bottom-8 right-8 glass p-4 rounded-2xl shadow-xl border border-white flex items-center gap-4 animate-bounce-slow">
                    <div class="bg-blue-600 text-white p-2 rounded-lg"><i data-lucide="cpu" class="w-5 h-5"></i></div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase">Tecnologia</p>
                        <p class="text-sm font-bold text-slate-800">OnlyOffice Engine 2026</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="premium" class="py-24 bg-slate-50/50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-20">
            <h2 class="text-4xl font-extrabold mb-6 tracking-tight text-slate-900">Sua Suíte <span class="text-blue-700">Office</span> Completa</h2>
            <p class="text-slate-500 text-lg">Compatibilidade total com arquivos do Microsoft Office (.docx, .xlsx, .pptx) sem precisar instalar nada.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="office-card p-8 rounded-[2rem] card-hover group">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i data-lucide="file-text" class="w-7 h-7"></i>
                </div>
                <h3 class="text-xl font-extrabold mb-3 text-slate-800">Editor de Textos</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-6">Processamento de texto avançado com suporte a tabelas, estilos e colaboração em tempo real.</p>
                <div class="rounded-xl overflow-hidden shadow-inner bg-slate-100">
                    <img src="https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&q=80&w=400" class="w-full h-40 object-cover opacity-90" alt="Docs">
                </div>
            </div>

            <div class="office-card p-8 rounded-[2rem] card-hover group">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i data-lucide="table-2" class="w-7 h-7"></i>
                </div>
                <h3 class="text-xl font-extrabold mb-3 text-slate-800">Planilhas Poderosas</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-6">Mais de 400 fórmulas disponíveis, tabelas dinâmicas e análise de dados profissional.</p>
                <div class="rounded-xl overflow-hidden shadow-inner bg-slate-100">
                    <img src="https://images.unsplash.com/photo-1551288049-bbbda546697a?auto=format&fit=crop&q=80&w=400" class="w-full h-40 object-cover opacity-90" alt="Sheets">
                </div>
            </div>

            <div class="office-card p-8 rounded-[2rem] card-hover group">
                <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center mb-6 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                    <i data-lucide="presentation" class="w-7 h-7"></i>
                </div>
                <h3 class="text-xl font-extrabold mb-3 text-slate-800">Apresentações</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-6">Crie slides impactantes com transições fluidas e ferramentas de design intuitivas.</p>
                <div class="rounded-xl overflow-hidden shadow-inner bg-slate-100">
                    <img src="https://images.unsplash.com/photo-1557804506-669a67965ba0?auto=format&fit=crop&q=80&w=400" class="w-full h-40 object-cover opacity-90" alt="Slides">
                </div>
            </div>
        </div>
    </div>
</section>

<section id="recursos" class="max-w-7xl mx-auto px-6 py-24">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
        <div class="order-2 lg:order-1">
            <div class="relative rounded-[2.5rem] overflow-hidden shadow-2xl">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1000" alt="Equipe">
                <div class="absolute inset-0 bg-blue-900/10"></div>
            </div>
        </div>
        <div class="order-1 lg:order-2">
            <h2 class="text-4xl font-extrabold mb-8 leading-tight text-slate-900">Recursos pensados para o <span class="text-blue-700">Brasil</span></h2>
            <div class="space-y-8">
                <div class="flex gap-5">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200">
                        <i data-lucide="award" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-slate-800">Normas ABNT 2026</h4>
                        <p class="text-slate-500 text-sm">Formatação automática de citações e referências seguindo os últimos padrões brasileiros.</p>
                    </div>
                </div>
                <div class="flex gap-5">
                    <div class="flex-shrink-0 w-12 h-12 bg-purple-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-purple-200">
                        <i data-lucide="history" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-slate-800">Histórico de Versões</h4>
                        <p class="text-slate-500 text-sm">Nunca perca uma alteração. Recupere qualquer versão anterior do seu arquivo com um clique.</p>
                    </div>
                </div>
                <div class="flex gap-5">
                    <div class="flex-shrink-0 w-12 h-12 bg-emerald-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-slate-800">Colaboração em Equipe</h4>
                        <p class="text-slate-500 text-sm">Compartilhe links e trabalhe simultaneamente com colegas no mesmo documento.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="planos" class="bg-slate-900 py-24 rounded-[3.5rem] mx-4 my-12 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/10 blur-[100px] -z-0"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-600/10 blur-[100px] -z-0"></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-extrabold text-white mb-4 tracking-tight">Escolha o seu plano</h2>
            <p class="text-slate-400 max-w-lg mx-auto leading-relaxed">Assine e tenha acesso imediato a todas as ferramentas premium sem anúncios ou limitações.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($planos as $index => $plano): 
                $isPopular = ($index === 1); 
            ?>
                <div class="bg-slate-800/40 border <?= $isPopular ? 'border-blue-500 ring-4 ring-blue-500/10' : 'border-slate-700' ?> rounded-[2.5rem] p-10 flex flex-col transition-all hover:scale-[1.02]">
                    <?php if($isPopular): ?>
                        <div class="mb-6"><span class="bg-blue-600 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest">Mais Assinado</span></div>
                    <?php endif; ?>

                    <h3 class="text-slate-400 font-bold uppercase tracking-widest text-xs mb-4"><?= htmlspecialchars($plano->nome) ?></h3>
                    <div class="flex items-baseline mb-8 text-white">
                        <span class="text-5xl font-black italic">R$</span>
                        <span class="text-6xl font-black ml-1"><?= number_format($plano->preco, 0, ',', '.') ?></span>
                        <span class="text-slate-500 font-bold ml-2">/mês</span>
                    </div>

                    <ul class="space-y-4 mb-10 flex-1">
                        <li class="flex items-center gap-3 text-slate-300 text-sm">
                            <i data-lucide="circle-check" class="w-5 h-5 text-blue-500"></i> Editor .docx, .xlsx, .pptx
                        </li>
                        <li class="flex items-center gap-3 text-slate-300 text-sm">
                            <i data-lucide="circle-check" class="w-5 h-5 text-blue-500"></i> 10GB de Armazenamento
                        </li>
                        <li class="flex items-center gap-3 text-slate-300 text-sm">
                            <i data-lucide="circle-check" class="w-5 h-5 text-blue-500"></i> Suporte Prioritário
                        </li>
                        <li class="flex items-center gap-3 text-slate-300 text-sm">
                            <i data-lucide="circle-check" class="w-5 h-5 text-blue-500"></i> Sem anúncios no editor
                        </li>
                    </ul>

                    <a href="<?= BASE_URL ?>/plans/checkout/<?= $plano->id ?>"
                    class="block w-full py-4 rounded-2xl font-black text-center transition-all <?= $isPopular ? 'bg-blue-600 text-white hover:bg-blue-500 shadow-xl shadow-blue-900/20' : 'bg-slate-700 text-slate-200 hover:bg-slate-600' ?>">
                        ASSINAR AGORA
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<footer class="bg-white pt-24 pb-12">
    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-start gap-12 border-b border-slate-100 pb-16">
        <div class="max-w-xs">
            <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="NexoWriter" class="h-10 mb-6 opacity-90">
            <p class="text-slate-500 text-sm leading-relaxed font-medium">
                Sua suíte de produtividade brasileira. Tecnologia OnlyOffice licenciada para o máximo desempenho e segurança dos seus dados.
            </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-12">
            <div>
                <h4 class="font-black text-slate-900 mb-6 text-xs uppercase tracking-widest">Ferramentas</h4>
                <ul class="space-y-3 text-slate-500 text-sm font-semibold">
                    <li><a href="#" class="hover:text-blue-700 transition">NexWriter Word</a></li>
                    <li><a href="#" class="hover:text-blue-700 transition">NexWriter Excel</a></li>
                    <li><a href="#" class="hover:text-blue-700 transition">NexWriter Slides</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-black text-slate-900 mb-6 text-xs uppercase tracking-widest">Empresa</h4>
                <ul class="space-y-3 text-slate-500 text-sm font-semibold">
                    <li><a href="#" class="hover:text-blue-700 transition">Suporte</a></li>
                    <li><a href="#" class="hover:text-blue-700 transition">Planos</a></li>
                    <li><a href="#" class="hover:text-blue-700 transition">Blog</a></li>
                </ul>
            </div>
            <div class="col-span-2 md:col-span-1 text-left md:text-right">
                <h4 class="font-black text-slate-900 mb-6 text-xs uppercase tracking-widest">Siga-nos</h4>
                <div class="flex gap-4 justify-start md:justify-end">
                    <a href="#" class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all shadow-sm"><i data-lucide="instagram" class="w-5 h-5"></i></a>
                    <a href="#" class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all shadow-sm"><i data-lucide="linkedin" class="w-5 h-5"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-6 pt-10 flex flex-col md:flex-row justify-between items-center gap-4 text-slate-400 text-[10px] font-black uppercase tracking-widest">
        <p>© <?= date('Y') ?> NexoWriter Office. Todos os direitos reservados.</p>
        <div class="flex gap-6">
            <a href="#" class="hover:text-slate-900 transition-colors">Privacidade</a>
            <a href="#" class="hover:text-slate-900 transition-colors">Termos</a>
        </div>
    </div>
</footer>

<script>
    lucide.createIcons();
    
    // Animação simples de scroll suave (opcional se não quiser usar CSS puramente)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>

</body>
</html>