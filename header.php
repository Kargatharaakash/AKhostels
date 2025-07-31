<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Hostel Management System - <?php echo $pageTitle; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="bootstrap.min.css" rel="stylesheet">
    <!-- jQuery (optional, for legacy scripts) -->
    <script src="jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css.css" type="text/css">
    <script src="js.js"></script>
</head>
<body>
<div class="container my-4">
    <?php include("nav.php"); ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h1 class="h3 m-0"><?php echo $pageTitle; ?></h1>
        </div>
        <div class="card-body">
