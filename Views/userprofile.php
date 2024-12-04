<?php
$this->layout('layouts/user');
?>
<div class="container-fluid p-0">

    <h1 class="h3 mb-3"><strong>My Profile</strong></h1>
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <table class="table table-hover my-0">
                        <tr>
                            <th colspan="2" class="text-center bg-primary text-white">My Profile</th>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <th class="d-none d-xl-table-cell"><?= $_SESSION["full_name"] ?></th>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <th class="d-none d-xl-table-cell"><?= $_SESSION["email"] ?></th>
                        </tr>
                        <tr>
                            <th>Active Status</th>
                            <th class="d-none d-xl-table-cell">Active</th>
                        </tr>
                        <tr>
                            <th colspan="2" class="cursor-pointer text-center bg-danger text-white" onclick="window.location.href='/logout/'">Logout</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
