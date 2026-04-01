<div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight">Logs</h1>
        <p class="mt-1 text-slate-400 text-sm">Histórico de envios (API e painel).</p>
    </div>
    <form method="get" action="<?= e(base_url('logs')) ?>" class="shrink-0">
        <label class="label-saas sr-only" for="filtro-app">Application</label>
        <select class="select-saas min-w-[220px]" id="filtro-app" name="application_id" onchange="this.form.submit()">
            <option value="">Todas as applications</option>
            <?php foreach ($applications as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= ($filtroApplicationId !== null && (int)$filtroApplicationId === (int)$c['id']) ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="card-saas">
    <?php if (empty($emails)): ?>
        <div class="text-center py-14">
            <p class="text-slate-300 font-medium m-0">Nenhum registro</p>
            <p class="muted-saas mt-2 m-0">Use a API <span class="code-inline">POST .../api/send/{id}</span> ou a tela Enviar.</p>
        </div>
    <?php else: ?>
        <div class="table-saas-wrap">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th class="hidden lg:table-cell">Application</th>
                        <th class="hidden md:table-cell">Template</th>
                        <th>Para</th>
                        <th>Status</th>
                        <th class="text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($emails as $em): ?>
                        <tr>
                            <td class="text-slate-400 text-sm whitespace-nowrap"><?= e($em['created_at'] ?? '') ?></td>
                            <td class="hidden lg:table-cell text-slate-300"><?= e($em['application_nome'] ?? '') ?></td>
                            <td class="hidden md:table-cell text-slate-500 text-sm"><?= e($em['template_nome'] ?? '—') ?></td>
                            <td class="font-medium text-slate-100"><?= e($em['destinatario']) ?></td>
                            <td><?= badge_status((string) $em['status']) ?></td>
                            <td class="text-right">
                                <a class="btn-saas btn-saas--sm" href="<?= e(base_url('logs/ver?id=' . (int)$em['id'])) ?>" target="_blank" rel="noopener">HTML</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
