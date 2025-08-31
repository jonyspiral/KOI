<?php
ini_set('display_errors',1); error_reporting(E_ALL);
session_start();
header('Content-Type: text/plain; charset=utf-8');
print_r($_SESSION);
