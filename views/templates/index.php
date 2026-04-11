<div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight">Templates</h1>
        <p class="mt-1 text-slate-400 text-sm">Application: <span class="text-indigo-300 font-medium"><?= e($application['nome']) ?></span> — apenas templates <strong class="text-slate-400">vinculados</strong> aparecem aqui.</p>
    </div>
    <div class="flex flex-wrap gap-2 shrink-0">
        <?php if (!empty($unattached)): ?>
            <a class="btn-saas btn-saas--primary" href="<?= e(base_url('templates/vincular?application_id=' . (int)$application['id'])) ?>">
                <i data-lucide="link" class="w-4 h-4" aria-hidden="true"></i>
                Vincular existente
            </a>
        <?php endif; ?>
        <a class="btn-saas btn-saas--primary" href="<?= e(base_url('templates/novo?application_id=' . (int)$application['id'])) ?>">
            <i data-lucide="file-plus" class="w-4 h-4" aria-hidden="true"></i>
            Novo template
        </a>
        <a class="btn-saas" href="<?= e(base_url('applications')) ?>">Applications</a>
    </div>
</div>

<div class="card-saas">
    <?php if (empty($templates)): ?>
        <div class="text-center py-12">
            <p class="text-slate-300">Nenhum template vinculado.</p>
            <p class="muted-saas mt-2">Crie um novo ou vincule um da biblioteca global.</p>
            <div class="flex flex-wrap justify-center gap-2 mt-4">
                <?php if (!empty($unattached)): ?>
                    <a class="btn-saas btn-saas--primary" href="<?= e(base_url('templates/vincular?application_id=' . (int)$application['id'])) ?>">Vincular template</a>
                <?php endif; ?>
                <a class="btn-saas btn-saas--primary" href="<?= e(base_url('templates/novo?application_id=' . (int)$application['id'])) ?>">Criar template</a>
            </div>
        </div>
    <?php else: ?>
        <div class="table-saas-wrap">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Assunto</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $t): ?>
                        <tr>
                            <td class="font-medium text-slate-100"><?= e($t['nome']) ?></td>
                            <td class="text-slate-400 max-w-xs truncate"><?= e($t['assunto']) ?></td>
                            <td class="text-right">
                                <div class="flex flex-wrap justify-end gap-1.5">
                                    <a class="btn-saas btn-saas--sm" href="<?= e(base_url('templates/editar?id=' . (int)$t['id'])) ?>">Editar</a>
                                    <form method="post" action="<?= e(base_url('templates/desvincular')) ?>" class="inline" onsubmit="return confirm('Remover só desta application? O template continuará na biblioteca e em outras apps.');">
                                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="template_id" value="<?= (int)$t['id'] ?>">
                                        <input type="hidden" name="application_id" value="<?= (int)$application['id'] ?>">
                                        <button type="submit" class="btn-saas btn-saas--sm">Desvincular</button>
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
