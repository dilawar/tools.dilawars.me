<?php echo $this->extend(config('Auth')->views['layout']); ?>

<?php echo $this->section('title'); ?><?php echo lang('Auth.email2FATitle'); ?> <?php echo $this->endSection(); ?>

<?php echo $this->section('main'); ?>

<div class="container d-flex justify-content-center p-5">
    <div class="card col-12 col-md-5 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-5"><?php echo lang('Auth.emailEnterCode'); ?></h5>

            <p><?php echo lang('Auth.emailConfirmCode'); ?></p>

            <?php if (null !== session('error')) { ?>
            <div class="alert alert-danger"><?php echo session('error'); ?></div>
            <?php } ?>

            <form action="<?php echo url_to('auth-action-verify'); ?>" method="post">
                <?php echo csrf_field(); ?>

                <!-- Code -->
                <div class="mb-2">
                    <input type="number" class="form-control" name="token" placeholder="000000"
                        inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" required>
                </div>

                <div class="d-grid col-8 mx-auto m-3">
                    <button type="submit" class="btn btn-primary btn-block"><?php echo lang('Auth.confirm'); ?></button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php echo $this->endSection(); ?>
