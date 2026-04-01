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
    <p class="mt-1 text-slate-400 text-sm"><?= e($cliente['nome']) ?></p>
</div>

<div class="card-saas max-w-4xl">
    <form method="post" action="<?= e(base_url('templates/salvar')) ?>">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="cliente_id" value="<?= (int) $cliente['id'] ?>">

        <div class="field-saas">
            <label class="label-saas" for="nome">Nome interno</label>
            <input type="text" class="input-saas" id="nome" name="nome" required value="<?= e($template['nome'] ?? '') ?>">
        </div>

        <div class="field-saas">
            <label class="label-saas" for="assunto">Assunto</label>
            <input type="text" class="input-saas" id="assunto" name="assunto" required value="<?= e($template['assunto'] ?? '') ?>" placeholder="Suporta {{variáveis}}">
        </div>

        <div class="field-saas">
            <label class="label-saas" for="template_base_id">Template base (opcional)</label>
            <select class="select-saas" id="template_base_id" name="template_base_id">
                <option value="0">— Nenhum —</option>
                <?php foreach ($bases as $b): ?>
                    <option value="<?= (int)$b['id'] ?>" <?= ($isEdit && (int)($template['template_base_id'] ?? 0) === (int)$b['id']) ? 'selected' : '' ?>><?= e($b['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field-saas">
            <label class="label-saas" for="variaveis_json">Variáveis (JSON)</label>
            <textarea class="textarea-saas" id="variaveis_json" name="variaveis_json" rows="5" placeholder='["nome","empresa","link"]'><?= e($vj) ?></textarea>
        </div>

        <div class="field-saas">
            <label class="label-saas" for="html">HTML</label>
            <textarea class="textarea-saas" id="html" name="html" rows="16" required placeholder="<p>Olá {{nome}}</p>"><?= e($template['html'] ?? '') ?></textarea>
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="btn-saas btn-saas--primary">
                <i data-lucide="save" class="w-4 h-4" aria-hidden="true"></i>
                Salvar
            </button>
            <a class="btn-saas" href="<?= e(base_url('templates?cliente_id=' . (int)$cliente['id'])) ?>">Voltar</a>
        </div>
    </form>
</div>
