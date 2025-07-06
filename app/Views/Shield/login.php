<?php echo $this->extend(config('Auth')->views['layout']); ?>

<?php echo $this->section('title'); ?><?php echo lang('Auth.login'); ?> <?php echo $this->endSection(); ?>

<?php echo $this->section('main'); ?>

    <div class="d-flex justify-content-center p-5">
        <div class="col-12 col-md-5">
            <div class="card-body">
                <h5 class="card-title mb-5"><?php echo lang('Auth.login'); ?></h5>

                <?php if (null !== session('error')) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo session('error'); ?>
                    </div>
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

                <?php if (null !== session('message')) { ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo session('message'); ?>
                    </div>
                <?php } ?>

                <form action="<?php echo url_to('login'); ?>" method="post">

                    <?php echo csrf_field(); ?>

                    <!-- Email -->
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="floatingEmailInput" name="email" inputmode="email" autocomplete="email" placeholder="<?php echo lang('Auth.email'); ?>" value="<?php echo old('email'); ?>" required>
                        <label for="floatingEmailInput"><?php echo lang('Auth.email'); ?></label>
                    </div>

                    <!-- Password -->
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="floatingPasswordInput" name="password" inputmode="text" autocomplete="current-password" placeholder="<?php echo lang('Auth.password'); ?>" required>
                        <label for="floatingPasswordInput"><?php echo lang('Auth.password'); ?></label>
                    </div>

                    <!-- Remember me -->
                    <?php if (setting('Auth.sessionConfig')['allowRemembering']) { ?>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" name="remember" class="form-check-input" <?php if (old('remember')) { ?> checked<?php } ?>>
                                <?php echo lang('Auth.rememberMe'); ?>
                            </label>
                        </div>
                    <?php } ?>

                    <div class="d-grid col-12 col-md-8 mx-auto m-3">
                        <button type="submit" class="btn btn-primary btn-block"><?php echo lang('Auth.login'); ?></button>
                    </div>

                    <?php if (setting('Auth.allowMagicLinkLogins')) { ?>
                        <p class="text-center"><?php echo lang('Auth.forgotPassword'); ?> <a href="<?php echo url_to('magic-link'); ?>"><?php echo lang('Auth.useMagicLink'); ?></a></p>
                    <?php } ?>

                    <?php if (setting('Auth.allowRegistration')) { ?>
                        <p class="text-center"><?php echo lang('Auth.needAccount'); ?> <a href="<?php echo url_to('register'); ?>"><?php echo lang('Auth.register'); ?></a></p>
                    <?php } ?>

                </form>
            </div>
        </div>
    </div>

<?php echo $this->endSection(); ?>
