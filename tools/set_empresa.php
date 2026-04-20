<?php
session_start();
$_SESSION['empresa'] = '1';
echo json_encode(['sid'=>session_id(),'empresa'=>$_SESSION['empresa']]);
