<?php
    $this->layout('layouts/blank', [
        'pageTitle' => "Login"
    ]);

    if (isset($_GET['message'])) {
        $message = [
            "type" => "success",
            "text" => urldecode($_GET['message'])
        ];
    }
?>

<div class="container d-flex flex-column">
    <div class="row vh-100">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
            <div class="d-table-cell align-middle">

                <div class="text-center mt-4">
                    <h1 class="h2">Welcome back to <?= APP_NAME ?></h1>
                    <p class="lead">
                        Sign in to your account to continue
                    </p>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="m-sm-3">
                            <form method="POST" action="">
                                <?php if (isset($message)) : ?>
                                <div class="container-fluid py-3 mb-3 text-white bg-<?= $message['type'] ?>">
                                    <strong><?= $message["text"] ?></strong>
                                </div>
                                <?php endif;?>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input id="email" class="form-control form-control-lg" type="email" name="email" placeholder="Enter your email" required="1" autofocus="1" />
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input id="password" MINLENGTH="8" class="form-control form-control-lg" type="password" name="password" placeholder="Enter your password" required="1" />
                                </div>
                                <div class="d-grid gap-2 mt-3">
                                    <button type="submit" class="btn btn-lg btn-primary">Sign in</button>
                                </div>

                                <div class="my-2">
                                    <div class="text-center">
                                        Forgot Password? <a href="/reset-password/">Reset It</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="text-center mb-3">
                    Don't have an account? <a href="/register/">Register</a>
                </div>
            </div>
        </div>
    </div>
</div>