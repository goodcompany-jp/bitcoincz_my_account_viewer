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


$bitcoincz_profile_url_response = file_get_contents($bitcoincz_profile_url . $bitcoincz_token, false, $context);
preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matches);
$bitcoincz_profile_url_status_code = $matches[1];
switch ($bitcoincz_profile_url_status_code) {
    case '200':
        $profileData = json_decode($bitcoincz_profile_url_response, true);
        break;
    case '401':
        $error[] = array('status' => '401 Unauthorized', 'message' => 'Request was not successful, token is probably invalid.');
        break;
    case '500':
        $error[] = array('status' => '500 Server error', 'message' => 'Server is down for maintenance or there is some unexpected error.');
        break;
    default:
        $error[] = array('status' => $bitcoincz_profile_url_status_code, 'message' => 'Unknown error occurred.');
        break;
}

$totalReward = $bitcoincz_blocks->getTotalReward($threshold_block_no);

$history = $bitcoincz_blocks->getBitcoinczBlocks($threshold_block_no);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>BITCOINCZ Bitcoin block history</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap-theme.min.css">

    <!-- My CSS -->
    <link rel="stylesheet" href="style.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>

    <!-- Latest compiled and minified JavaScript -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>

    <script>
    $('#myTab a').click(function (e) {
      e.preventDefault()
      $(this).tab('show')
    })
    </script>
  </head>
  <body id="top">
<header>
    <div class="container">
        <h1>BITCOINCZ <small>My account viewer</small></h1>
<?php if(isset($error) and count($error) > 0): ?>
        <div class="alert alert-danger">
<?php foreach ($error as $key => $val): ?>
            <strong><?php echo $val['status'] ?></strong> <?php echo $val['message'] ?>
            <br>
<?php endforeach; ?>
        </div>
<?php endif; ?>
        <nav>
            <ul class="nav nav-tabs" id="myTab">
              <li class="active"><a href="#history" data-toggle="tab">history</a></li>
              <li><a href="#statistics" data-toggle="tab">statistics</a></li>
              <li><a href="#account" data-toggle="tab">account</a></li>
              <li><a href="#donation" data-toggle="tab">donation</a></li>
            </ul>
        </nav>
    </div>
