<?php

echo $this->extend('default');
echo $this->section('content');

$qrApiUrl = $qr_api_url ?? '/qrcode';

?>

<section style="max-width: 720px; margin: auto;">
<div class="h3 section-title">Generate Single QR Code</div>
<p style="color: var(--text-secondary);">
    Configure your QR code below. The preview and embeddable URL update automatically as you type.
    <a href="/tool/qrcodes/bulk">Generate multiple QR codes as PDF / ZIP instead.</a>
</p>

<div class="form-section mt-4">
    <div class="form-group row mb-3">
        <label for="sq-data" class="col-12 col-sm-4 col-form-label fw-semibold">Content</label>
        <div class="col-12 col-sm-8">
            <input type="text" id="sq-data" class="form-control"
                   placeholder="https://example.com, phone number, text…"
                   value="https://tools.dilawars.me">
        </div>
    </div>

    <div class="form-group row mb-3">
        <label for="sq-ecc" class="col-12 col-sm-4 col-form-label fw-semibold">
            ECC Level
            <small class="d-block" style="color: var(--text-secondary); font-weight: 400;">
                Use H when adding a logo
            </small>
        </label>
        <div class="col-12 col-sm-4">
            <select id="sq-ecc" class="form-control">
                <option value="L">L — Low (7%)</option>
                <option value="M">M — Medium (15%)</option>
                <option value="Q">Q — Quartile (25%)</option>
                <option value="H" selected>H — High (30%)</option>
            </select>
        </div>
    </div>

    <div class="form-group row mb-3">
        <label for="sq-version" class="col-12 col-sm-4 col-form-label fw-semibold">
            QR Version
            <small class="d-block" style="color: var(--text-secondary); font-weight: 400;">
                Higher version = more data capacity
            </small>
        </label>
        <div class="col-12 col-sm-4">
            <input type="number" id="sq-version" class="form-control" value="5" min="1" max="40">
        </div>
    </div>

    <div class="form-group row mb-3">
        <label for="sq-logo-space" class="col-12 col-sm-4 col-form-label fw-semibold">
            Logo Space (%)
            <small class="d-block" style="color: var(--text-secondary); font-weight: 400;">
                0 = no logo space
            </small>
        </label>
        <div class="col-12 col-sm-4">
            <input type="number" id="sq-logo-space" class="form-control" value="0" min="0" max="35">
        </div>
    </div>

    <div class="form-group row mb-3">
        <label for="sq-logo-url" class="col-12 col-sm-4 col-form-label fw-semibold">Logo URL</label>
        <div class="col-12 col-sm-8">
            <input type="url" id="sq-logo-url" class="form-control"
                   placeholder="https://example.com/logo.png">
        </div>
    </div>
</div>

<div class="result mt-4">
    <div class="h5 mb-3">Preview</div>

    <div class="row align-items-start g-4">
        <div class="col-auto">
            <img id="sq-preview"
                 src=""
                 alt="QR code preview"
                 width="200" height="200"
                 style="border: 1px solid var(--border-color); border-radius: 6px; background: #fff; padding: 8px;">
        </div>

        <div class="col">
            <div class="mb-3">
                <label class="form-label fw-semibold">Embeddable URL</label>
                <div class="input-group">
                    <input type="text" id="sq-url" class="form-control font-monospace" readonly
                           style="font-size: 0.8rem;">
                    <button class="btn btn-outline-secondary" type="button" id="sq-copy-url"
                            onclick="sqCopyUrl()">Copy</button>
                </div>
                <div id="sq-copy-feedback" class="form-text" style="color: var(--accent); display:none;">
                    Copied!
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">HTML img tag</label>
                <div class="input-group">
                    <input type="text" id="sq-html" class="form-control font-monospace" readonly
                           style="font-size: 0.8rem;">
                    <button class="btn btn-outline-secondary" type="button"
                            onclick="sqCopyHtml()">Copy</button>
                </div>
            </div>

            <div>
                <a id="sq-download" href="#" download="qrcode.svg"
                   class="btn btn-sm btn-outline-secondary">Download SVG</a>
            </div>
        </div>
    </div>
</div>
</section>

<script>
(function () {
    const BASE = <?php echo json_encode($qrApiUrl); ?>;

    function buildUrl() {
        const data = document.getElementById('sq-data').value.trim();
        if (!data) return '';

        const params = new URLSearchParams({ data });

        const ecc = document.getElementById('sq-ecc').value;
        if (ecc && ecc !== 'H') params.set('ecc_level', ecc);

        const ver = document.getElementById('sq-version').value;
        if (ver && ver !== '5') params.set('qr_version', ver);

        const logoSpace = document.getElementById('sq-logo-space').value;
        if (logoSpace && logoSpace !== '0') params.set('qr_logo_space', logoSpace);

        const logoUrl = document.getElementById('sq-logo-url').value.trim();
        if (logoUrl) params.set('qr_logo_url', logoUrl);

        return BASE + '?' + params.toString();
    }

    function update() {
        const url = buildUrl();

        document.getElementById('sq-url').value = url;
        document.getElementById('sq-html').value = url ? `<img src="${url}" alt="QR code">` : '';
        document.getElementById('sq-download').href = url || '#';

        const preview = document.getElementById('sq-preview');
        if (url) {
            preview.src = url;
        } else {
            preview.src = '';
        }
    }

    ['sq-data', 'sq-ecc', 'sq-version', 'sq-logo-space', 'sq-logo-url'].forEach(function (id) {
        const el = document.getElementById(id);
        el.addEventListener('input', update);
        el.addEventListener('change', update);
    });

    // initial render
    update();

    window.sqCopyUrl = function () {
        const val = document.getElementById('sq-url').value;
        if (!val) return;
        navigator.clipboard.writeText(val).then(function () {
            const fb = document.getElementById('sq-copy-feedback');
            fb.style.display = 'block';
            setTimeout(function () { fb.style.display = 'none'; }, 2000);
        });
    };

    window.sqCopyHtml = function () {
        const val = document.getElementById('sq-html').value;
        if (val) navigator.clipboard.writeText(val);
    };
}());
</script>

<?php echo $this->endSection(); ?>
