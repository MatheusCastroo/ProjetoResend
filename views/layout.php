<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Painel') ?> — E-mail Transacional</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        saas: {
                            bg: '#0f172a',
                            card: '#111827',
                            accent: '#6366f1',
                        }
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="<?= e(asset_url('css/saas.css')) ?>">
</head>
<body class="min-h-screen antialiased text-slate-200 bg-slate-950 selection:bg-indigo-500/30 selection:text-white">
    <div class="fixed inset-0 -z-10 pointer-events-none overflow-hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-[#0f172a]"></div>
        <div class="absolute -top-32 left-[10%] w-[min(560px,90vw)] h-[560px] rounded-full bg-indigo-600/[0.18] blur-[100px]"></div>
        <div class="absolute top-1/3 -right-20 w-[480px] h-[480px] rounded-full bg-violet-600/[0.12] blur-[90px]"></div>
        <div class="absolute bottom-0 left-1/3 w-[400px] h-[320px] rounded-full bg-indigo-500/[0.08] blur-[80px]"></div>
    </div>

    <?php
    $navItems = [
        [base_url(), 'Início', 'layout-dashboard'],
        [base_url('applications'), 'Applications', 'layers'],
        [base_url('templates'), 'Templates', 'file-code'],
        [base_url('envio'), 'Enviar', 'send'],
        [base_url('logs'), 'Logs', 'inbox'],
    ];
    ?>
    <nav class="fixed top-0 left-0 right-0 z-50 border-b border-white/[0.08] bg-slate-950/75 backdrop-blur-xl supports-[backdrop-filter]:bg-slate-950/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="h-16 flex items-center justify-between gap-4">
                <a href="<?= e(base_url()) ?>" class="flex items-center gap-2.5 shrink-0 transition-transform duration-200 ease-in-out hover:scale-[1.02]">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-500/25 ring-1 ring-white/10">
                        <i data-lucide="mail" class="w-[18px] h-[18px] text-white" aria-hidden="true"></i>
                    </span>
                    <span class="font-semibold text-slate-100 tracking-tight text-sm sm:text-base">E-mail Transacional</span>
                </a>

                <button type="button" id="nav-toggle" class="md:hidden inline-flex items-center justify-center p-2 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 transition-all duration-200 ease-in-out hover:scale-[1.02]" aria-expanded="false" aria-controls="nav-mobile" aria-label="Menu">
                    <i data-lucide="menu" class="w-5 h-5 nav-icon-open" aria-hidden="true"></i>
                    <i data-lucide="x" class="w-5 h-5 nav-icon-close hidden" aria-hidden="true"></i>
                </button>

                <div class="hidden md:flex items-center gap-0.5">
                    <?php foreach ($navItems as [$href, $label, $icon]): ?>
                        <a href="<?= e($href) ?>" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium text-slate-400 hover:text-white hover:bg-white/[0.06] transition-all duration-200 ease-in-out">
                            <i data-lucide="<?= e($icon) ?>" class="w-4 h-4 opacity-70" aria-hidden="true"></i>
                            <?= e($label) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="nav-mobile" class="nav-mobile-panel md:hidden border-t border-white/[0.06]">
                <div class="py-3 flex flex-col gap-0.5">
                    <?php foreach ($navItems as [$href, $label, $icon]): ?>
                        <a href="<?= e($href) ?>" class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/[0.06] transition-all duration-200 ease-in-out">
                            <i data-lucide="<?= e($icon) ?>" class="w-4 h-4 text-indigo-400 shrink-0" aria-hidden="true"></i>
                            <?= e($label) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="main-saas-enter max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">
        <?php
        $fOk = flash('ok');
        $fEr = flash('erro');
        if ($fOk): ?>
            <div class="flash-saas flash-saas--ok" role="status"><?= e($fOk) ?></div>
        <?php endif; ?>
        <?php if ($fEr): ?>
            <div class="flash-saas flash-saas--err" role="alert"><?= e($fEr) ?></div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <script src="https://unpkg.com/lucide@0.460.0/dist/umd/lucide.min.js"></script>
    <script>
        lucide.createIcons();
        (function () {
            var toggle = document.getElementById('nav-toggle');
            var panel = document.getElementById('nav-mobile');
            var openIcon = document.querySelector('.nav-icon-open');
            var closeIcon = document.querySelector('.nav-icon-close');
            if (!toggle || !panel) return;
            toggle.addEventListener('click', function () {
                var isOpen = panel.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                if (openIcon && closeIcon) {
                    openIcon.classList.toggle('hidden', isOpen);
                    closeIcon.classList.toggle('hidden', !isOpen);
                }
            });
        })();
    </script>
</body>
</html>
