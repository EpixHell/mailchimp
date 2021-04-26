<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('ROOT', './');

session_start();

require_once(ROOT.'lib/autoload.php');
require_once(ROOT.'vendor/autoload.php');

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header("Access-Control-Allow-Headers: Content-Type, Authorization, Access-Control-Allow-Headers, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

$request_method = $_SERVER["REQUEST_METHOD"];
if(!empty($_SERVER["HTTP_X_HTTP_METHOD_OVERRIDE"])){$request_method = $_SERVER["HTTP_X_HTTP_METHOD_OVERRIDE"];}

if (in_array($request_method, ["GET", "POST", "PUT", "DELETE", "OPTIONS"])) {
	$requestMethodArray = array();
	$requestMethodArray = $_REQUEST;
	
	switch($request_method){
		case 'GET':
            $membre = new \models\users();
            $list = $membre->lire($requestMethodArray['offset']);
            if ($membre->success()) {
                $res['success'] = true;
                //echo '<pre>';print_r($list);echo '</pre>';
                //$_SESSION['list'] = $list;
                //$list = $_SESSION['list'];
                //var_dump($requestMethodArray['offset']);
                //echo '<pre>';print_r($list);echo '</pre>';
                $res["total_items"] = $list["total_items"];
                $res["liste"] = array();
                foreach($list['members'] as $seq=>$member){
                    extract($member);
                    $list_item=array(
                        "seq" => $seq,
                        "id" => $id,
                        "email_address" => $email_address,
                        "FNAME" => $merge_fields["FNAME"],
                        "LNAME" => $merge_fields["LNAME"]
                    );
                    array_push($res["liste"], $list_item);
                }
            } else {
                $res['success'] = false;
                $res['message'] = $membre->getLastError();
            }
			
			echo json_encode($res);
			break;
        case 'POST':
            $errors = array();
            $res = array();
            if (empty($requestMethodArray['FNAME'])) {$errors['FNAME'] = 'FNAME is required.';}
            if (empty($requestMethodArray['LNAME'])) {$errors['LNAME'] = 'LNAME is required.';}
            if (empty($requestMethodArray['email_address'])) {$errors['email_address'] = 'Email is required.';}
            if (!empty($errors)) {
                $res['success'] = false;
                $res['errors'] = $errors;
            } else {
                $membre = new \models\users();
                $list = $membre->add($requestMethodArray);
                if ($membre->success()) {
                    $res['success'] = true;
                    $res['message'] = 'OK';
                } else {
                    $res['success'] = false;
                    $res['message'] = $membre->getLastError();
                }
            }

            echo json_encode($res);
            break;
        case 'PUT':
            $errors = array();
            $res = array();
            if (empty($requestMethodArray['FNAME'])) {$errors['FNAME'] = 'FNAME is required.';}
            if (empty($requestMethodArray['LNAME'])) {$errors['LNAME'] = 'LNAME is required.';}
            if (empty($requestMethodArray['email_address'])) {$errors['email_address'] = 'Email is required.';}
            if (!empty($errors)) {
                $res['success'] = false;
                $res['errors'] = $errors;
            } else {
                $membre = new \models\users();
                $list = $membre->modif($requestMethodArray);
                if ($membre->success()) {
                    $res['success'] = true;
                    $res['message'] = 'OK';
                } else {
                    $res['success'] = false;
                    $res['message'] = $membre->getLastError();
                }
            }
            
            echo json_encode($res);
            break;
        case 'DELETE':
            var_dump($requestMethodArray);
            if (empty($requestMethodArray['email_address'])) {$errors['email_address'] = 'Email is required.';}
            if (!empty($errors)) {
                $res['success'] = false;
                $res['errors'] = $errors;
            } else {
                $membre = new \models\users();
                $list = $membre->delete($requestMethodArray['email_address']);
                if ($membre->success()) {
                    $res['success'] = true;
                    $res['message'] = 'OK';
                } else {
                    $res['success'] = false;
                    $res['message'] = $membre->getLastError();
                }
            }
            
            echo json_encode($res);
            break;
		default:
            http_response_code(405);
			$res['message'] = 'Not Allowed';
			echo json_encode($res);
			break;
	}
}