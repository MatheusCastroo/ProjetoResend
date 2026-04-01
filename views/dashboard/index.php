<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight">Dashboard</h1>
    <p class="mt-1 text-slate-400 text-sm sm:text-base">API multi-tenant e envios via Resend.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8">
    <div class="card-saas card-saas--stat flex items-start gap-4">
        <div class="stat-icon stat-icon--indigo">
            <i data-lucide="layers" class="w-6 h-6" aria-hidden="true"></i>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Applications</p>
            <p class="mt-1 text-3xl font-semibold text-slate-100 tabular-nums"><?= (int) $totalApplications ?></p>
            <p class="muted-saas mt-1">Apps com API Key própria</p>
        </div>
    </div>
    <div class="card-saas card-saas--stat flex items-start gap-4">
        <div class="stat-icon stat-icon--emerald">
            <i data-lucide="mail-check" class="w-6 h-6" aria-hidden="true"></i>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">E-mails (logs)</p>
            <p class="mt-1 text-3xl font-semibold text-slate-100 tabular-nums"><?= (int) $totalEmails ?></p>
            <p class="muted-saas mt-1">Total registrado</p>
        </div>
    </div>
</div>

<div class="card-saas">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/[0.06] border border-white/[0.08]">
                <i data-lucide="inbox" class="w-5 h-5 text-indigo-400" aria-hidden="true"></i>
            </span>
            <div>
                <h2 class="text-lg font-semibold text-slate-100 m-0">Últimos envios</h2>
                <p class="muted-saas m-0 text-sm">Até 10 registros</p>
            </div>
        </div>
        <a href="<?= e(base_url('logs')) ?>" class="btn-saas btn-saas--primary btn-saas--sm shrink-0">
            <i data-lucide="arrow-right" class="w-4 h-4" aria-hidden="true"></i>
            Ver logs
        </a>
    </div>

    <?php if (empty($ultimosEmails)): ?>
        <p class="muted-saas py-8 text-center border border-dashed border-white/10 rounded-xl">Nenhum e-mail registrado ainda.</p>
    <?php else: ?>
        <div class="space-y-2">
            <?php foreach ($ultimosEmails as $em): ?>
                <div class="list-saas-item">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 min-w-0 flex-1">
                        <span class="font-medium text-slate-100 truncate"><?= e($em['destinatario']) ?></span>
                        <span class="text-sm text-slate-500 shrink-0"><?= e($em['created_at'] ?? '') ?></span>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end flex-wrap">
                        <span class="text-xs text-slate-500 truncate max-w-[160px]" title="<?= e($em['application_nome'] ?? '') ?>"><?= e($em['application_nome'] ?? '') ?></span>
                        <?= badge_status((string) $em['status']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
