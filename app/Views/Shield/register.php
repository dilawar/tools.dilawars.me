<?php echo $this->extend(config('Auth')->views['layout']); ?>

<?php echo $this->section('title'); ?>
<?php echo lang('Auth.register'); ?> 
<?php echo $this->endSection(); ?>

<?php echo $this->section('main'); ?>

    <div class="container d-flex justify-content-center p-5">
        <div class="card col-12 col-md-5 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-5"><?php echo lang('Auth.register'); ?></h5>

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

                <form action="<?php echo url_to('register'); ?>" method="post">
                    <?php echo csrf_field(); ?>

                    <!-- Email -->
                    <div class="form-floating mb-2">
                        <input type="email" class="form-control" id="floatingEmailInput" name="email" inputmode="email" autocomplete="email" placeholder="<?php echo lang('Auth.email'); ?>" value="<?php echo old('email'); ?>" required>
                        <label for="floatingEmailInput"><?php echo lang('Auth.email'); ?></label>
                    </div>

                    <!-- Username -->
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="floatingUsernameInput" name="username" inputmode="text" autocomplete="username" placeholder="<?php echo lang('Auth.username'); ?>" value="<?php echo old('username'); ?>" required>
                        <label for="floatingUsernameInput"><?php echo lang('Auth.username'); ?></label>
                    </div>

                    <!-- Password -->
                    <div class="form-floating mb-2">
                        <input type="password" class="form-control" id="floatingPasswordInput" name="password" inputmode="text" autocomplete="new-password" placeholder="<?php echo lang('Auth.password'); ?>" required>
                        <label for="floatingPasswordInput"><?php echo lang('Auth.password'); ?></label>
                    </div>

                    <!-- Password (Again) -->
                    <div class="form-floating mb-5">
                        <input type="password" class="form-control" id="floatingPasswordConfirmInput" name="password_confirm" inputmode="text" autocomplete="new-password" placeholder="<?php echo lang('Auth.passwordConfirm'); ?>" required>
                        <label for="floatingPasswordConfirmInput"><?php echo lang('Auth.passwordConfirm'); ?></label>
                    </div>

                    <div class="d-grid col-12 col-md-8 mx-auto m-3">
                        <button type="submit" class="btn btn-primary btn-block"><?php echo lang('Auth.register'); ?></button>
                    </div>

                    <p class="text-center"><?php echo lang('Auth.haveAccount'); ?> <a href="<?php echo url_to('login'); ?>"><?php echo lang('Auth.login'); ?></a></p>

                </form>
            </div>
        </div>
    </div>

<?php echo $this->endSection(); ?>
