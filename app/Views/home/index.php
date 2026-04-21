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
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); }
        .hero-gradient {
            background: radial-gradient(circle at 0% 0%, rgba(37, 99, 235, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 100% 100%, rgba(124, 58, 237, 0.08) 0%, transparent 50%);
        }
        .text-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-10px); box-shadow: 0 20px 40px -15px rgba(0,0,0,0.1); }
        
        .office-card {
            background: linear-gradient(180deg, rgba(255,255,255,1) 0%, rgba(248,250,252,1) 100%);
        }
    </style>
</head>

<body class="bg-white text-slate-900 antialiased">

<nav class="glass border-b border-slate-100 sticky top-0 z-[100]">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <a href="<?= BASE_URL ?>/" class="flex items-center gap-2.5 group">
            <div class="bg-blue-600 p-2 rounded-xl text-white group-hover:rotate-12 transition-transform">
                <i data-lucide="feather" class="w-6 h-6"></i>
            </div>
            <span class="text-2xl font-extrabold tracking-tighter text-slate-900">NEXOWRITER</span>
        </a>

        <div class="hidden md:flex items-center gap-8">
            <a href="#recursos" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Recursos</a>
            <a href="#premium" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Premium</a>
            <a href="#planos" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Preços</a>
            <div class="h-5 w-px bg-slate-200"></div>
            <a href="<?= BASE_URL ?>/login" class="text-sm font-bold text-slate-700 hover:text-blue-600 transition">Entrar</a>
            <a href="<?= BASE_URL ?>/register"
               class="px-5 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-blue-600 shadow-lg shadow-slate-200 transition-all active:scale-95">
                Começar Grátis
            </a>
        </div>
    </div>
</nav>

<section class="relative hero-gradient overflow-hidden pt-16 pb-24 md:pt-24 md:pb-32">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 mb-6 text-[11px] font-bold tracking-widest text-blue-600 bg-blue-50 border border-blue-100 rounded-full uppercase">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                Nova Geração de Editores
            </div>
            <h1 class="text-5xl md:text-7xl font-extrabold leading-[1.1] mb-8 tracking-tight">
                Seu escritório <br><span class="text-gradient">na nuvem.</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-500 max-w-lg mb-10 leading-relaxed">
                Crie documentos, planilhas e apresentações profissionais com ferramentas avançadas e formatação automática em um só lugar.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="<?= BASE_URL ?>/register"
                   class="px-8 py-4 bg-blue-600 text-white rounded-2xl text-lg font-bold hover:bg-blue-700 transition shadow-2xl shadow-blue-200 flex items-center justify-center gap-3 group">
                    Criar conta grátis <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <div class="flex -space-x-3 items-center ml-0 sm:ml-4">
                    <img class="w-10 h-10 rounded-full border-2 border-white object-cover" src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?q=80&w=100&auto=format&fit=crop" alt="User">
                    <img class="w-10 h-10 rounded-full border-2 border-white object-cover" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=100&auto=format&fit=crop" alt="User">
                    <img class="w-10 h-10 rounded-full border-2 border-white object-cover" src="https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=100&auto=format&fit=crop" alt="User">
                    <span class="pl-5 text-sm font-bold text-slate-500">+5.000 usuários</span>
                </div>
            </div>
        </div>

        <div class="relative">
            <div class="relative z-10 rounded-[2.5rem] bg-slate-900 p-2 shadow-2xl overflow-hidden border border-slate-800 transition-transform hover:scale-[1.02] duration-500">
                <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&q=80&w=1200" 
                     alt="Workspace Premium" class="rounded-[2rem] opacity-80 hover:opacity-100 transition-opacity">
            </div>
            <div class="absolute -top-6 -right-6 glass p-5 rounded-2xl shadow-xl border border-white z-20 hidden md:flex items-center gap-4">
                <div class="bg-blue-100 text-blue-600 p-3 rounded-xl"><i data-lucide="shield-check" class="w-6 h-6"></i></div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Tecnologia</p>
                    <p class="text-sm font-bold">OnlyOffice Engine 2025</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="premium" class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-3xl md:text-5xl font-extrabold mb-6 tracking-tight">Experiência <span class="text-blue-600">Premium</span> completa</h2>
            <p class="text-slate-500 text-lg">Para assinantes, oferecemos uma suíte de produtividade idêntica ao Microsoft Office, direto no seu navegador.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="office-card p-8 rounded-[2.5rem] border border-slate-200 card-hover group">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <i data-lucide="file-text" class="w-8 h-8"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">Editor de Documentos</h3>
                <p class="text-slate-500 mb-8 leading-relaxed">Poderoso como o Word. Suporte total a .docx, tabelas, sumários automáticos e revisão colaborativa.</p>
                <img src="https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&q=80&w=400" class="rounded-2xl h-48 w-full object-cover shadow-lg" alt="Documentos">
            </div>

            <div class="office-card p-8 rounded-[2.5rem] border border-slate-200 card-hover group">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                    <i data-lucide="layout-grid" class="w-8 h-8"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">Editor de Planilhas</h3>
                <p class="text-slate-500 mb-8 leading-relaxed">Compatível com Excel. Use fórmulas complexas, tabelas dinâmicas e gráficos profissionais em tempo real.</p>
                <img src="https://images.unsplash.com/photo-1551288049-bbbda546697a?auto=format&fit=crop&q=80&w=400" class="rounded-2xl h-48 w-full object-cover shadow-lg" alt="Planilhas">
            </div>

            <div class="office-card p-8 rounded-[2.5rem] border border-slate-200 card-hover group">
                <div class="w-16 h-16 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-orange-600 group-hover:text-white transition-all">
                    <i data-lucide="presentation" class="w-8 h-8"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">Editor de Slides</h3>
                <p class="text-slate-500 mb-8 leading-relaxed">Igual ao PowerPoint. Crie apresentações impactantes com transições modernas e layouts prontos.</p>
                <img src="https://images.unsplash.com/photo-1557804506-669a67965ba0?auto=format&fit=crop&q=80&w=400" class="rounded-2xl h-48 w-full object-cover shadow-lg" alt="Slides">
            </div>
        </div>
    </div>
