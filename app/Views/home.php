<?php echo $this->extend('default'); ?>
<?php echo $this->section('content'); ?>

<section>
    <div class="row mx-1">
        <div class="col-4">
            <div class="card mx-1">
                <div class="card-body">
                    <h5 class="card-title">Compress Image</h5>
                    <p class="card-text">Yet another tools to compress images.</p>
                    <a href="/tool/compress/image" class="card-link">Open Image Compressor</a>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card mx-1">
                <div class="card-body">
                    <h5 class="card-title">Heic To JPEG</h5>
                    <p class="card-text">Convert HEIC image to ubiquitous JPEG</p>
                    <a href="/tool/convert/heic/jpeg" class="card-link">HEIC to JPEG</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php echo $this->endSection(); ?>
