<div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight">Applications</h1>
        <p class="mt-1 text-slate-400 text-sm">Cada app possui API Key, chave Resend e identidade visual.</p>
    </div>
    <a href="<?= e(base_url('applications/novo')) ?>" class="btn-saas btn-saas--primary shrink-0">
        <i data-lucide="plus" class="w-4 h-4" aria-hidden="true"></i>
        Nova application
    </a>
</div>

<div class="card-saas">
    <?php if (empty($applications)): ?>
        <div class="text-center py-14 px-4">
            <p class="text-slate-300 font-medium">Nenhuma application cadastrada</p>
            <p class="muted-saas mt-2">Crie uma para obter <span class="code-inline">X-API-KEY</span> e integrar sistemas externos.</p>
            <a href="<?= e(base_url('applications/novo')) ?>" class="btn-saas btn-saas--primary mt-6">Cadastrar</a>
        </div>
    <?php else: ?>
        <div class="table-saas-wrap">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th class="hidden lg:table-cell">API Key</th>
                        <th class="hidden md:table-cell">Remetente</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $a): ?>
                        <?php
                        $key = (string) ($a['api_key'] ?? '');
                        $masked = strlen($key) > 16
                            ? substr($key, 0, 8) . '…' . substr($key, -4)
                            : $key;
                        ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <?php if (!empty($a['logo_url'])): ?>
                                        <img src="<?= e($a['logo_url']) ?>" alt="" width="40" height="40" class="rounded-xl object-contain bg-slate-950/80 ring-1 ring-white/10">
                                    <?php else: ?>
                                        <span class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 shrink-0"></span>
                                    <?php endif; ?>
                                    <span class="font-medium text-slate-100"><?= e($a['nome']) ?></span>
                                </div>
                            </td>
                            <td class="hidden lg:table-cell">
                                <code class="text-xs text-slate-400 select-all"><?= e($masked) ?></code>
                            </td>
                            <td class="hidden md:table-cell text-slate-400 text-sm"><?= e($a['resend_from'] ?? '') ?></td>
                            <td class="text-right">
                                <div class="flex flex-wrap justify-end gap-1.5">
                                    <a class="btn-saas btn-saas--sm" href="<?= e(base_url('applications/editar?id=' . (int)$a['id'])) ?>">Editar</a>
                                    <a class="btn-saas btn-saas--sm" href="<?= e(base_url('templates?application_id=' . (int)$a['id'])) ?>">Templates</a>
                                    <a class="btn-saas btn-saas--sm" href="<?= e(base_url('media?application_id=' . (int)$a['id'])) ?>">Media</a>
                                    <form method="post" action="<?= e(base_url('applications/excluir')) ?>" class="inline" onsubmit="return confirm('Excluir esta application e dados relacionados?');">
                                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                                        <button type="submit" class="btn-saas btn-saas--sm btn-saas--danger">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="muted-saas mt-4 text-sm">API: <span class="code-inline">POST <?= e(base_url('api/send/{template_id}')) ?></span> com header <span class="code-inline">X-API-KEY</span>.</p>
    <?php endif; ?>
</div>
