#!/usr/local/bin/php5
<?php
include 'include.php';
include 'db_manager.php';
include 'bitcoincz_blocks.php';

$db = new db_manager($mysql_database, $mysql_host, $mysql_username, $mysql_password);

$bitcoincz_blocks = new bitcoincz_blocks($db);

$context = stream_context_create(array(
    'http' => array('ignore_errors' => true)
));

$bitcoincz_stats_url_response = file_get_contents($bitcoincz_stats_url . $bitcoincz_token, false, $context);
preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matches);
$bitcoincz_stats_url_status_code = $matches[1];
switch ($bitcoincz_stats_url_status_code) {
    case '200':
        $statsData = json_decode($bitcoincz_stats_url_response, true);
        $bitcoincz_blocks->regBitcoinczBlocks($statsData);
        break;
    case '401':
        $error[] = array('status' => '401 Unauthorized', 'message' => 'Request was not successful, token is probably invalid.');
        break;
    case '500':
        $error[] = array('status' => '500 Server error', 'message' => 'Server is down for maintenance or there is some unexpected error.');
        break;
    default:
        $error[] = array('status' => $bitcoincz_stats_url_status_code, 'message' => 'Unknown error occurred.');
        break;
}

if(isset($error) and count($error) > 0) {
	$error_msg = '';
	foreach ($error as $key => $val) {
		$error_msg .= $val['status'] . ' ' . $val['message'] . "\n";
	}
	echo $error_msg;
}
?>