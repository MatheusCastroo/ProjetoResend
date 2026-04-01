<?php
$cid = $cliente ? (int) $cliente['id'] : 0;
$tid = $template ? (int) $template['id'] : 0;
?>
<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-100 tracking-tight">Enviar e-mail</h1>
    <p class="mt-1 text-slate-400 text-sm max-w-2xl">Escolha cliente e template, preencha variáveis e dispare via Resend. Branding <span class="code-inline">nome_cliente</span>, <span class="code-inline">logo</span> e cores é aplicado automaticamente.</p>
</div>

<div class="card-saas max-w-3xl">
    <form method="get" action="<?= e(base_url('envio')) ?>" class="pb-6 mb-6 border-b border-white/[0.08]">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="field-saas">
                <label class="label-saas" for="cliente_id">Cliente</label>
                <select class="select-saas" id="cliente_id" name="cliente_id" onchange="this.form.submit()">
                    <option value="0">— Selecione —</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= $cid === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($cliente): ?>
                <div class="field-saas">
                    <label class="label-saas" for="template_id">Template</label>
                    <select class="select-saas" id="template_id" name="template_id" onchange="this.form.submit()">
                        <option value="0">— Selecione —</option>
                        <?php foreach ($templates as $t): ?>
                            <option value="<?= (int)$t['id'] ?>" <?= $tid === (int)$t['id'] ? 'selected' : '' ?>><?= e($t['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>
    </form>

    <?php if ($cliente && $template): ?>
        <form id="form-envio" method="post" action="<?= e(base_url('envio/enviar')) ?>">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="cliente_id" value="<?= $cid ?>">
            <input type="hidden" name="template_id" value="<?= $tid ?>">

            <div class="field-saas">
                <label class="label-saas" for="destinatario">Destinatário</label>
                <input type="email" class="input-saas" id="destinatario" name="destinatario" required value="<?= e($defaultEmail) ?>">
            </div>

            <?php $brandingHint = ['nome_cliente', 'logo', 'cor_primaria', 'cor_secundaria']; ?>
            <p class="muted-saas mb-4">Variáveis automáticas: <?php foreach ($brandingHint as $i => $h): ?><?php if ($i > 0): ?>, <?php endif; ?><span class="code-inline"><?= e($h) ?></span><?php endforeach; ?></p>

            <?php foreach ($varKeys as $key): ?>
                <?php $field = 'var_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $key); ?>
                <div class="field-saas">
                    <label class="label-saas" for="<?= e($field) ?>"><?= e($key) ?></label>
                    <input type="text" class="input-saas" id="<?= e($field) ?>" name="<?= e($field) ?>" value="">
                </div>
            <?php endforeach; ?>

            <div class="flex flex-wrap gap-3 pt-4">
                <button type="submit" class="btn-saas btn-saas--primary">
                    <i data-lucide="send" class="w-4 h-4" aria-hidden="true"></i>
                    Enviar
                </button>
            </div>
        </form>

        <form id="form-preview" method="post" action="<?= e(base_url('envio/preview')) ?>" target="_blank" class="mt-8 pt-6 border-t border-white/[0.08]">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="cliente_id" value="<?= $cid ?>">
            <input type="hidden" name="template_id" value="<?= $tid ?>">
            <?php foreach ($varKeys as $key): ?>
                <?php $field = 'var_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $key); ?>
                <input type="hidden" name="<?= e($field) ?>" value="" class="preview-copy">
            <?php endforeach; ?>
            <button type="submit" class="btn-saas">
                <i data-lucide="eye" class="w-4 h-4" aria-hidden="true"></i>
                Pré-visualizar HTML
            </button>
            <p class="muted-saas mt-3 mb-0">Abre o HTML renderizado em nova aba (copia os valores do formulário acima).</p>
        </form>
        <script>
        (function () {
            var main = document.getElementById('form-envio');
            var prev = document.getElementById('form-preview');
            if (!main || !prev) return;
            prev.addEventListener('submit', function () {
                prev.querySelectorAll('.preview-copy').forEach(function (h) {
                    var name = h.getAttribute('name');
                    var el = main.querySelector('[name="' + name + '"]');
                    if (el) h.value = el.value;
                });
            });
        })();
        </script>
    <?php elseif ($cliente && !$template): ?>
        <div class="flex items-start gap-3 rounded-xl border border-amber-500/20 bg-amber-500/5 px-4 py-3">
            <i data-lucide="info" class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" aria-hidden="true"></i>
            <p class="text-sm text-amber-200/90 m-0">Escolha um template para habilitar variáveis e envio.</p>
        </div>
    <?php else: ?>
        <div class="flex items-start gap-3 rounded-xl border border-white/10 bg-white/[0.03] px-4 py-3">
            <i data-lucide="mouse-pointer-2" class="w-5 h-5 text-slate-500 shrink-0 mt-0.5" aria-hidden="true"></i>
            <p class="text-sm text-slate-400 m-0">Selecione um cliente para continuar.</p>
        </div>
    <?php endif; ?>
</div>
