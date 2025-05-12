<?php echo $this->extend('default'); ?>
<?php echo $this->section('content'); ?>

<?php if (! user()) { ?>

    <p>Please <a href="/home/login">Login</a> to continue.</p>

<?php } else { ?>
    <p class="mt-3"> Welcome 
        <span class="text-primary"> <?php echo user(); ?>!</span>. 
        Your roles are <?php echo implode(' & ', userRoles()) ?>.
    </p> 

    <div class="row d-flex justify-content-between" class="h3">
        <a class="col btn btn-light m-3" href="/home/logout">Logout</a>
        <a class="col btn btn-info m-3" href="/ehr">EHR Form</a>
        <a class="col btn btn-info m-3" href="/trainer">Trainer Home</a>
    </div>
<?php } ?>

<?php echo $this->endSection(); ?>
