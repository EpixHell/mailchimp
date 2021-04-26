<?php
use \DrewM\MailChimp\MailChimp;
use \DrewM\MailChimp\Batch;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('ROOT', './');

session_start();

require_once(ROOT.'lib/autoload.php');
require_once(ROOT.'vendor/autoload.php');

$request_method = $_SERVER["REQUEST_METHOD"];
if(!empty($_SERVER["HTTP_X_HTTP_METHOD_OVERRIDE"])){$request_method = $_SERVER["HTTP_X_HTTP_METHOD_OVERRIDE"];}


    /*$fichier = "./test_dev_db.csv";
    $count = 0;
    if($fp = fopen($fichier,"r")){
        $membre = new \models\users();
        while(!feof($fp)){
            $ligne = fgets($fp,4096);
            if($count>0){
                $temp=str_replace('\n','',$ligne);
                $temp=str_replace('\r','',$temp);
                $temp=str_replace('"','',$temp);
                if(!empty($temp)){
                    $liste = explode(',',$temp);
                    $membre->post("op1", "lists/77964287b8/members", [
                        'email_address' => $liste[0],
                        'status'        => 'subscribed',
                        'merge_fields' => ['FNAME'=>$liste[1], 'LNAME'=>$liste[2]]
                    ]);
                }
            }
            $count++;
        }
        $result = $membre->execute();
        var_dump($result);
        fclose($fp);
    }*/

    /*$membre = new \models\users();
    $result = $membre->check_status('1434e0a242');
    var_dump($result);*/


?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Test</title>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="./">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./list">liste</a>
            </li>
            <!-- <li class="nav-item active">
                <a class="nav-link" href="./import">import</a>
            </li> -->
            </ul>
        </div>
    </nav>
    <!-- <form method="post"  enctype="multipart/form-data">
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="customFile">
            <label class="custom-file-label" for="customFile">Choose file</label>
        </div>
        <button type="submit" class="btn btn-primary">modifier</button>
    </form> -->


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>