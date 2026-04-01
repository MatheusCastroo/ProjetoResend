<div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight">Templates</h1>
        <p class="mt-1 text-slate-400 text-sm">Todos os templates por application. Para CRUD, escolha uma app.</p>
    </div>
    <a href="<?= e(base_url('applications')) ?>" class="btn-saas shrink-0">
        <i data-lucide="layers" class="w-4 h-4" aria-hidden="true"></i>
        Applications
    </a>
</div>

<div class="card-saas">
    <?php if (empty($rows)): ?>
        <p class="muted-saas text-center py-12">Nenhum template. Crie uma <a href="<?= e(base_url('applications')) ?>" class="text-indigo-400 hover:underline">application</a> e adicione templates.</p>
    <?php else: ?>
        <div class="table-saas-wrap">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Application</th>
                        <th>Nome interno</th>
                        <th>Assunto</th>
                        <th class="text-right">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td class="text-slate-300"><?= e($r['application_nome']) ?></td>
                            <td class="font-medium text-slate-100"><?= e($r['nome']) ?></td>
                            <td class="text-slate-400 max-w-xs truncate"><?= e($r['assunto']) ?></td>
                            <td class="text-right">
                                <a class="btn-saas btn-saas--sm" href="<?= e(base_url('templates/editar?id=' . (int)$r['id'])) ?>">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
