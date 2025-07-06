<?php echo $this->extend(config('Auth')->views['layout']); ?>

<?php echo $this->section('title'); ?><?php echo lang('Auth.useMagicLink'); ?> <?php echo $this->endSection(); ?>

<?php echo $this->section('main'); ?>

<div class="container d-flex justify-content-center p-5">
    <div class="col-12 col-md-5">
        <div class="card-body">
            <h5 class="card-title mb-5"><?php echo lang('Auth.useMagicLink'); ?></h5>

                <?php if (null !== session('error')) { ?>
                    <div class="alert alert-danger" role="alert"><?php echo session('error'); ?></div>
                <?php } elseif (null !== session('errors')) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php if (is_array(session('errors'))) { ?>
                            <?php foreach (session('errors') as $error) { ?>
                                <?php echo $error; ?>
                                <br>
                            <?php } ?>
                        <?php } else { ?>
                            <?php echo session('errors'); ?>
                        <?php } ?>
                    </div>
                <?php } ?>

            <form action="<?php echo url_to('magic-link'); ?>" method="post">
                <?php echo csrf_field(); ?>

                <!-- Email -->
                <div class="form-floating mb-2">
                    <input type="email" class="form-control" id="floatingEmailInput" name="email" autocomplete="email" placeholder="<?php echo lang('Auth.email'); ?>"
                           value="<?php echo old('email', auth()->user()->email ?? null); ?>" required>
                    <label for="floatingEmailInput"><?php echo lang('Auth.email'); ?></label>
                </div>

                <div class="d-grid col-12 col-md-8 mx-auto m-3">
                    <button type="submit" class="btn btn-primary btn-block"><?php echo lang('Auth.send'); ?></button>
                </div>

            </form>

            <p class="text-center"><a href="<?php echo url_to('login'); ?>"><?php echo lang('Auth.backToLogin'); ?></a></p>
        </div>
    </div>
</div>

<?php echo $this->endSection(); ?>
