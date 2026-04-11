<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight">Vincular template</h1>
    <p class="mt-1 text-slate-400 text-sm"><?= e($application['nome']) ?> — escolha um template da biblioteca que ainda não está vinculado.</p>
</div>

<div class="card-saas max-w-lg">
    <?php if (empty($unattached)): ?>
        <p class="muted-saas">Todos os templates já estão vinculados ou a biblioteca está vazia.</p>
        <a class="btn-saas btn-saas--primary mt-4 inline-flex" href="<?= e(base_url('templates/novo?application_id=' . (int)$application['id'])) ?>">Criar novo template</a>
    <?php else: ?>
        <form method="post" action="<?= e(base_url('templates/vincular')) ?>">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="application_id" value="<?= (int) $application['id'] ?>">
            <div class="field-saas">
                <label class="label-saas" for="template_id">Template</label>
                <select class="select-saas" id="template_id" name="template_id" required>
                    <option value="">— Selecione —</option>
                    <?php foreach ($unattached as $u): ?>
                        <option value="<?= (int)$u['id'] ?>"><?= e($u['nome']) ?> — <?= e(mb_substr($u['assunto'], 0, 48)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex flex-wrap gap-2 pt-2">
                <button type="submit" class="btn-saas btn-saas--primary">Vincular</button>
                <a class="btn-saas" href="<?= e(base_url('templates?application_id=' . (int)$application['id'])) ?>">Cancelar</a>
            </div>
        </form>
    <?php endif; ?>
</div>
