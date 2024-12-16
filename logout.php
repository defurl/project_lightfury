<?php

/* 
        logout.php
        logout functionality
        author: Minh Hieu Tran
    */

session_start();
session_destroy();
header("Location: index.php");
exit();
