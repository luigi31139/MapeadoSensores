<?php

if (!isset($usernumbers) || empty($usernumbers)) {
    die("No recipients found.");
}


$account_sid = "";
$auth_token = "";
$twilio_number = ""; 


$message = "PELIGRO!!!! Posible Inundacion Detectada en Tu Area. Por favor, toma precauciones y mantente seguro.";


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
