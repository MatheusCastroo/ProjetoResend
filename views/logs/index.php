<div class="logs-page">
<div class="logs-page-header mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight m-0">Logs 123</h1>
        <p class="mt-1 text-slate-400 text-sm m-0">Histórico de envios (API e painel).</p>
    </div>
    <form method="get" action="<?= e(base_url('logs')) ?>" class="w-full sm:w-auto shrink-0 max-w-full">
        <label class="label-saas sr-only" for="filtro-app">Application</label>
        <select class="select-saas w-full min-w-0 sm:min-w-[220px] max-w-full" id="filtro-app" name="application_id" onchange="this.form.submit()">
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
                        <th class="logs-col-date">Data</th>
                        <th class="hidden lg:table-cell logs-col-app">Application</th>
                        <th class="hidden md:table-cell logs-col-template">Template</th>
                        <th class="logs-col-para">Para</th>
                        <th class="logs-col-status">Status</th>
                        <th class="text-right logs-col-actions"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($emails as $em): ?>
                        <tr>
                            <td class="logs-col-date text-slate-400 text-sm"><?= e($em['created_at'] ?? '') ?></td>
                            <td class="logs-col-app hidden lg:table-cell text-slate-300"><?= e($em['application_nome'] ?? '') ?></td>
                            <td class="logs-col-template hidden md:table-cell text-slate-500 text-sm"><?= e($em['template_nome'] ?? '—') ?></td>
                            <td class="logs-col-para text-slate-100"><?= e($em['destinatario']) ?></td>
                            <td class="logs-col-status"><?= badge_status((string) $em['status']) ?></td>
                            <td class="logs-col-actions text-right">
                                <a class="btn-saas btn-saas--sm" href="<?= e(base_url('logs/ver?id=' . (int)$em['id'])) ?>" target="_blank" rel="noopener">HTML</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</div>
