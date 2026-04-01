<div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight">Templates</h1>
        <p class="mt-1 text-slate-400 text-sm">Cliente: <span class="text-indigo-300 font-medium"><?= e($cliente['nome']) ?></span></p>
    </div>
    <div class="flex flex-wrap gap-2 shrink-0">
        <a class="btn-saas btn-saas--primary" href="<?= e(base_url('templates/novo?cliente_id=' . (int)$cliente['id'])) ?>">
            <i data-lucide="file-plus" class="w-4 h-4" aria-hidden="true"></i>
            Novo template
        </a>
        <a class="btn-saas" href="<?= e(base_url('clientes')) ?>">Clientes</a>
    </div>
</div>

<div class="card-saas">
    <?php if (empty($templates)): ?>
        <div class="text-center py-12">
            <p class="text-slate-300">Nenhum template.</p>
            <p class="muted-saas mt-2">Use placeholders como <span class="code-inline">{{nome}}</span>, <span class="code-inline">{{nome_cliente}}</span>.</p>
            <a class="btn-saas btn-saas--primary mt-4" href="<?= e(base_url('templates/novo?cliente_id=' . (int)$cliente['id'])) ?>">Criar template</a>
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
                                    <form method="post" action="<?= e(base_url('templates/excluir')) ?>" class="inline" onsubmit="return confirm('Excluir?');">
                                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
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
