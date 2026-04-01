<div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight">Mídias</h1>
        <p class="mt-1 text-slate-400 text-sm"><?= e($cliente['nome']) ?> — upload via API (provider cloudinary)</p>
    </div>
    <a class="btn-saas shrink-0" href="<?= e(base_url('clientes')) ?>">
        <i data-lucide="arrow-left" class="w-4 h-4" aria-hidden="true"></i>
        Clientes
    </a>
</div>

<div class="card-saas mb-6">
    <h2 class="text-base font-semibold text-slate-100 m-0 mb-4 flex items-center gap-2">
        <i data-lucide="cloud-upload" class="w-5 h-5 text-indigo-400" aria-hidden="true"></i>
        Novo upload
    </h2>
    <form method="post" action="<?= e(base_url('midias/upload')) ?>" enctype="multipart/form-data">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="cliente_id" value="<?= (int) $cliente['id'] ?>">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="field-saas">
                <label class="label-saas" for="nome">Nome / descrição</label>
                <input type="text" class="input-saas" id="nome" name="nome" placeholder="Banner promo">
            </div>
            <div class="field-saas">
                <label class="label-saas" for="arquivo">Arquivo</label>
                <input type="file" class="input-saas py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-500/20 file:text-indigo-200 file:text-sm" id="arquivo" name="arquivo" required>
            </div>
        </div>
        <button type="submit" class="btn-saas btn-saas--primary">
            <i data-lucide="upload" class="w-4 h-4" aria-hidden="true"></i>
            Enviar para API
        </button>
    </form>
</div>

<div class="card-saas">
    <?php if (empty($midias)): ?>
        <p class="muted-saas text-center py-10 m-0">Nenhuma mídia. URLs ficam salvas para uso nos templates.</p>
    <?php else: ?>
        <div class="table-saas-wrap">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th class="hidden md:table-cell">URL</th>
                        <th>Provider</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($midias as $m): ?>
                        <tr>
                            <td class="font-medium text-slate-100"><?= e($m['nome']) ?></td>
                            <td class="hidden md:table-cell">
                                <a href="<?= e($m['url']) ?>" target="_blank" rel="noopener" class="text-indigo-400 hover:text-indigo-300 text-sm truncate max-w-xs inline-block align-bottom"><?= e(mb_substr($m['url'], 0, 48)) ?>…</a>
                            </td>
                            <td><?= e($m['provider']) ?></td>
                            <td class="text-right">
                                <form method="post" action="<?= e(base_url('midias/excluir')) ?>" class="inline" onsubmit="return confirm('Remover registro?');">
                                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                                    <input type="hidden" name="cliente_id" value="<?= (int)$cliente['id'] ?>">
                                    <button type="submit" class="btn-saas btn-saas--sm btn-saas--danger">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
