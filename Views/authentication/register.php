<?php
$this->layout('layouts/blank', [
    'pageTitle' => "Register"
]);
?>
<div class="container d-flex flex-column">
    <div class="row vh-100">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
            <div class="d-table-cell align-middle">
                <div class="text-center mt-4">
                    <h1 class="h2">Get started</h1>
                    <p class="lead">
                        Start monitoring the environment at your fingertips.
                    </p>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="m-sm-3">
                            <?php if (isset($message)): ?>
                            <div class="container-fluid py-3 mb-3 text-white bg-<?= $message['type'] ?>">
                                <strong><?= $message["text"] ?></strong>
                            </div>
                            <?php endif; ?>
                            <form method="post" action="/register/">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full name</label>
                                    <input id="name" required class="form-control form-control-lg" type="text" name="name" placeholder="Enter your name" />
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input required class="form-control form-control-lg" type="email" id="email" name="email" placeholder="Enter your email" />
                                </div>
                                <div class="d-grid gap-2 mt-3">
                                    <button type="submit" class="btn btn-lg btn-primary">Sign up</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="text-center mb-3">
                    Already have account? <a href="/login/">Log In</a>
                </div>
            </div>
        </div>
    </div>
</div>