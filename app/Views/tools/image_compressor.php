<?php
echo $this->extend('default');
echo $this->section('content');

use App\Data\ToolActionName;
?>

<h1 class="section-title">Image Compressor</h1>

<section>
<?php
echo form_open_multipart("tool/compress/action/" . ToolActionName::CompressImage->value);
echo form_upload("image");
echo form_submit("submit", "Compress", extra: [
    'class' => 'btn btn-primary',
]);
echo "</form>";
?>
</section>

<?php echo $this->endSection(); ?>
