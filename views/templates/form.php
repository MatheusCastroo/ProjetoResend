<?php
$isEdit = $template !== null;
$id = $isEdit ? (int) $template['id'] : 0;
$vj = '';
if ($isEdit && !empty($template['variaveis'])) {
    $raw = $template['variaveis'];
    $vj = is_string($raw) ? $raw : json_encode($raw, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
$linkedSet = array_fill_keys(array_map('intval', $linkedApplicationIds ?? []), true);
?>
<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight"><?= $isEdit ? 'Editar template' : 'Novo template' ?></h1>
    <p class="mt-1 text-slate-400 text-sm max-w-2xl">
        Um mesmo template pode ser <strong class="text-slate-300">reutilizado por várias applications</strong>. A identidade (logo, cores, empresa) vem sempre da application no envio.
    </p>
</div>

<div class="card-saas max-w-4xl">
    <?php if (empty($applications)): ?>
        <p class="text-amber-200/90 mb-4">Não há applications cadastradas. <a href="<?= e(base_url('applications/novo')) ?>" class="text-indigo-400 underline">Crie uma application</a> antes de salvar templates.</p>
    <?php endif; ?>
    <form method="post" action="<?= e(base_url('templates/salvar')) ?>">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="field-saas">
            <label class="label-saas">Applications que usam este template</label>
            <p class="muted-saas mb-2 text-sm">Marque todas que poderão enviar e-mails com este HTML (API e painel).</p>
            <div class="space-y-2 rounded-xl border border-white/[0.08] bg-slate-950/40 p-3 max-h-56 overflow-y-auto">
                <?php foreach ($applications as $app): ?>
                    <?php $aid = (int) $app['id']; ?>
                    <label class="flex items-center gap-3 cursor-pointer rounded-lg px-2 py-2 hover:bg-white/[0.04] transition-colors">
                        <input type="checkbox" name="application_ids[]" value="<?= $aid ?>" class="rounded border-slate-600 text-indigo-500 focus:ring-indigo-500/50"
                            <?= isset($linkedSet[$aid]) ? 'checked' : '' ?>>
                        <span class="text-slate-200 text-sm font-medium"><?= e($app['nome']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="field-saas">
            <label class="label-saas" for="nome">Nome interno (ex.: boas_vindas)</label>
            <input type="text" class="input-saas" id="nome" name="nome" required value="<?= e($template['nome'] ?? '') ?>">
        </div>

        <div class="field-saas">
            <label class="label-saas" for="assunto">Assunto</label>
            <input type="text" class="input-saas" id="assunto" name="assunto" required value="<?= e($template['assunto'] ?? '') ?>">
        </div>

        <div class="field-saas">
            <label class="label-saas" for="variaveis_json">Variáveis (JSON)</label>
            <textarea class="textarea-saas" id="variaveis_json" name="variaveis_json" rows="5" placeholder='["nome","link"]'><?= e($vj) ?></textarea>
            <p class="muted-saas mt-1.5">Branding automático: <span class="code-inline">empresa</span>, <span class="code-inline">logo</span>, <span class="code-inline">cor_primaria</span>, <span class="code-inline">cor_secundaria</span>.</p>
        </div>

        <div class="field-saas">
            <label class="label-saas" for="html">HTML</label>
            <textarea class="textarea-saas" id="html" name="html" rows="16" required><?= e($template['html'] ?? '') ?></textarea>
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="btn-saas btn-saas--primary" <?= empty($applications) ? 'disabled' : '' ?>>
                <i data-lucide="save" class="w-4 h-4" aria-hidden="true"></i>
                Salvar
            </button>
            <a class="btn-saas" href="<?= e(base_url($backUrl ?? 'templates')) ?>">Voltar</a>
        </div>
    </form>
</div>
