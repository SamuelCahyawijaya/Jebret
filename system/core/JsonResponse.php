<?php
const NO_RESULT_ERROR = 1418;
const DATABASE_ERROR = 4219;
const EVENT_CLOSED_ERROR = 6107;
const INCOMPLETE_PARAMETER_ERROR = 9164;
const INCOMPLETE_URI_ERROR = 2153;
const EXPIRED_FB_TOKEN = 7538;
const INVALID_LOCATION_ERROR = 5280;
const INVALID_CANCEL_TIME_ERROR = 3784; 
const UPLOAD_FILE_ERROR = 8941; 
const USER_ALREADY_EXIST_ERROR = 1395;  
const RENEW_TOKEN_NEEDED_ERROR = 4072;
const GROUP_ALREADY_EXIST_ERROR = 6429;
const EVENT_ALREADY_EXIST_ERROR = 9836;
const SEND_MAIL_ERROR = 2397;
const PUNISHMENT_DELETED_ERROR = 7970;
const UNKNOWN_ERROR = 1111;

function print_response($data) 
{
	$response['status'] = 'success';
	$response['data'] = $data;
	echo json_encode($response);
}

function print_success() 
{
	$response['status'] = 'success';
	echo json_encode($response);
}

function print_error($error_code) 
{
	$response['status'] = 'failed';
	$response['error_code'] = $error_code;
	echo json_encode($response);
}
?>