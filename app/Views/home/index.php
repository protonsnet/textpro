<?php
/**
 * IMPORTANTE: Não deve haver nenhum espaço ou linha em branco acima do DOCTYPE.
 */
?><!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        section { scroll-margin-top: 6rem; }
        .gradient-bg {
            background: radial-gradient(circle at top right, #f8fafc 0%, #ffffff 50%);
        }
        .text-gradient {
            background: linear-gradient(to right, #2563eb, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="bg-white text-slate-800 antialiased">

<nav class="bg-white/80 backdrop-blur-xl border-b sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <a href="<?= BASE_URL ?>/" class="flex items-center gap-2 text-2xl font-black tracking-tighter">
            <div class="bg-blue-600 p-1.5 rounded-lg text-white">
                <i data-lucide="pen-tool" class="w-6 h-6"></i>
            </div>
            <span class="text-slate-900">TEXTPRO</span>
        </a>

        <div class="hidden md:flex items-center space-x-8">
            <a href="#beneficios" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Recursos</a>
            <a href="#escritores" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Para Escritores</a>
            <a href="#planos" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Preços</a>
            <div class="h-6 w-px bg-slate-200"></div>
            <a href="<?= BASE_URL ?>/login" class="text-sm font-bold text-slate-700 hover:text-blue-600">Entrar</a>
            <a href="<?= BASE_URL ?>/register"
               class="px-6 py-3 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-blue-600 shadow-xl shadow-slate-200 transition-all hover:-translate-y-0.5">
                Começar a Escrever
            </a>
        </div>
        
        <button class="md:hidden p-2 text-slate-600">
            <i data-lucide="menu"></i>
        </button>
    </div>
</nav>

<section class="relative gradient-bg overflow-hidden border-b border-slate-50">
    <div class="max-w-7xl mx-auto px-6 py-16 md:py-32 flex flex-col md:flex-row items-center gap-16">
        <div class="flex-1 text-center md:text-left">
            <div class="inline-flex items-center gap-2 px-4 py-2 mb-8 text-xs font-black tracking-[0.2em] text-blue-700 bg-blue-50 rounded-full uppercase">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                Sua obra pronta para o mundo
            </div>
            <h1 class="text-5xl md:text-7xl font-black leading-[1.05] mb-8 text-slate-900">
                Onde grandes <span class="text-gradient">histórias</span> ganham forma.
            </h1>
            <p class="text-xl text-slate-600 max-w-xl mb-12 leading-relaxed">
                Seja um <span class="font-bold text-slate-900">livro para publicação</span> ou um <span class="font-bold text-slate-900">TCC acadêmico</span>, o TextPro automatiza a formatação para você focar apenas no que importa: sua escrita.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                <a href="<?= BASE_URL ?>/register"
                   class="px-10 py-5 bg-blue-600 text-white rounded-2xl text-lg font-bold hover:bg-blue-700 transition shadow-2xl shadow-blue-200 flex items-center justify-center gap-3 group">
                    Iniciar meu projeto <i data-lucide="chevron-right" class="group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="#planos" class="px-10 py-5 bg-white text-slate-700 border border-slate-200 rounded-2xl text-lg font-bold hover:bg-slate-50 transition flex items-center justify-center gap-3">
                    Ver planos
                </a>
            </div>
            
            <div class="mt-12 flex items-center justify-center md:justify-start gap-6 text-slate-400">
                <div class="flex items-center gap-2 text-sm font-medium">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i> Normas ABNT 2025
                </div>
                <div class="flex items-center gap-2 text-sm font-medium">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i> Pronto para Amazon KDP
                </div>
            </div>
        </div>

        <div class="flex-1 relative w-full max-w-2xl">
            <div class="relative z-10 bg-slate-900 rounded-3xl shadow-[0_32px_64px_-16px_rgba(0,0,0,0.3)] p-3 border border-slate-800">
                <div class="bg-white rounded-2xl overflow-hidden aspect-[4/3] relative">
                    <img src="https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&q=80&w=1000" alt="Interface de escrita" class="w-full h-full object-cover opacity-90">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent"></div>
                    <div class="absolute top-10 left-10 right-10 bottom-10 bg-white shadow-2xl rounded-lg p-6 overflow-hidden hidden lg:block">
                        <div class="h-4 w-1/3 bg-slate-100 rounded mb-4"></div>
                        <div class="h-2 w-full bg-slate-50 rounded mb-2"></div>
                        <div class="h-2 w-full bg-slate-50 rounded mb-2"></div>
                        <div class="h-2 w-4/5 bg-slate-50 rounded mb-8"></div>
                        <div class="h-4 w-1/4 bg-slate-100 rounded mb-4"></div>
                        <div class="h-2 w-full bg-slate-50 rounded mb-2"></div>
                        <div class="h-2 w-full bg-slate-50 rounded mb-2"></div>
                        <div class="h-2 w-3/4 bg-slate-50 rounded"></div>
                    </div>
                </div>
            </div>
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-blue-100 rounded-full blur-3xl opacity-60"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-purple-100 rounded-full blur-3xl opacity-60"></div>
        </div>
    </div>
</section>

<section id="beneficios" class="max-w-7xl mx-auto px-6 py-24 border-b border-slate-100">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 items-end mb-20">
        <div class="lg:col-span-2">
            <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-6 tracking-tight">Uma ferramenta, <br>todas as possibilidades.</h2>
            <p class="text-lg text-slate-600">Não importa o que você está escrevendo, o TextPro tem o modelo ideal para sua necessidade.</p>
        </div>
        <div class="lg:col-span-2 flex justify-start lg:justify-end gap-10 border-l border-slate-100 pl-10">
            <div>
                <p class="text-3xl font-black text-blue-600">15k+</p>
                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Trabalhos</p>
            </div>
            <div>
                <p class="text-3xl font-black text-purple-600">2k+</p>
                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Livros</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="p-10 rounded-3xl bg-white border border-slate-100 shadow-sm hover:shadow-2xl hover:border-blue-100 transition-all group">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-blue-600 group-hover:text-white transition-all">
                <i data-lucide="graduation-cap" class="w-8 h-8"></i>
            </div>
            <h3 class="text-2xl font-bold mb-4">Estudantes</h3>
            <p class="text-slate-500 leading-relaxed mb-6">
                TCCs, teses e dissertações formatados automaticamente. Menos preocupação com margens e mais foco na nota máxima.
            </p>
            <ul class="text-sm font-bold text-slate-400 space-y-2">
                <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4"></i> ABNT/APA/Vancouver</li>
                <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4"></i> Sumário Automático</li>
            </ul>
        </div>

        <div class="p-10 rounded-3xl bg-white border border-slate-100 shadow-sm hover:shadow-2xl hover:border-purple-100 transition-all group">
            <div class="w-16 h-16 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-purple-600 group-hover:text-white transition-all">
                <i data-lucide="book-open" class="w-8 h-8"></i>
            </div>
            <h3 class="text-2xl font-bold mb-4">Autores e Escritores</h3>
            <p class="text-slate-500 leading-relaxed mb-6">
                Escreva seu romance ou livro técnico. Exporte nos formatos aceitos pelas maiores editoras e livrarias digitais do mundo.
            </p>
            <ul class="text-sm font-bold text-slate-400 space-y-2">
                <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4"></i> Formato ePub e MOBI</li>
                <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4"></i> Layout de Impressão</li>
            </ul>
        </div>

        <div class="p-10 rounded-3xl bg-white border border-slate-100 shadow-sm hover:shadow-2xl hover:border-emerald-100 transition-all group">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                <i data-lucide="layout" class="w-8 h-8"></i>
            </div>
            <h3 class="text-2xl font-bold mb-4">Profissionais</h3>
            <p class="text-slate-500 leading-relaxed mb-6">
                Relatórios corporativos, manuais e e-books profissionais com design impecável e exportação rápida.
            </p>
            <ul class="text-sm font-bold text-slate-400 space-y-2">
                <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4"></i> PDF Profissional</li>
                <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4"></i> Segurança em Nuvem</li>
            </ul>
        </div>
    </div>
</section>

<section id="planos" class="bg-slate-50 py-24">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <h2 class="text-4xl md:text-5xl font-black mb-6">Escolha o seu plano</h2>
        <p class="text-slate-500 text-lg mb-16">Tudo o que você precisa para publicar sua ideia.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($planos as $index => $plano): 
                $isPopular = ($index === 1); 
            ?>
                <div class="bg-white rounded-[2rem] p-10 border <?= $isPopular ? 'border-blue-600 ring-4 ring-blue-50' : 'border-slate-200' ?> relative transition-transform hover:scale-[1.02]">
                    <?php if($isPopular): ?>
                        <span class="absolute -top-4 left-1/2 -translate-x-1/2 bg-blue-600 text-white text-[10px] font-black px-5 py-2 rounded-full uppercase tracking-widest shadow-xl">Recomendado</span>
                    <?php endif; ?>

                    <h3 class="text-xl font-black text-slate-900 mb-4"><?= htmlspecialchars($plano->nome) ?></h3>
                    
                    <div class="flex items-baseline justify-center mb-10">
                        <span class="text-5xl font-black">R$ <?= number_format($plano->preco, 2, ',', '.') ?></span>
                        <span class="text-slate-400 font-bold ml-2">/mês</span>
                    </div>

                    <div class="space-y-4 mb-10">
                        <div class="flex items-center gap-3 text-sm font-medium text-slate-600">
                            <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i data-lucide="check" class="w-3 h-3"></i>
                            </div>
                            Projetos Ilimitados
                        </div>
                        <div class="flex items-center gap-3 text-sm font-medium text-slate-600">
                            <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i data-lucide="check" class="w-3 h-3"></i>
                            </div>
                            Exportação PDF e Word
                        </div>
                        <div class="flex items-center gap-3 text-sm font-medium text-slate-600">
                            <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i data-lucide="check" class="w-3 h-3"></i>
                            </div>
                            Focado em Livros e TCCs
                        </div>
                    </div>

                    <a href="<?= BASE_URL ?>/plans/checkout/<?= $plano->id ?>"
                    class="block w-full py-4 rounded-2xl font-black text-lg transition-all <?= $isPopular ? 'bg-blue-600 text-white shadow-xl shadow-blue-200 hover:bg-blue-700' : 'bg-slate-100 text-slate-900 hover:bg-slate-200' ?>">
                        Começar Agora
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<footer class="bg-white pt-24 pb-12 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-16 mb-20">
        <div class="col-span-1 md:col-span-2">
            <a href="#" class="flex items-center gap-2 text-2xl font-black text-slate-900 mb-8">
                <div class="bg-blue-600 p-1.5 rounded-lg text-white">
                    <i data-lucide="pen-tool" class="w-6 h-6"></i>
                </div>
                TEXTPRO
            </a>
            <p class="text-slate-500 text-lg max-w-sm mb-8 leading-relaxed">
                A plataforma de escrita definitiva para quem não tem tempo a perder com burocracia de formatação.
            </p>
            <div class="flex gap-4">
                <a href="#" class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all"><i data-lucide="instagram"></i></a>
                <a href="#" class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all"><i data-lucide="twitter"></i></a>
                <a href="#" class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all"><i data-lucide="linkedin"></i></a>
            </div>
        </div>

        <div>
            <h4 class="font-black text-slate-900 mb-8 uppercase tracking-widest text-xs">Produto</h4>
            <ul class="space-y-4 text-slate-500 font-medium">
                <li><a href="#" class="hover:text-blue-600 transition">Para Autores</a></li>
                <li><a href="#" class="hover:text-blue-600 transition">Para Acadêmicos</a></li>
                <li><a href="#" class="hover:text-blue-600 transition">Modelos ABNT</a></li>
                <li><a href="#" class="hover:text-blue-600 transition">Preços</a></li>
            </ul>
        </div>

        <div>
            <h4 class="font-black text-slate-900 mb-8 uppercase tracking-widest text-xs">Suporte</h4>
            <ul class="space-y-4 text-slate-500 font-medium">
                <li><a href="#" class="hover:text-blue-600 transition">Central de Ajuda</a></li>
                <li><a href="#" class="hover:text-blue-600 transition">Termos de Uso</a></li>
                <li><a href="#" class="hover:text-blue-600 transition">Privacidade</a></li>
                <li><a href="#" class="hover:text-blue-600 transition">Contato</a></li>
            </ul>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-6 pt-12 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-6 text-slate-400 text-xs font-bold uppercase tracking-widest">
        <p>© <?= date('Y') ?> TextPro. Criando pontes entre sua mente e o papel.</p>
        <div class="flex gap-8">
            <span>Brasil</span>
            <span>CNPJ: 00.000.000/0000-00</span>
        </div>
    </div>
</footer>

<script>
    lucide.createIcons();
</script>

</body>
</html>