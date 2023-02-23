<?php 
require 'function.php';

if(isset($_SESSION['login'])){
    // lanjut
} else {
    // login dulu
    header('location:login.php');
}

?>