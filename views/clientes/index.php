<div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight">Clientes</h1>
        <p class="mt-1 text-slate-400 text-sm">Gerencie tenants, identidade visual e recursos por cliente.</p>
    </div>
    <a href="<?= e(base_url('clientes/novo')) ?>" class="btn-saas btn-saas--primary shrink-0">
        <i data-lucide="user-plus" class="w-4 h-4" aria-hidden="true"></i>
        Novo cliente
    </a>
</div>

<div class="card-saas">
    <?php if (empty($clientes)): ?>
        <div class="text-center py-14 px-4">
            <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white/[0.05] border border-white/10 mb-4">
                <i data-lucide="users" class="w-7 h-7 text-slate-500" aria-hidden="true"></i>
            </span>
            <p class="text-slate-300 font-medium">Nenhum cliente ainda</p>
            <p class="muted-saas mt-2 max-w-md mx-auto">Crie o primeiro para isolar dados, templates e envios por marca.</p>
            <a href="<?= e(base_url('clientes/novo')) ?>" class="btn-saas btn-saas--primary mt-6">Cadastrar cliente</a>
        </div>
    <?php else: ?>
        <div class="table-saas-wrap">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th class="hidden sm:table-cell">Logo</th>
                        <th>Cores</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $c): ?>
                        <tr>
                            <td>
                                <span class="font-medium text-slate-100"><?= e($c['nome']) ?></span>
                            </td>
                            <td class="hidden sm:table-cell">
                                <?php if (!empty($c['logo_url'])): ?>
                                    <img src="<?= e($c['logo_url']) ?>" alt="" width="44" height="44" class="rounded-xl object-contain bg-slate-950/80 ring-1 ring-white/10">
                                <?php else: ?>
                                    <span class="muted-saas">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-block w-7 h-7 rounded-lg ring-1 ring-white/10" style="background:<?= e($c['cor_primaria']) ?>;" title="Primária"></span>
                                    <span class="inline-block w-7 h-7 rounded-lg ring-1 ring-white/10" style="background:<?= e($c['cor_secundaria']) ?>;" title="Secundária"></span>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="flex flex-wrap items-center justify-end gap-1.5">
                                    <a class="btn-saas btn-saas--sm" href="<?= e(base_url('clientes/editar?id=' . (int)$c['id'])) ?>">Editar</a>
                                    <a class="btn-saas btn-saas--sm" href="<?= e(base_url('templates?cliente_id=' . (int)$c['id'])) ?>">Templates</a>
                                    <a class="btn-saas btn-saas--sm hidden lg:inline-flex" href="<?= e(base_url('template-bases?cliente_id=' . (int)$c['id'])) ?>">Bases</a>
                                    <a class="btn-saas btn-saas--sm" href="<?= e(base_url('midias?cliente_id=' . (int)$c['id'])) ?>">Mídias</a>
                                    <form method="post" action="<?= e(base_url('clientes/excluir')) ?>" class="inline" onsubmit="return confirm('Excluir este cliente e todos os dados?');">
                                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                                        <button type="submit" class="btn-saas btn-saas--sm btn-saas--danger">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