</header>
<div class="container theme-showcase">
    <div class="tab-content">
        <div class="table-responsive tab-pane active" id="history">
         <h2>Bitcoin block history</h2>
         <p>All times are in UTC.</p>
         <table class="table table-striped table-bordered">
            <thead> 
              <tr>
                <th>block_no</th>
                <th>date_started</th>
                <th>date_found</th>
                <th>mining_duration</th>
                <th>total_shares</th>
                <th>total_score</th>
                <th>reward</th>
                <th>confirmations</th>
                <th>is_mature</th>
                <th>nmc_reward</th>
              </tr>
            </thead>
            <tbody>
        <?php foreach ($history as $key => $val): ?>
            <tr>
            <td><a href="https://blockchain.info/search/<?php echo $val['block_no']; ?>" target="_blank"><?php echo $val['block_no']; ?></a></td>
            <td><?php echo $val['date_started']; ?></td>
            <td><?php echo $val['date_found']; ?></td>
            <td><?php echo $val['mining_duration']; ?></td>
            <td><?php echo $val['total_shares']; ?></td>
            <td><?php echo $val['total_score']; ?></td>
            <td><?php echo $val['reward']; ?></td>
            <td><?php echo $val['confirmations']; ?></td>
            <td><?php echo $val['is_mature']; ?></td>
            <td><?php echo $val['nmc_reward']; ?></td>
            </tr>
        <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
              <th>total:</th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th><?php echo $totalReward; ?></th>
              <th></th>
              <th></th>
              <th></th>
              </tr>
            </tfoot>
         </table>
        </div>


        <div class="tab-pane table-responsive" id="statistics">
         <h2>System statistics</h2>
         <p>All times are in UTC.</p>
         <table class="table table-striped table-bordered">
            <tbody>
            <tr>
                <td>round_started:</td>
                <td><?php echo $statsData['round_started']; ?></td>
            </tr>
            <tr>
                <td>round_duration:</td>
                <td><?php echo $statsData['round_duration']; ?></td>
            </tr>
            <tr>
                <td>shares_cdf:</td>
                <td><?php echo $statsData['shares_cdf']; ?></td>
            </tr>
            <tr>
                <td>luck_1:</td>
                <td><?php echo $statsData['luck_1']; ?></td>
            </tr>
            <tr>
                <td>luck_7:</td>
                <td><?php echo $statsData['luck_7']; ?></td>
            </tr>
            <tr>
                <td>luck_30:</td>
                <td><?php echo $statsData['luck_30']; ?></td>
            </tr>
            <tr>
                <td>shares:</td>
                <td><?php echo $statsData['shares']; ?></td>
            </tr>
            <tr>
                <td>score:</td>
                <td><?php echo $statsData['score']; ?></td>
            </tr>
            <tr>
                <td>ghashes_ps:</td>
                <td><?php echo $statsData['ghashes_ps']; ?></td>
            </tr>
            <tr>
                <td>active_workers:</td>
                <td><?php echo $statsData['active_workers']; ?></td>
            </tr>
            <tr>
                <td>active_stratum:</td>
                <td><?php echo $statsData['active_stratum']; ?></td>
            </tr>
            </tbody>
         </table>
        </div>

        <div class="tab-pane table-responsive" id="account">
         <h2>My account</h2>
         <table class="table table-striped table-bordered">
            <tbody>
            <tr>
                <td>username:</td>
                <td><?php echo $profileData['username']; ?></td>
            </tr>
            <tr>
                <td>wallet:</td>
                <td><?php echo $profileData['wallet']; ?></td>
            </tr>
            <tr>
                <td>send_threshold:</td>
                <td><?php echo $profileData['send_threshold']; ?></td>
            </tr>
            <tr>
                <td>estimated_reward:</td>
                <td><?php echo $profileData['estimated_reward']; ?></td>
            </tr>
            <tr>
                <td>unconfirmed_reward:</td>
                <td><?php echo $profileData['unconfirmed_reward']; ?></td>
            </tr>
            <tr>
                <td>confirmed_reward:</td>
                <td><?php echo $profileData['confirmed_reward']; ?></td>
            </tr>
            <tr>
                <td>hashrate:</td>
                <td><?php echo $profileData['hashrate']; ?></td>
            </tr>
            <tr>
                <td>rating:</td>
                <td><?php echo $profileData['rating']; ?></td>
            </tr>
            <tr>
                <td>nmc_send_threshold:</td>
                <td><?php echo $profileData['nmc_send_threshold']; ?></td>
            </tr>
            <tr>
                <td>unconfirmed_nmc_reward:</td>
                <td><?php echo $profileData['unconfirmed_nmc_reward']; ?></td>
            </tr>
            <tr>
                <td>confirmed_nmc_reward:</td>
                <td><?php echo $profileData['confirmed_nmc_reward']; ?></td>
            </tr>
            </tbody>
         </table>

         <h2>Workers</h2>
         <table class="table table-striped table-bordered">
            <thead> 
              <tr>
                <th>worker_name</th>
                <th>shares</th>
                <th>score</th>
                <th>last_share</th>
                <th>hashrate</th>
                <th>alive</th>
              </tr>
            </thead>
            <tbody>
        <?php foreach ($profileData['workers'] as $workerName => $workerData): ?>
            <tr>
            <td><?php echo $workerName; ?></td>
            <td><?php echo $workerData['shares']; ?></td>
            <td><?php echo $workerData['score']; ?></td>
            <td><?php echo $workerData['last_share']; ?></td>
            <td><?php echo $workerData['hashrate']; ?></td>
            <td><?php echo $workerData['alive']; ?></td>
            </tr>
        <?php endforeach; ?>
            </tbody>
         </table>
        </div>

        <div class="tab-pane table-responsive" id="donation">
         <h2>Donation</h2>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
        <script type="text/javascript" src="https://blockchain.info//Resources/wallet/pay-now-button.js"></script>
        <div class="blockchain-btn"
             data-address="1A4cXfnCi11bZNLqopp91sZFiEQqp9Xg5N"
             data-shared="false">
            <div class="blockchain stage-begin">
                <img src="https://blockchain.info//Resources/buttons/donate_64.png"/>
            </div>
            <div class="blockchain stage-loading">
                <img src="https://blockchain.info//Resources/loading-large.gif"/>
            </div>
            <div class="blockchain stage-ready">
                 <p>Please Donate To Bitcoin Address: <b>[[address]]</b></p>
                 <p class="qr-code"></p>
            </div>
            <div class="blockchain stage-paid">
                 Donation of <b>[[value]] BTC</b> Received. Thank You.
            </div>
            <div class="blockchain stage-error">
                <font color="red">[[error]]</font>
            </div>
        </div>
        </div>
    </div>
</div>
<footer class="navbar navbar-default navbar-fixed-bottom" role="navigation">
    <div class="container">
        <p class="pull-right"><a href="#top">Back to top</a></p>
        <p>© 2014 Good Company · About & Contact: <a href="http://www.goodcompany-jp.com/">About Us</a></p>
    </div>
</footer>
  </body>
</html>