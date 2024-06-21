<?php

// Replace with your server key from Firebase console
define('SERVER_KEY', 'YOUR_SERVER_KEY');

// Replace with your FCM token (device token) to send the notification to a specific device
$deviceToken = 'YOUR_DEVICE_TOKEN';

// Notification content
$message = [
    'title' => 'Notification Title',
    'body' => 'This is the body of the notification.',
];

// Data payload (optional)
$dataPayload = [
    'key' => 'value',
    'key2' => 'value2',
];

// Send notification function
function sendNotification($deviceToken, $message, $dataPayload = []) {
    $url = 'https://fcm.googleapis.com/fcm/send';
    $headers = [
        'Authorization: key=' . SERVER_KEY,
        'Content-Type: application/json',
    ];

    $notification = [
        'to' => $deviceToken,
        'notification' => $message,
        'data' => $dataPayload,
    ];

    $payload = json_encode($notification);

    // Initialize curl handle
    $ch = curl_init();
    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    // Execute post
    $result = curl_exec($ch);
    if ($result === false) {
        die('Curl failed: ' . curl_error($ch));
    }

    // Close connection
    curl_close($ch);

    // Debugging output of the response
    echo $result;
}

// Call the function to send notification
sendNotification($deviceToken, $message, $dataPayload);

?>
