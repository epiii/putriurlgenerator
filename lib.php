<?php

// debug value of "variable"
function pr($par){
  echo "<pre>";
    print_r($par);
  echo"</pre>";
  exit();
}

/*function preProcess($no){
  if(is_numeric($no)){ // 
    $no = str_replace(" ","",$no);
    $no = str_replace(".","",$no);
    $no = str_replace("-","",$no);
    $no = str_replace("(","",$no);
    $no = str_replace(")","",$no);

    if(substr($no, 0,1)==='0'){ // ex: 085655009393
      $_1startNo = substr($no, 1); // 85655009393
      $_1digitNo = substr($no,1,1); // 8
      $_2digitNo = substr($no,1,2); // 85
      $_totDigit = strlen($_1startNo); // 11 (start from 8,..)
      phoneProcess($_1startNo,$_1digitNo,$_2digitNo);
    }elseif(substr($no, 0,1)!=='+'){ // ex: +6285655009393
      $_1startNo = $no; // 85655009393
      $_1digitNo = substr($no,0,1); // 8
      $_2digitNo = substr($no,0,2); // 85
      $_totDigit = strlen($_1startNo); // 11 (start from 8,..)
    }else{ // ex : 85655009393
      $_1startNo = $no; // 85655009393
      $_1digitNo = substr($no,0,1); // 8
      $_2digitNo = substr($no,0,2); // 85
      $_totDigit = strlen($_1startNo); // 11 (start from 8,..)
      phoneProcess($_1startNo,$_1digitNo,$_2digitNo);
    }
  }
}
*/
// convert "format" of phone number
// function phoneProcess($_totDigit,$_1startNo,$_1digitNo,$_2digitNo){
function phoneProcess($no){
  $res['digit']=null;
  $res['prefixLocal']=null;
  $res['prefixInter']=null;
  $res['number']=null;
  $res['numberPlus']=null;
  $res['country']=null;

  if (is_numeric($no)) { // input must be number 
    require 'koneksi.php';
    $no = str_replace(" ","",$no);
    $no = str_replace("+","",$no);
    $no = str_replace("-","",$no);
    $no = str_replace("(","",$no);
    $no = str_replace(")","",$no);
    $no = str_replace(".","",$no);

    // if(substr($no, 0,1)==='0'){ // ex: 085655009393
      $_1startNo = substr($no, 1); // 85655009393
      $_1digitNo = substr($no,1,1); // 8
      $_2digitNo = substr($no,1,2); // 85
      $_digitNo  = strlen($_1startNo);
    // }else{ // ex : 85655009393
    //   $_1startNo = $no; // 85655009393
    //   $_1digitNo = substr($no,0,1); // 8
    //   $_2digitNo = substr($no,0,2); // 85
    //   $_digitNo  = strlen($_1startNo)-1;
    // }

    $s='SELECT nama,param2,param3,param4
        FROM parameter
        WHERE
          param1="nomor" AND
          param4 LIKE "%'.$_digitNo.'%"
        ORDER BY
          CHAR_LENGTH(param3) DESC';
    $e=mysqli_query($conn,$s);
    $n=mysqli_num_rows($e);
    // pr($n);

    if($n>0){ // data is exist
      // loop from table : parameter
      while ($r=mysqli_fetch_assoc($e)) {
        if(strpos($r['param3'],',')==false){ // not contain (,) / only 1 digit (ex : indonesia : 8)
          if($r['param3']==$_1digitNo){ // 8 == 8
            $prefixInterPlus = $r['nama']; // +62
            $prefixInter = substr($r['nama'],1); // 62
            $prefixLocal = $r['param3']; // 8
            $digit = $r['param4']; // 9,10,11
            $country = $r['param2']; // indonesia
            break ; // exit from "while" loop
          }
        } else { // exist (,) / more than 1 digit (ex : india : 7,8,90,dst..)
          $param3s=explode(',',$r['param3']);
          foreach ($param3s as $param3) {
            if($param3==$_1digitNo || $param3==$_2digitNo){
              $prefixInterPlus = $r['nama']; // +91
              $prefixInter = substr($r['nama'],1); // 91
              $digit = $r['param4']; // 10
              $prefixLocal = $r['param3']; // 7,8,90,...
              $country = $r['param2']; // india
              break 2; // exit from "foreach & while" loop
            } // end of if
          } // end of foreach
        } // end of else
      } // end of while

      // store value to array
      $res=[
        'digit'=>$digit,
        'prefixLocal'=>$prefixLocal,
        'prefixInter'=>$prefixInter,
        'numberPlus'=>$prefixInterPlus.$_1startNo,
        'number'=>$prefixInter.$_1startNo,
        'country'=>$country,
      ];
    } // end of 'if'
  }
  return $res;
 } // end of function

 function getDigit($no){
   $ret = phoneProcess($no);
   return is_null($ret['digit'])?'unknown':$ret['digit'];
 }

 function getNumber($no){
   $ret = phoneProcess($no);
   return is_null($ret['number'])?'unknown':$ret['number'];
 }

 function getNumberPlus($no){
   $ret = phoneProcess($no);
   return is_null($ret['numberPlus'])?'unknown':$ret['numberPlus'];
 }

function getCountry($no){
  $ret = phoneProcess($no);
  return is_null($ret['country'])?'unknown':$ret['country'];
}

function getPrefixInter($no){
  $ret = phoneProcess($no);
  return is_null($ret['prefixInter'])?'unknown':$ret['prefixInter'];
}

function getPrefixLocal($no){
  $ret = phoneProcess($no);
  return is_null($ret['prefixLocal'])?'unknown':'0'.$ret['prefixLocal'];
}

function getWAbyNumber($no,$msg){
  $urlx = 'https://wa.me/'.getNumber($no).'/?text='.urlencode($msg);
  // $urlx = 'https://wa.me/'.getNumber($no).'/?text="'.urlencode($msg).'"';
  return '<a href="'.$urlx.'" target="_blank">'.$urlx.'</a>';
}

function getWAbyUsername($username,$msg){
  require 'koneksi.php';
  $s = 'SELECT no_wa FROM pengguna WHERE username="'.$username.'"';
  $e = mysqli_query($conn,$s);
  $r = mysqli_fetch_assoc($e);
  return getWAbyNumber($r['no_wa'],$msg);
}

