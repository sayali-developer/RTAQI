<?php

$this->layout('layouts/blank', [
    'pageTitle' => "Error 404 - Page not found",
]);
?>


<div class="container d-flex flex-column">
    <div class="row vh-100">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
            <div class="d-table-cell align-middle">
                <div class="card">
                    <div class="card-body">
                        <div class="m-sm-3 text-center">
                            <h1 class="text-danger">Error 404 : Page Not Found</h1>
                            <p class="pt-3"><strong>Request URL : </strong> <?= htmlspecialchars($_SERVER["REQUEST_URI"])  ?></p>
                        </div>
                    </div>
                </div>
                <div class="text-center mb-3">
                    Got lost? <a href="/">Back to Homepage</a>
                </div>
            </div>
        </div>
    </div>
</div>
