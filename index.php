<?php

// Function to upload the file to Telegram
function uploadToTelegram($fileUrl, $fileType, $telegramBotToken, $chatId) {
    // Telegram Bot API request to send file
    $url = "https://api.telegram.org/bot$telegramBotToken/send$fileType";

    $postData = array(
        'chat_id' => $chatId,
        $fileType => $fileUrl
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

// Main code
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['drive_link'])) {
    $driveLink = $_POST['drive_link']; // Get Google Drive link from form

    // Extract file ID from the Google Drive link
    $parts = explode("/", parse_url($driveLink, PHP_URL_PATH));
    $fileId = end($parts);

    // Get file information from Google Drive API
    $fileInfoUrl = "https://www.googleapis.com/drive/v3/files/$fileId?fields=webContentLink,mimeType";
    $fileInfo = json_decode(file_get_contents($fileInfoUrl), true);
    $mimeType = $fileInfo['mimeType'];
    $fileUrl = $fileInfo['webContentLink'];

    // Determine file type
    switch ($mimeType) {
        case 'application/pdf':
            $fileType = 'document';
            break;
        case 'video/mp4':
            $fileType = 'video';
            break;
        case 'audio/mpeg':
            $fileType = 'audio';
            break;
        default:
            $fileType = 'unknown';
            break;
    }

    // Provide your Telegram Bot token and chat ID
    $telegramBotToken = "YOUR_TELEGRAM_BOT_TOKEN";
    $chatId = "YOUR_TELEGRAM_GROUP_CHAT_ID";

    // Upload the file to Telegram
    $telegramResponse = uploadToTelegram($fileUrl, ucfirst($fileType), $telegramBotToken, $chatId);

    // Handle response as needed
    echo $telegramResponse;
}

?>

<!-- HTML Form to submit Google Drive link -->
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="text" name="drive_link" placeholder="Enter Google Drive link">
    <button type="submit">Upload to Telegram</button>
</form>
