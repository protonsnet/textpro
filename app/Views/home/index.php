<?php
/**
 * Interface Profissional NexoWriter - Design Focado em Conversão
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
            background: radial-gradient(circle at 0% 0%, rgba(37, 99, 235, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 100% 100%, rgba(124, 58, 237, 0.05) 0%, transparent 50%);
        }
        .text-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-10px); box-shadow: 0 20px 40px -15px rgba(0,0,0,0.1); }
    </style>
</head>

<body class="bg-white text-slate-900 antialiased">

<nav class="glass border-b border-slate-100 sticky top-0 z-[100]">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <a href="<?= BASE_URL ?>/" class="flex items-center gap-2.5 group">
            <div class="bg-blue-600 p-2 rounded-xl text-white group-hover:rotate-12 transition-transform">
                <i data-lucide="feather" class="w-6 h-6"></i>
            </div>
            <span class="text-2xl font-extrabold tracking-tighter text-slate-900">TEXTPRO</span>
        </a>

        <div class="hidden md:flex items-center gap-8">
            <a href="#beneficios" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Recursos</a>
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
                Atualizado ABNT 2025
            </div>
            <h1 class="text-5xl md:text-7xl font-extrabold leading-[1.1] mb-8 tracking-tight">
                Escreva com <br><span class="text-gradient">perfeição técnica.</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-500 max-w-lg mb-10 leading-relaxed">
                A plataforma inteligente que cuida da formatação, capas e normas enquanto você foca na criação da sua obra ou pesquisa.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="<?= BASE_URL ?>/register"
                   class="px-8 py-4 bg-blue-600 text-white rounded-2xl text-lg font-bold hover:bg-blue-700 transition shadow-2xl shadow-blue-200 flex items-center justify-center gap-3 group">
                    Iniciar meu projeto <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <div class="flex -space-x-3 items-center ml-4">
                    <img class="w-10 h-10 rounded-full border-2 border-white object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=100" alt="User">
                    <img class="w-10 h-10 rounded-full border-2 border-white object-cover" src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=100" alt="User">
                    <img class="w-10 h-10 rounded-full border-2 border-white object-cover" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=100" alt="User">
                    <span class="pl-5 text-sm font-bold text-slate-500">+2.400 autores ativos</span>
                </div>
            </div>
        </div>

        <div class="relative">
            
            <div class="relative z-10 rounded-[2.5rem] bg-slate-900 p-2 shadow-2xl overflow-hidden border border-slate-800">
                <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&q=80&w=1200" 
                     alt="Interface do Editor" class="rounded-[2rem] opacity-90 hover:opacity-100 transition-opacity">
            </div>
            <div class="absolute -bottom-6 -left-6 glass p-4 rounded-2xl shadow-xl border border-white z-20 flex items-center gap-3 animate-bounce">
                <div class="bg-emerald-100 text-emerald-600 p-2 rounded-lg"><i data-lucide="check-check"></i></div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">Status</p>
                    <p class="text-sm font-bold">Formatação 100% OK</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="beneficios" class="max-w-7xl mx-auto px-6 py-24">
    <div class="text-center max-w-3xl mx-auto mb-20">
        <h2 class="text-3xl md:text-5xl font-extrabold mb-6 tracking-tight">Feito para quem busca excelência</h2>
        <p class="text-slate-500 text-lg">Deixe as regras chatas de lado e foque no seu conteúdo.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="p-8 rounded-[2rem] bg-slate-50 border border-transparent card-hover">
            <div class="w-14 h-14 bg-white shadow-sm text-blue-600 rounded-2xl flex items-center justify-center mb-6">
                <i data-lucide="graduation-cap" class="w-7 h-7"></i>
            </div>
            <h3 class="text-xl font-bold mb-3">Normas Acadêmicas</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-6">
                ABNT, APA e Vancouver configuradas automaticamente. Sumários, citações e referências sem erros.
            </p>
            <img src="https://images.unsplash.com/photo-1523050335456-c38730b0ebf4?auto=format&fit=crop&q=80&w=400" class="rounded-xl h-32 w-full object-cover grayscale hover:grayscale-0 transition-all" alt="Educação">
        </div>

        <div class="p-8 rounded-[2rem] bg-slate-50 border border-transparent card-hover">
            <div class="w-14 h-14 bg-white shadow-sm text-purple-600 rounded-2xl flex items-center justify-center mb-6">
                <i data-lucide="book-type" class="w-7 h-7"></i>
            </div>
            <h3 class="text-xl font-bold mb-3">Publicação de Livros</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-6">
                Layouts prontos para Amazon KDP e gráficas. Capas, páginas de rosto e numeração profissional.
            </p>
            <img src="https://images.unsplash.com/photo-1474366521946-c3d4b507ad7a?auto=format&fit=crop&q=80&w=400" class="rounded-xl h-32 w-full object-cover grayscale hover:grayscale-0 transition-all" alt="Livros">
        </div>

        <div class="p-8 rounded-[2rem] bg-slate-50 border border-transparent card-hover">
            <div class="w-14 h-14 bg-white shadow-sm text-emerald-600 rounded-2xl flex items-center justify-center mb-6">
                <i data-lucide="zap" class="w-7 h-7"></i>
            </div>
            <h3 class="text-xl font-bold mb-3">Exportação Rápida</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-6">
                Gere arquivos em PDF de alta qualidade ou Word editável em segundos, direto para seu e-mail.
            </p>
            <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=400" class="rounded-xl h-32 w-full object-cover grayscale hover:grayscale-0 transition-all" alt="Rapidez">
        </div>
    </div>
</section>

<section id="planos" class="bg-slate-900 py-24 rounded-[3rem] mx-4 my-8">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-extrabold text-white mb-4">Planos que cabem no seu bolso</h2>
            <p class="text-slate-400">Sem taxas escondidas. Cancele quando quiser.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($planos as $index => $plano): 
                $isPopular = ($index === 1); 
            ?>
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-[2.5rem] p-10 border <?= $isPopular ? 'border-blue-500 shadow-2xl shadow-blue-500/20' : 'border-slate-700' ?> relative group transition-all">
                    <?php if($isPopular): ?>
                        <span class="absolute -top-4 left-1/2 -translate-x-1/2 bg-blue-600 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-tighter">Mais escolhido</span>
                    <?php endif; ?>

                    <h3 class="text-lg font-bold text-slate-300 mb-2 uppercase tracking-widest text-sm"><?= htmlspecialchars($plano->nome) ?></h3>
                    <div class="flex items-baseline mb-8 text-white">
                        <span class="text-5xl font-extrabold">R$ <?= number_format($plano->preco, 2, ',', '.') ?></span>
                        <span class="text-slate-400 ml-2">/mês</span>
                    </div>

                    <ul class="space-y-4 mb-10">
                        <li class="flex items-center gap-3 text-slate-300 text-sm">
                            <i data-lucide="check" class="w-5 h-5 text-blue-500"></i> Projetos ilimitados
                        </li>
                        <li class="flex items-center gap-3 text-slate-300 text-sm">
                            <i data-lucide="check" class="w-5 h-5 text-blue-500"></i> Exportação em PDF
                        </li>
                        <li class="flex items-center gap-3 text-slate-300 text-sm">
                            <i data-lucide="check" class="w-5 h-5 text-blue-500"></i> Templates exclusivos
                        </li>
                    </ul>

                    <a href="<?= BASE_URL ?>/plans/checkout/<?= $plano->id ?>"
                    class="block w-full py-4 rounded-2xl font-bold text-center transition-all <?= $isPopular ? 'bg-blue-600 text-white hover:bg-blue-500' : 'bg-slate-700 text-white hover:bg-slate-600' ?>">
                        Selecionar Plano
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
                <span class="text-xl font-black tracking-tighter">TEXTPRO</span>
            </div>
            <p class="text-slate-500 text-sm leading-relaxed">
                Transformando a forma como o Brasil escreve e publica. De estudantes a autores best-sellers.
            </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-12">
            <div>
                <h4 class="font-bold text-slate-900 mb-6 text-sm">Produto</h4>
                <ul class="space-y-3 text-slate-500 text-sm">
                    <li><a href="#" class="hover:text-blue-600 transition">Editor Online</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Modelos ABNT</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Templates Livros</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 mb-6 text-sm">Empresa</h4>
                <ul class="space-y-3 text-slate-500 text-sm">
                    <li><a href="#" class="hover:text-blue-600 transition">Sobre nós</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Blog</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Carreiras</a></li>
                </ul>
            </div>
            <div class="col-span-2 md:col-span-1">
                <h4 class="font-bold text-slate-900 mb-6 text-sm">Redes Sociais</h4>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all"><i data-lucide="instagram" class="w-5 h-5"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all"><i data-lucide="linkedin" class="w-5 h-5"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-6 pt-10 flex flex-col md:flex-row justify-between items-center gap-4 text-slate-400 text-[10px] font-bold uppercase tracking-widest">
        <p>© <?= date('Y') ?> NexoWriter Tech. Todos os direitos reservados.</p>
        <div class="flex gap-6">
            <a href="#" class="hover:text-slate-900">Termos</a>
            <a href="#" class="hover:text-slate-900">Privacidade</a>
        </div>
    </div>
</footer>

<script>
    lucide.createIcons();
</script>

</body>
</html>