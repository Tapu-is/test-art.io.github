<?php
// Set content type
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $size = filter_input(INPUT_POST, 'size', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
    $details = filter_input(INPUT_POST, 'details', FILTER_SANITIZE_STRING);
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($size) || empty($type)) {
        echo json_encode(['error' => 'All required fields must be filled']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => 'Invalid email format']);
        exit;
    }
    
    // Artist email (REPLACE WITH YOUR EMAIL)
    $to = "your_email@example.com";
    $subject = "New Commission Request: $name";
    
    // Build email message
    $message = "
    <html>
    <head>
        <title>New Commission Request</title>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; }
            h1 { color: #c19d5e; }
            .detail { margin-bottom: 15px; }
            .label { font-weight: bold; color: #c19d5e; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>New Commission Request</h1>
            
            <div class='detail'>
                <span class='label'>Client:</span> $name
            </div>
            <div class='detail'>
                <span class='label'>Email:</span> $email
            </div>
            <div class='detail'>
                <span class='label'>Phone:</span> " . ($phone ?: 'Not provided') . "
            </div>
            <div class='detail'>
                <span class='label'>Size:</span> $size
            </div>
            <div class='detail'>
                <span class='label'>Art Type:</span> $type
            </div>
            <div class='detail'>
                <span class='label'>Details:</span><br>
                " . nl2br($details) . "
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Email headers
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Send email
    if (mail($to, $subject, $message, $headers)) {
        // Send confirmation to client
        $client_subject = "Commission Request Received";
        $client_message = "Thank you for your commission request! We've received your details and will contact you soon.";
        mail($email, $client_subject, $client_message);
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to send email. Please try again later.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>

