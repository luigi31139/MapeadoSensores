<?php

if (!isset($usernumbers) || empty($usernumbers)) {
    die("No recipients found.");
}


$account_sid = "AC55fc1b52f7f1679b8c50b71ef37e34e5";
$auth_token = "559cac9092d533fdf15917e4d31e1b29";
$twilio_number = "+15704389991"; 


$message = "Alert: A sensor measurement exceeded the threshold.";


$url = "https://api.twilio.com/2010-04-01/Accounts/$account_sid/Messages.json";


function sendSMS($to, $from, $message, $url, $account_sid, $auth_token) {
    $data = array(
        'From' => $from,
        'To' => $to,
        'Body' => $message
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$account_sid:$auth_token");

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "Error sending to $to: " . curl_error($ch) . "\n";
    } else {
        echo "Message sent to $to. Response: $response\n";
    }

    curl_close($ch);
}


foreach ($usernumbers as $recipient) {
    sendSMS($recipient, $twilio_number, $message, $url, $account_sid, $auth_token);
}
?>
