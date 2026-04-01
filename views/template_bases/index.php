<div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight">Templates base</h1>
        <p class="mt-1 text-slate-400 text-sm"><?= e($cliente['nome']) ?> — casca reutilizável com <span class="code-inline">{{conteudo}}</span></p>
    </div>
    <div class="flex flex-wrap gap-2 shrink-0">
        <a class="btn-saas btn-saas--primary" href="<?= e(base_url('template-bases/novo?cliente_id=' . (int)$cliente['id'])) ?>">
            <i data-lucide="layers" class="w-4 h-4" aria-hidden="true"></i>
            Novo base
        </a>
        <a class="btn-saas" href="<?= e(base_url('templates?cliente_id=' . (int)$cliente['id'])) ?>">Templates</a>
    </div>
</div>

<div class="card-saas">
    <?php if (empty($bases)): ?>
        <p class="muted-saas text-center py-10">Nenhum template base.</p>
    <?php else: ?>
        <div class="table-saas-wrap">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bases as $b): ?>
                        <tr>
                            <td class="font-medium text-slate-100"><?= e($b['nome']) ?></td>
                            <td class="text-right">
                                <div class="flex flex-wrap justify-end gap-1.5">
                                    <a class="btn-saas btn-saas--sm" href="<?= e(base_url('template-bases/editar?id=' . (int)$b['id'])) ?>">Editar</a>
                                    <form method="post" action="<?= e(base_url('template-bases/excluir')) ?>" class="inline" onsubmit="return confirm('Excluir?');">
                                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                                        <input type="hidden" name="cliente_id" value="<?= (int)$cliente['id'] ?>">
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
