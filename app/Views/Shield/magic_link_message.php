<?php echo $this->extend(config('Auth')->views['layout']); ?>

<?php echo $this->section('title'); ?><?php echo lang('Auth.useMagicLink'); ?> <?php echo $this->endSection(); ?>

<?php echo $this->section('main'); ?>

<div class="container d-flex justify-content-center p-5">
    <div class="col-12 col-md-5">
        <div class="card-body">
            <h5 class="card-title mb-5"><?php echo lang('Auth.useMagicLink'); ?></h5>

            <p><b><?php echo lang('Auth.checkYourEmail'); ?></b></p>

            <p><?php echo lang('Auth.magicLinkDetails', [setting('Auth.magicLinkLifetime') / 60]); ?></p>
        </div>
    </div>
</div>

<?php echo $this->endSection(); ?>
