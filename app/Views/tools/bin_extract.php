<?php

echo $this->extend('default');
echo $this->section('content');

?>

<h1 class="section-title">Binary Extractor</h1>

<form>
    <label for="file">Upload File</label>
    <input id="file" type="file" />
</form>


<?php echo $this->endSection(); ?>
