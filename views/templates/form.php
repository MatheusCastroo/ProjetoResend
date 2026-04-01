<?php
$isEdit = $template !== null;
$id = $isEdit ? (int) $template['id'] : 0;
$vj = '';
if ($isEdit && !empty($template['variaveis'])) {
    $raw = $template['variaveis'];
    $vj = is_string($raw) ? $raw : json_encode($raw, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight"><?= $isEdit ? 'Editar template' : 'Novo template' ?></h1>
    <p class="mt-1 text-slate-400 text-sm"><?= e($application['nome']) ?></p>
</div>

<div class="card-saas max-w-4xl">
    <form method="post" action="<?= e(base_url('templates/salvar')) ?>">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="application_id" value="<?= (int) $application['id'] ?>">

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
            <button type="submit" class="btn-saas btn-saas--primary">
                <i data-lucide="save" class="w-4 h-4" aria-hidden="true"></i>
                Salvar
            </button>
            <a class="btn-saas" href="<?= e(base_url('templates?application_id=' . (int)$application['id'])) ?>">Voltar</a>
        </div>
    </form>
</div>
