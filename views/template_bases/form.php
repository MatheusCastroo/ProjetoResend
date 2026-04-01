<?php
$isEdit = $base !== null;
$id = $isEdit ? (int) $base['id'] : 0;
?>
<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight"><?= $isEdit ? 'Editar template base' : 'Novo template base' ?></h1>
    <p class="mt-1 text-slate-400 text-sm"><?= e($cliente['nome']) ?></p>
</div>

<div class="card-saas max-w-4xl">
    <form method="post" action="<?= e(base_url('template-bases/salvar')) ?>">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="cliente_id" value="<?= (int) $cliente['id'] ?>">

        <div class="field-saas">
            <label class="label-saas" for="nome">Nome</label>
            <input type="text" class="input-saas" id="nome" name="nome" required value="<?= e($base['nome'] ?? '') ?>">
        </div>

        <div class="field-saas">
            <label class="label-saas" for="html">HTML com <span class="code-inline">{{conteudo}}</span></label>
            <textarea class="textarea-saas" id="html" name="html" rows="18" required><?= e($base['html'] ?? '') ?></textarea>
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="btn-saas btn-saas--primary">
                <i data-lucide="save" class="w-4 h-4" aria-hidden="true"></i>
                Salvar
            </button>
            <a class="btn-saas" href="<?= e(base_url('template-bases?cliente_id=' . (int)$cliente['id'])) ?>">Voltar</a>
        </div>
    </form>
</div>
