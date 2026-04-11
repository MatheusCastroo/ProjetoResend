<div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight">Biblioteca de templates</h1>
        <p class="mt-1 text-slate-400 text-sm">Templates globais reutilizáveis — vincule cada um às applications que podem usá-los.</p>
    </div>
    <div class="flex flex-wrap gap-2 shrink-0">
        <a href="<?= e(base_url('templates/novo')) ?>" class="btn-saas btn-saas--primary">
            <i data-lucide="file-plus" class="w-4 h-4" aria-hidden="true"></i>
            Novo template
        </a>
        <a href="<?= e(base_url('applications')) ?>" class="btn-saas shrink-0">
            <i data-lucide="layers" class="w-4 h-4" aria-hidden="true"></i>
            Applications
        </a>
    </div>
</div>

<div class="card-saas">
    <?php if (empty($rows)): ?>
        <p class="muted-saas text-center py-12">Nenhum template. Crie um e marque quais applications podem usá-lo.</p>
    <?php else: ?>
        <div class="table-saas-wrap">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Applications</th>
                        <th>Assunto</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td class="font-medium text-slate-100"><?= e($r['nome']) ?></td>
                            <td class="text-slate-400 text-sm max-w-xs">
                                <?php if (!empty($r['apps_count'])): ?>
                                    <?= e($r['applications_nomes'] ?? '—') ?>
                                <?php else: ?>
                                    <span class="text-amber-400/90">Nenhuma — edite e vincule</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-slate-500 max-w-xs truncate"><?= e($r['assunto']) ?></td>
                            <td class="text-right">
                                <div class="flex flex-wrap justify-end gap-1.5">
                                    <a class="btn-saas btn-saas--sm" href="<?= e(base_url('templates/editar?id=' . (int)$r['id'])) ?>">Editar</a>
                                    <form method="post" action="<?= e(base_url('templates/excluir')) ?>" class="inline" onsubmit="return confirm('Excluir este template de todas as applications? Esta ação não pode ser desfeita.');">
                                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
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
