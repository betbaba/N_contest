<?php
$apiKey = "xcmVHov9ubqFIpCumize1Y6zXHdPVom8cVMklyiFZjsNXqwrmg7NLOrp";

session_start();

if (!isset($_SESSION['conversation'])) {
    $_SESSION['conversation'] = [];
}

$videos = [];

if (isset($_POST['query'])) {
    $userQuestion = htmlspecialchars($_POST['query']);
    $_SESSION['conversation'][] = ['user' => $userQuestion];

    $searchQuery = urlencode($_POST['query']);
    $response = file_get_contents("https://api.pexels.com/videos/search?query=$searchQuery&per_page=3&page=1", false, stream_context_create([
        "http" => [
            "header" => "Authorization: $apiKey"
        ]
    ]));

    $responseData = json_decode($response, true);
    $videos = $responseData['videos'] ?? [];

    $_SESSION['conversation'][] = ['videos' => $videos];
}

if (isset($_POST['new_conversation'])) {
    $_SESSION['conversation'] = [];
    $videos = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASHEWA AI</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0d1117;
            color: #c9d1d9;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 20%;
            background-color: #0d1117;
            padding: 20px;
            border-right: 2px solid #4dffb3;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex-grow: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            height: 100vh;
        }

        .header {
            margin-bottom: 20px;
            color: #4dffb3;
            font-size: 32px;
            text-align: center;
        }

        .chat-window {
            width: 100%;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
            overflow-y: auto;
            max-height: 80%;
            padding-right: 10px;
        }

        .user-message, .ai-response {
            padding: 10px;
            border-radius: 10px;
            max-width: 80%;
        }

        .user-message {
            align-self: flex-end;
            background-color: #4dffb3;
            color: #0d1117;
            text-align: right;
        }

        .ai-response {
            align-self: flex-start;
            background-color: #161b22;
            color: #151515;
            text-align: left;
        }

        .video-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 10px;
        }

        .video-card {
            background-color: #0d1117;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #30363d;
        }

        video {
            width: 100%;
            border-radius: 10px;
        }

        .video-info {
            margin-top: 10px;
            text-align: center;
            color: #4dffb3;
        }

        .chat-input {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            border-top: 1px solid #30363d;
            padding-top: 10px;
        }

        #chatInput {
            width: 100%;
            padding: 12px;
            border: 1px solid #30363d;
            border-radius: 10px;
            background-color: #0d1117;
            color: #c9d1d9;
            outline: none;
            margin-right: 10px;
        }

        #sendBtn {
            padding: 12px 25px;
            border: none;
            background-color: #4dffb3;
            color: #0d1117;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        #sendBtn:hover {
            background-color: #33ffcc;
        }
        h3 {
            color: #4dffb3;
        }
        .sidebar-links {
            margin-top: 20px;
        }

        .sidebar-link {
            display: flex;
            flex-direction: column;
            align-items: left;
            margin-bottom: 20px;
            text-decoration: none;
            color: #4dffb3;
        }

        .sidebar-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .sidebar-link p {
            font-size: 16px;
            color: #4dffb3;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="new-conversation">
            <h3>New Conversation</h3>
            <ul>
                <li style="color: #4dffb3; cursor: pointer;" onclick="document.getElementById('newConversationForm').submit();">Start New Conversation</li>
            </ul>
            <div class="sidebar-links">
        <a href="ai.php" class="sidebar-link">Content Generator</a>

        <a href="image.php" class="sidebar-link">Image Generator </a>
        
        <a href="feedback.html" class="sidebar-link">Feedback and Support </a>
        </div>
            <form id="newConversationForm" method="POST" style="display:none;">
                <input type="hidden" name="new_conversation" value="1">
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="header">NEHABI AI FOR VIDEO</div>
        <div class="chat-window">
            <?php if (!empty($_SESSION['conversation'])): ?>
                <?php foreach ($_SESSION['conversation'] as $entry): ?>
                    <?php if (isset($entry['user'])): ?>
                        <div class="user-message">
                            <div><?php echo $entry['user']; ?></div>
                        </div>
                    <?php elseif (isset($entry['videos'])): ?>
                        <div class="ai-response">
                            <div>
                                <?php if (!empty($entry['videos'])): ?>
                                    <div class="video-container">
                                        <?php foreach ($entry['videos'] as $video): ?>
                                            <div class="video-card">
                                                <video controls>
                                                    <source src="<?php echo htmlspecialchars($video['video_files'][0]['link']); ?>" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="video-info">No videos found.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="chat-input">
            <form method="POST" style="width:100%; display: flex;">
                <input type="text" id="chatInput" name="query" placeholder="Type your question here..." required>
                <button type="submit" id="sendBtn">Send</button>
            </form>
        </div>
    </div>
</body>
</html>
