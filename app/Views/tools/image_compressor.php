<?php
echo $this->extend('default');
echo $this->section('content');
?>

<h1 class="section-title">Image Compressor</h1>

<section>
<?php
echo form_open_multipart("/tools/compress/image/action");
echo form_upload("image");
echo form_submit("submit", "Compress", extra: [
    'class' => 'btn btn-primary',
]);
echo "</form>";
?>
</section>

<?php echo $this->endSection(); ?>
