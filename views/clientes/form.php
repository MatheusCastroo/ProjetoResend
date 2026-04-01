<?php
$isEdit = $cliente !== null;
$id = $isEdit ? (int) $cliente['id'] : 0;
?>
<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight"><?= $isEdit ? 'Editar cliente' : 'Novo cliente' ?></h1>
    <p class="mt-1 text-slate-400 text-sm">Identidade visual e layout opcional com <span class="code-inline">{{conteudo}}</span>.</p>
</div>

<div class="card-saas max-w-3xl">
    <form method="post" action="<?= e(base_url('clientes/salvar')) ?>" enctype="multipart/form-data">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="field-saas">
            <label class="label-saas" for="nome">Nome</label>
            <input type="text" class="input-saas" id="nome" name="nome" required value="<?= e($cliente['nome'] ?? '') ?>">
        </div>

        <div class="field-saas">
            <label class="label-saas" for="logo_url">URL do logo (opcional se enviar arquivo)</label>
            <input type="url" class="input-saas" id="logo_url" name="logo_url" placeholder="https://..." value="<?= e($cliente['logo_url'] ?? '') ?>">
        </div>

        <div class="field-saas">
            <label class="label-saas" for="logo_file">Upload do logo</label>
            <input type="file" class="input-saas py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-500/20 file:text-indigo-200 file:text-sm" id="logo_file" name="logo_file" accept="image/*">
            <p class="muted-saas mt-1.5">Envia para a API externa configurada e preenche a URL automaticamente.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="field-saas">
                <label class="label-saas" for="cor_primaria">Cor primária</label>
                <input type="color" class="input-saas h-12 cursor-pointer" id="cor_primaria" name="cor_primaria" value="<?= e($cliente['cor_primaria'] ?? '#2563eb') ?>">
            </div>
            <div class="field-saas">
                <label class="label-saas" for="cor_secundaria">Cor secundária</label>
                <input type="color" class="input-saas h-12 cursor-pointer" id="cor_secundaria" name="cor_secundaria" value="<?= e($cliente['cor_secundaria'] ?? '#1e293b') ?>">
            </div>
        </div>

        <div class="field-saas">
            <label class="label-saas" for="layout_padrao">Layout padrão (opcional)</label>
            <textarea class="textarea-saas" id="layout_padrao" name="layout_padrao" rows="8" placeholder="HTML com {{conteudo}}"><?= e($cliente['layout_padrao'] ?? '') ?></textarea>
            <p class="muted-saas mt-1.5">Variáveis: <span class="code-inline">nome_cliente</span>, <span class="code-inline">logo</span>, <span class="code-inline">cor_primaria</span>, etc.</p>
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="btn-saas btn-saas--primary">
                <i data-lucide="save" class="w-4 h-4" aria-hidden="true"></i>
                Salvar
            </button>
            <a class="btn-saas" href="<?= e(base_url('clientes')) ?>">Voltar</a>
        </div>
    </form>
</div>
