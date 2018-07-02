<?php
require 'koneksi.php';
require 'lib.php';

$out=[];
if(!isset($_POST)){ // error
  $out=['status'=>'invalid_request'];
} else { // tidak error
  if($_POST['mode']=='phoneConvert'){ // check nomor
    $out=[
      'status'=>'phoneConvert',
      'number'=>getNumber($_POST['number']),
      'country'=>getCountry($_POST['number']),
      'digit'=>getDigit($_POST['number']),
    ];
  } elseif ($_POST['mode']=='phoneSave'){ // create / update
    // sql here ......
    $out=[
      'status'=>true,
    ];
  }
}
echo json_encode($out);
?>
