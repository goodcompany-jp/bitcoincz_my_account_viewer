<?php
ini_set('display_errors', 1);
error_reporting(-1);

//Manage API token form https://mining.bitcoin.cz/accounts/token-manage/
$bitcoincz_token = 'YOUR API TOKEN';
$bitcoincz_stats_url = 'https://mining.bitcoin.cz/stats/json/';
$bitcoincz_profile_url = 'https://mining.bitcoin.cz/accounts/profile/json/';

//Database
$mysql_host = 'HOST NAME';
$mysql_username = 'USER NAME';
$mysql_password = 'PASSWORD';
$mysql_database = 'DB_NAME';

//Display threshold block no
$threshold_block_no = 0;
?>