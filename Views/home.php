<?php
$this->layout('layouts/blank', [
    'pageTitle' => "Home"
]);
?>

<div class="container">
    <center><h1 class='bg-warning text-white cursor-pointer py-3 mb-3' onclick="window.location.href = '/login/'">Login For More Data</h1></center>
    <h1 class="text-center my-4 text-danger">Air Quality Dashboard</h1>

    <div class="row" id="widgets-container">
        <center><h1 class="my-5">Loading Data...</h1></center>
    </div>
</div>
<script src="/assets/js/home.js" type="text/javascript"></script>