</section>

<section id="recursos" class="max-w-7xl mx-auto px-6 py-24">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
        <div>
            <h2 class="text-4xl font-extrabold mb-8 leading-tight">Esqueça os erros de <span class="text-blue-600">formatação</span></h2>
            <div class="space-y-6">
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="check" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg">Normas ABNT 2025</h4>
                        <p class="text-slate-500">Gere capas, sumários e citações no padrão exato exigido pelas universidades.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="cloud-save" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg">Auto-save Inteligente</h4>
                        <p class="text-slate-500">Trabalhe com tranquilidade. Cada alteração é salva instantaneamente na nuvem.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative rounded-[3rem] overflow-hidden shadow-2xl">
            <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1000" alt="Equipe trabalhando">
        </div>
    </div>
</section>

<section id="planos" class="bg-slate-900 py-24 rounded-[3rem] mx-4 my-8">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-extrabold text-white mb-4">Escolha seu plano</h2>
            <p class="text-slate-400">Desbloqueie a Suíte Office completa hoje mesmo.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($planos as $index => $plano): 
                $isPopular = ($index === 1); 
            ?>
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-[2.5rem] p-10 border <?= $isPopular ? 'border-blue-500 shadow-2xl shadow-blue-500/20' : 'border-slate-700' ?> relative group transition-all">
                    <?php if($isPopular): ?>
                        <span class="absolute -top-4 left-1/2 -translate-x-1/2 bg-blue-600 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-tighter">Premium Recomendado</span>
                    <?php endif; ?>

                    <h3 class="text-lg font-bold text-slate-300 mb-2 uppercase tracking-widest text-sm"><?= htmlspecialchars($plano->nome) ?></h3>
                    <div class="flex items-baseline mb-8 text-white">
                        <span class="text-5xl font-extrabold">R$ <?= number_format($plano->preco, 2, ',', '.') ?></span>
                        <span class="text-slate-400 ml-2">/mês</span>
                    </div>

                    <ul class="space-y-4 mb-10">
                        <li class="flex items-center gap-3 text-slate-300 text-sm">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-blue-500"></i> Editor de Docs (.docx)
                        </li>
                        <li class="flex items-center gap-3 text-slate-300 text-sm">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i> Planilhas Avançadas (.xlsx)
                        </li>
                        <li class="flex items-center gap-3 text-slate-300 text-sm">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-orange-500"></i> Editor de Slides (.pptx)
                        </li>
                        <li class="flex items-center gap-3 text-slate-300 text-sm">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-blue-500"></i> Armazenamento em Nuvem
                        </li>
                    </ul>

                    <a href="<?= BASE_URL ?>/plans/checkout/<?= $plano->id ?>"
                    class="block w-full py-4 rounded-2xl font-bold text-center transition-all <?= $isPopular ? 'bg-blue-600 text-white hover:bg-blue-500' : 'bg-slate-700 text-white hover:bg-slate-600' ?>">
                        Ativar Agora
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<footer class="bg-white pt-24 pb-12">
    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-start gap-12 border-b border-slate-100 pb-16">
        <div class="max-w-xs">
            <div class="flex items-center gap-2.5 mb-6">
                <div class="bg-blue-600 p-1.5 rounded-lg text-white"><i data-lucide="feather" class="w-5 h-5"></i></div>
                <span class="text-xl font-black tracking-tighter">NEXOWRITER</span>
            </div>
            <p class="text-slate-500 text-sm leading-relaxed">
                A tecnologia definitiva para criação de documentos profissionais, planilhas e apresentações no Brasil.
            </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-12">
            <div>
                <h4 class="font-bold text-slate-900 mb-6 text-sm">Escritório Online</h4>
                <ul class="space-y-3 text-slate-500 text-sm">
                    <li><a href="#" class="hover:text-blue-600 transition">NexWriter Word</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">NexWriter Excel</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">NexWriter Slides</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 mb-6 text-sm">Suporte</h4>
                <ul class="space-y-3 text-slate-500 text-sm">
                    <li><a href="#" class="hover:text-blue-600 transition">Central de Ajuda</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">API para Empresas</a></li>
                </ul>
            </div>
            <div class="col-span-2 md:col-span-1 text-right">
                <h4 class="font-bold text-slate-900 mb-6 text-sm">Siga-nos</h4>
                <div class="flex gap-4 justify-end">
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all"><i data-lucide="instagram" class="w-5 h-5"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all"><i data-lucide="linkedin" class="w-5 h-5"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-6 pt-10 flex flex-col md:flex-row justify-between items-center gap-4 text-slate-400 text-[10px] font-bold uppercase tracking-widest">
        <p>© <?= date('Y') ?> NexoWriter Office Tech. Imagens Unsplash.</p>
        <div class="flex gap-6">
            <a href="#" class="hover:text-slate-900">Privacidade</a>
            <a href="#" class="hover:text-slate-900">Termos de Uso</a>
        </div>
    </div>
</footer>

<script>
    lucide.createIcons();
</script>

</body>
</html>