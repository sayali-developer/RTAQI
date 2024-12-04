<?php
$this->layout('layouts/blank', [
    'pageTitle' => "Reset Password"
]);
?>

<?php if (isset($email)):?>
<div class="container d-flex flex-column">
    <div class="row vh-100">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
            <div class="d-table-cell align-middle">

                <div class="text-center mt-4">
                    <h1 class="h2">Welcome back</h1>
                    <p class="lead">
                        <strong>Reset Password for <?= $email ?></strong>
                    </p>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="m-sm-3">
                            <form action="/reset-password/" method="post">

                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input MINLENGTH="8" id="password" class="form-control form-control-lg" type="password" name="password" placeholder="Enter your password" required="1" />
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input MINLENGTH="8" id="confirm_password" class="form-control form-control-lg" type="password" name="confirm_password" placeholder="Confirm your password" required="1" />
                                </div>
                                <div class="d-grid gap-2 mt-3">
                                    <input type="hidden" name="code" value="<?= $code?>" />
                                    <button type="submit" class="btn btn-lg btn-primary">Reset Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else : ?>
    <div class="container d-flex flex-column">
        <div class="row vh-100">
            <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
                <div class="d-table-cell align-middle">



                    <div class="card">
                        <div class="card-body">
                            <div class="m-sm-3">
                                <div class="text-center mb-4">
                                    <h1 class="h2">Reset Password</h1>
                                </div>
                                <form method="post" action="/reset-password/">
                                    <?php if (isset($message)): ?>
                                        <div class="container-fluid py-3 mb-3 text-white bg-<?= $message['type'] ?>">
                                            <strong><?= $message["text"] ?></strong>
                                        </div>
                                    <?php else :?>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Enter Your Email</label>
                                        <input id="email" class="form-control form-control-lg" type="email" name="email" placeholder="Enter your email" required="1" />
                                    </div>

                                    <div class="d-grid gap-2 mt-3">
                                        <button type="submit" class="btn btn-lg btn-primary">Reset Password</button>
                                    </div>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mb-3">
                        Already have an account? <a href="/login">Login</a><br />
                        Don't have an account? <a href="/register/">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>




