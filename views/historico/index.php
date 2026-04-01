<div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight">Histórico</h1>
        <p class="mt-1 text-slate-400 text-sm">Envios registrados e resposta da API Resend.</p>
    </div>
    <form method="get" action="<?= e(base_url('historico')) ?>" class="shrink-0">
        <label class="label-saas sr-only" for="filtro-cliente">Cliente</label>
        <select class="select-saas min-w-[200px]" id="filtro-cliente" name="cliente_id" onchange="this.form.submit()">
            <option value="">Todos os clientes</option>
            <?php foreach ($clientes as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= ($filtroClienteId !== null && (int)$filtroClienteId === (int)$c['id']) ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="card-saas">
    <?php if (empty($emails)): ?>
        <div class="text-center py-14">
            <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white/[0.05] border border-white/10 mb-4">
                <i data-lucide="inbox" class="w-7 h-7 text-slate-500" aria-hidden="true"></i>
            </span>
            <p class="text-slate-300 font-medium m-0">Nenhum registro</p>
            <p class="muted-saas mt-2 m-0">Os envios aparecem aqui após usar a tela Enviar.</p>
        </div>
    <?php else: ?>
        <div class="table-saas-wrap">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th class="hidden lg:table-cell">Cliente</th>
                        <th>Para</th>
                        <th class="hidden md:table-cell">Assunto</th>
                        <th>Status</th>
                        <th class="text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($emails as $em): ?>
                        <tr>
                            <td class="text-slate-400 text-sm whitespace-nowrap"><?= e($em['data_envio']) ?></td>
                            <td class="hidden lg:table-cell text-slate-300"><?= e($em['cliente_nome']) ?></td>
                            <td class="font-medium text-slate-100"><?= e($em['destinatario']) ?></td>
                            <td class="hidden md:table-cell text-slate-400 max-w-[200px] truncate"><?= e($em['assunto']) ?></td>
                            <td><?= badge_status((string) $em['status']) ?></td>
                            <td class="text-right">
                                <a class="btn-saas btn-saas--sm" href="<?= e(base_url('historico/ver?id=' . (int)$em['id'])) ?>" target="_blank" rel="noopener">
                                    <i data-lucide="external-link" class="w-3.5 h-3.5" aria-hidden="true"></i>
                                    HTML
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
