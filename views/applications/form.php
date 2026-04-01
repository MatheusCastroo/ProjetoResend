<?php
$isEdit = $application !== null;
$id = $isEdit ? (int) $application['id'] : 0;
?>
<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight"><?= $isEdit ? 'Editar application' : 'Nova application' ?></h1>
    <p class="mt-1 text-slate-400 text-sm">Configure Resend por aplicação e use <span class="code-inline">{{empresa}}</span>, <span class="code-inline">{{logo}}</span>, cores nos templates.</p>
</div>

<div class="card-saas max-w-3xl">
    <form method="post" action="<?= e(base_url('applications/salvar')) ?>" enctype="multipart/form-data">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="field-saas">
            <label class="label-saas" for="nome">Nome da aplicação</label>
            <input type="text" class="input-saas" id="nome" name="nome" required value="<?= e($application['nome'] ?? '') ?>">
        </div>

        <div class="field-saas">
            <label class="label-saas" for="resend_api_key">Resend API Key</label>
            <input type="password" class="input-saas" id="resend_api_key" name="resend_api_key" required value="<?= e($application['resend_api_key'] ?? '') ?>" autocomplete="off">
            <p class="muted-saas mt-1.5">Usada em <span class="code-inline">Authorization: Bearer</span> ao enviar e-mails desta app.</p>
        </div>

        <div class="field-saas">
            <label class="label-saas" for="resend_from">Remetente (from)</label>
            <input type="text" class="input-saas" id="resend_from" name="resend_from" required value="<?= e($application['resend_from'] ?? 'onboarding@resend.dev') ?>" placeholder="Nome &lt;email@dominio.com&gt;">
        </div>

        <div class="field-saas">
            <label class="label-saas" for="logo_url">URL do logo (opcional)</label>
            <input type="url" class="input-saas" id="logo_url" name="logo_url" placeholder="https://..." value="<?= e($application['logo_url'] ?? '') ?>">
        </div>

        <div class="field-saas">
            <label class="label-saas" for="logo_file">Upload do logo</label>
            <input type="file" class="input-saas py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-500/20 file:text-indigo-200 file:text-sm" id="logo_file" name="logo_file" accept="image/*">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="field-saas">
                <label class="label-saas" for="cor_primaria">Cor primária</label>
                <input type="color" class="input-saas h-12 cursor-pointer" id="cor_primaria" name="cor_primaria" value="<?= e($application['cor_primaria'] ?? '#6366f1') ?>">
            </div>
            <div class="field-saas">
                <label class="label-saas" for="cor_secundaria">Cor secundária</label>
                <input type="color" class="input-saas h-12 cursor-pointer" id="cor_secundaria" name="cor_secundaria" value="<?= e($application['cor_secundaria'] ?? '#111827') ?>">
            </div>
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="btn-saas btn-saas--primary">
                <i data-lucide="save" class="w-4 h-4" aria-hidden="true"></i>
                Salvar
            </button>
            <a class="btn-saas" href="<?= e(base_url('applications')) ?>">Voltar</a>
        </div>
    </form>

    <?php if ($isEdit && !empty($application['api_key'])): ?>
        <div class="mt-6 p-4 rounded-xl border border-indigo-500/25 bg-indigo-500/5">
            <p class="label-saas m-0">API Key (header X-API-KEY)</p>
            <code class="block mt-2 text-sm text-indigo-200 break-all select-all"><?= e($application['api_key']) ?></code>
            <form method="post" action="<?= e(base_url('applications/regenerar-chave')) ?>" class="mt-3" onsubmit="return confirm('Gerar nova chave? Integrações atuais deixarão de funcionar até atualizar o X-API-KEY.');">
                <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= $id ?>">
                <button type="submit" class="btn-saas btn-saas--sm">Regenerar API Key</button>
            </form>
        </div>
    <?php endif; ?>
</div>
