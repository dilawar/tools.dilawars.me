<?php echo $this->extend(config('Auth')->views['layout']); ?>

<?php echo $this->section('title'); ?><?php echo lang('Auth.email2FATitle'); ?> <?php echo $this->endSection(); ?>

<?php echo $this->section('main'); ?>

<div class="container d-flex justify-content-center p-5">
    <div class="card col-12 col-md-5 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-5"><?php echo lang('Auth.email2FATitle'); ?></h5>

            <p><?php echo lang('Auth.confirmEmailAddress'); ?></p>

            <?php if (session('error')) { ?>
                <div class="alert alert-danger"><?php echo session('error'); ?></div>
            <?php } ?>

            <form action="<?php echo url_to('auth-action-handle'); ?>" method="post">
                <?php echo csrf_field(); ?>

                <!-- Email -->
                <div class="mb-2">
                    <input type="email" class="form-control" name="email"
                        inputmode="email" autocomplete="email" placeholder="<?php echo lang('Auth.email'); ?>"
                        <?php /** @var CodeIgniter\Shield\Entities\User $user */ ?>
                        value="<?php echo old('email', $user->email); ?>" required>
                </div>

                <div class="d-grid col-8 mx-auto m-3">
                    <button type="submit" class="btn btn-primary btn-block"><?php echo lang('Auth.send'); ?></button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php echo $this->endSection(); ?>
