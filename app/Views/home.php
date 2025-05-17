<?php echo $this->extend('default'); ?>
<?php echo $this->section('content'); ?>


<section>
    <div class="row mx-1">
        <!--
        <div class="col-4">
            <div class="card mx-1">
                <div class="card-body">
                    <h5 class="card-title">Compress Image</h5>
                    <p class="card-text">Yet another tools to compress images.</p>
                    <a href="/tool/compress/image" class="card-link">Open Image Compressor</a>
                </div>
            </div>
        </div>
        -->
        <div class="col-4">
            <div class="card mx-1">
                <div class="card-body">
                    <h5 class="card-title">Convert HEIC image to JPEG</h5>
                    <a href="/tool/convert/heic/jpeg" class="btn btn-primary stretched-link">
                        HEIC to JPEG
                    </a>
                </div>
                <div hidden>
                    <div>
                        <p><strong>Hindi (हिन्दी):</strong> HEIC को JPEG में बदलें</p>
                        <p><strong>Kannada (ಕನ್ನಡ):</strong> HEIC ಅನ್ನು JPEG ಗೆ ಪರಿವರ್ತಿಸಿ</p>
                        <p><strong>Tamil (தமிழ்):</strong> HEIC-ஐ JPEG-ஆக மாற்றவும்</p>
                        <p><strong>Telugu (తెలుగు):</strong> HEIC ను JPEG గా మార్చండి</p>
                        <p><strong>Marathi (मराठी):</strong> HEIC चे JPEG मध्ये रूपांतर करा</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php echo $this->endSection(); ?>
