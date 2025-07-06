<?php
echo $this->extend('default');
echo $this->section('content');
?>

<main role="main" class="container">
    <?php echo $this->renderSection('main'); ?>
</main>

<?php echo $this->renderSection('pageScripts'); ?>

<?php echo $this->endSection(); ?>
