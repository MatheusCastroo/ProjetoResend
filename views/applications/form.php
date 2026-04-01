<?php
$isEdit = $application !== null;
$id = $isEdit ? (int) $application['id'] : 0;
$corPri = e($application['cor_primaria'] ?? '#6366f1');
$corSec = e($application['cor_secundaria'] ?? '#111827');
?>
<div class="app-form-page">
    <header class="app-form-head">
        <h1><?= $isEdit ? 'Editar application' : 'Nova application' ?></h1>
        <p>
            Configure envio Resend e variáveis de template. Nos e-mails, use
            <span class="app-form-badge">{{empresa}}</span>
            <span class="app-form-badge">{{logo}}</span>
            <span class="app-form-badge">{{cor_primaria}}</span>
            <span class="app-form-badge">{{cor_secundaria}}</span>
        </p>
    </header>

    <div class="app-form-shell">
        <form id="app-form" method="post" action="<?= e(base_url('applications/salvar')) ?>" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="id" value="<?= $id ?>">

            <?php if (!$isEdit): ?>
                <div class="app-callout app-form-section" style="margin-bottom:1.75rem;padding:1rem 1.125rem;">
                    <i data-lucide="sparkles" class="w-5 h-5 text-indigo-400 shrink-0 mt-0.5" aria-hidden="true"></i>
                    <span>Ao salvar, uma <strong class="text-indigo-200">API Key exclusiva</strong> será gerada para o header <span class="font-mono text-xs">X-API-KEY</span>. Você poderá copiá-la na listagem ou ao editar.</span>
                </div>
            <?php endif; ?>

            <section class="app-form-section" aria-labelledby="sec-basic">
                <h2 id="sec-basic" class="app-form-section-title">
                    <i data-lucide="layout-grid" aria-hidden="true"></i>
                    Informações básicas
                </h2>

                <div class="app-field">
                    <label class="app-label" for="nome">Nome da aplicação</label>
                    <input type="text" class="app-input app-input--hero" id="nome" name="nome" required autocomplete="organization" placeholder="Ex.: Meu SaaS, Loja Principal" value="<?= e($application['nome'] ?? '') ?>">
                </div>

                <div class="app-field">
                    <label class="app-label" for="resend_api_key">
                        Resend API Key
                        <span class="app-tooltip-wrap">
                            <span class="app-tooltip-icon" title="Chave secreta da Resend usada apenas para enviar e-mails desta application (Authorization: Bearer). Não é a X-API-KEY pública do seu produto.">
                                <i data-lucide="help-circle" class="w-4 h-4" aria-hidden="true"></i>
                            </span>
                        </span>
                    </label>
                    <div class="app-input-row app-input-row--with-key">
                        <i data-lucide="key" class="app-input-icon-left" aria-hidden="true"></i>
                        <input type="password" class="app-input" id="resend_api_key" name="resend_api_key" required autocomplete="off" placeholder="re_..." value="<?= e($application['resend_api_key'] ?? '') ?>">
                        <button type="button" class="app-input-toggle" id="toggle-resend-key" aria-label="Mostrar ou ocultar chave">
                            <i data-lucide="eye" class="w-[18px] h-[18px] icon-eye" aria-hidden="true"></i>
                            <i data-lucide="eye-off" class="w-[18px] h-[18px] icon-eye-off hidden" aria-hidden="true"></i>
                        </button>
                    </div>
                    <p class="app-hint">Usada pela plataforma ao chamar a API do Resend. Mantenha em sigilo.</p>
                </div>

                <div class="app-field">
                    <label class="app-label" for="resend_from">Remetente (from)</label>
                    <input type="text" class="app-input" id="resend_from" name="resend_from" required value="<?= e($application['resend_from'] ?? 'onboarding@resend.dev') ?>" placeholder="Nome &lt;email@dominio.com&gt;" autocomplete="off">
                    <p class="app-hint">Para testes na Resend, use <a href="mailto:onboarding@resend.dev" rel="noopener">onboarding@resend.dev</a> ou um domínio verificado.</p>
                </div>

            </section>

            <section class="app-form-section" aria-labelledby="sec-brand">
                <h2 id="sec-brand" class="app-form-section-title">
                    <i data-lucide="palette" aria-hidden="true"></i>
                    Identidade visual
                </h2>

                <div class="app-field">
                    <label class="app-label" for="logo_url">URL do logo <span class="app-label-hint">(opcional)</span></label>
                    <input type="url" class="app-input" id="logo_url" name="logo_url" placeholder="https://cdn.../logo.png" value="<?= e($application['logo_url'] ?? '') ?>" autocomplete="off">
                    <img id="logo_url_preview" class="app-preview-img" alt="Preview do logo por URL" width="220" height="120">
                </div>

                <div class="app-field">
                    <span class="app-label">Upload do logo</span>
                    <div class="app-dropzone" id="logo-dropzone" role="button" tabindex="0" aria-label="Arraste uma imagem ou clique para selecionar">
                        <input type="file" id="logo_file" name="logo_file" accept="image/*">
                        <div class="app-dropzone-inner">
                            <i data-lucide="upload-cloud" class="w-8 h-8 mx-auto mb-2 text-indigo-400/90" aria-hidden="true"></i>
                            <p class="app-dropzone-title">Solte a imagem aqui ou clique para enviar</p>
                            <p class="app-dropzone-sub">PNG, JPG, WebP · substitui a URL se o upload for bem-sucedido</p>
                        </div>
                    </div>
                    <img id="logo_file_preview" class="app-preview-img" alt="Preview do arquivo selecionado" width="220" height="120">
                </div>

                <div class="app-color-grid">
                    <div class="app-color-field">
                        <label class="app-label" for="cor_primaria">Cor primária</label>
                        <div class="app-color-row">
                            <div class="app-color-strip" id="strip_primaria" style="background:<?= $corPri ?>;"></div>
                            <input type="color" id="cor_primaria" name="cor_primaria" value="<?= $corPri ?>" aria-label="Seletor cor primária">
                            <input type="text" class="app-input app-input--hex" id="hex_primaria" maxlength="7" pattern="^#[0-9A-Fa-f]{6}$" value="<?= $corPri ?>" aria-label="Hex cor primária">
                        </div>
                    </div>
                    <div class="app-color-field">
                        <label class="app-label" for="cor_secundaria">Cor secundária</label>
                        <div class="app-color-row">
                            <div class="app-color-strip" id="strip_secundaria" style="background:<?= $corSec ?>;"></div>
                            <input type="color" id="cor_secundaria" name="cor_secundaria" value="<?= $corSec ?>" aria-label="Seletor cor secundária">
                            <input type="text" class="app-input app-input--hex" id="hex_secundaria" maxlength="7" pattern="^#[0-9A-Fa-f]{6}$" value="<?= $corSec ?>" aria-label="Hex cor secundária">
                        </div>
                    </div>
                </div>
            </section>

            <div class="app-form-actions">
                <button type="submit" class="btn-app-primary">
                    <i data-lucide="check" class="w-4 h-4" aria-hidden="true"></i>
                    Salvar application
                </button>
                <a class="btn-app-outline" href="<?= e(base_url('applications')) ?>">Cancelar</a>
            </div>
        </form>

        <?php if ($isEdit && !empty($application['api_key'])): ?>
            <div class="app-api-key-box" style="margin-top:1.75rem;padding-top:1.75rem;border-top:1px solid rgba(255,255,255,0.06);">
                <span class="app-label m-0">Chave pública da API (header <code class="text-indigo-300">X-API-KEY</code>)</span>
                <code><?= e($application['api_key']) ?></code>
                <form method="post" action="<?= e(base_url('applications/regenerar-chave')) ?>" class="mt-3" onsubmit="return confirm('Gerar nova chave? Integrações atuais precisarão atualizar o X-API-KEY.');">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit" class="btn-app-outline" style="min-height:38px;font-size:0.8125rem;">Regenerar API Key</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function () {
    var keyInput = document.getElementById('resend_api_key');
    var toggleBtn = document.getElementById('toggle-resend-key');
    if (toggleBtn && keyInput) {
        toggleBtn.addEventListener('click', function () {
            var show = keyInput.type === 'password';
            keyInput.type = show ? 'text' : 'password';
            var eye = toggleBtn.querySelector('.icon-eye');
            var eyeOff = toggleBtn.querySelector('.icon-eye-off');
            if (eye) eye.classList.toggle('hidden', show);
            if (eyeOff) eyeOff.classList.toggle('hidden', !show);
            toggleBtn.setAttribute('aria-label', show ? 'Ocultar chave' : 'Mostrar chave');
        });
    }

    var logoUrl = document.getElementById('logo_url');
    var urlPrev = document.getElementById('logo_url_preview');
    function syncUrlPreview() {
        var u = (logoUrl && logoUrl.value) ? logoUrl.value.trim() : '';
        if (!urlPrev) return;
        if (u && /^https?:\/\//i.test(u)) {
            urlPrev.src = u;
            urlPrev.classList.add('is-visible');
            urlPrev.onerror = function () { urlPrev.classList.remove('is-visible'); };
        } else {
            urlPrev.removeAttribute('src');
            urlPrev.classList.remove('is-visible');
        }
    }
    if (logoUrl) {
        logoUrl.addEventListener('input', syncUrlPreview);
        logoUrl.addEventListener('blur', function () {
            if (logoUrl.value.trim() && logoUrl.checkValidity()) logoUrl.classList.add('app-input--success');
            else logoUrl.classList.remove('app-input--success');
            logoUrl.classList.toggle('app-input--error', logoUrl.value.trim() !== '' && !logoUrl.checkValidity());
        });
        syncUrlPreview();
    }

    var fileInput = document.getElementById('logo_file');
    var filePrev = document.getElementById('logo_file_preview');
    var dropzone = document.getElementById('logo-dropzone');
    function showFilePreview(file) {
        if (!file || !file.type || file.type.indexOf('image/') !== 0) return;
        var r = new FileReader();
        r.onload = function (ev) {
            if (filePrev && ev.target && ev.target.result) {
                filePrev.src = ev.target.result;
                filePrev.classList.add('is-visible');
            }
        };
        r.readAsDataURL(file);
    }
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (fileInput.files && fileInput.files[0]) showFilePreview(fileInput.files[0]);
        });
    }
    if (dropzone && fileInput) {
        ['dragenter', 'dragover'].forEach(function (ev) {
            dropzone.addEventListener(ev, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('is-dragover');
            });
        });
        ['dragleave', 'drop'].forEach(function (ev) {
            dropzone.addEventListener(ev, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('is-dragover');
            });
        });
        dropzone.addEventListener('drop', function (e) {
            var dt = e.dataTransfer;
            if (dt && dt.files && dt.files[0]) {
                fileInput.files = dt.files;
                showFilePreview(dt.files[0]);
            }
        });
    }

    function bindColor(colorId, hexId, stripId) {
        var c = document.getElementById(colorId);
        var h = document.getElementById(hexId);
        var s = document.getElementById(stripId);
        if (!c || !h || !s) return;
        function normHex(v) {
            v = (v || '').trim();
            if (/^#[0-9A-Fa-f]{6}$/.test(v)) return v;
            return c.value;
        }
        c.addEventListener('input', function () {
            h.value = c.value.toUpperCase();
            s.style.background = c.value;
        });
        h.addEventListener('input', function () {
            var v = normalizeHexInput(h.value);
            if (v) {
                c.value = v;
                h.value = v.toUpperCase();
                s.style.background = v;
            }
        });
        h.addEventListener('blur', function () {
            var v = normHex(h.value);
            h.value = v.toUpperCase();
            c.value = v;
            s.style.background = v;
        });
    }
    function normalizeHexInput(v) {
        v = v.trim();
        if (/^#[0-9A-Fa-f]{6}$/.test(v)) return v;
        if (/^[0-9A-Fa-f]{6}$/.test(v)) return '#' + v;
        return null;
    }
    bindColor('cor_primaria', 'hex_primaria', 'strip_primaria');
    bindColor('cor_secundaria', 'hex_secundaria', 'strip_secundaria');

    var form = document.getElementById('app-form');
    var nome = document.getElementById('nome');
    if (form && nome) {
        form.addEventListener('submit', function () {
            if (!nome.value.trim()) {
                nome.classList.add('app-input--error');
            }
        });
        nome.addEventListener('input', function () {
            nome.classList.remove('app-input--error');
            if (nome.value.trim()) nome.classList.add('app-input--success');
            else nome.classList.remove('app-input--success');
        });
    }
})();
</script>
