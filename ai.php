<?php
require 'vendor/autoload.php';

use LucianoTonet\GroqPHP\Groq;

$apiKey = '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';
$response = '';
$errorMessage = '';
$messages = [];

session_start();

if (isset($_POST['new_conversation'])) {
    unset($_SESSION['messages']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_SESSION['messages'])) {
    $messages = $_SESSION['messages'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question'])) {
    $userQuestion = htmlspecialchars($_POST['question']);
    $messages[] = ['role' => 'user', 'content' => $userQuestion];

    $groq = new Groq($apiKey);

    try {
        $response = $groq->chat()->completions()->create([
            'model' => 'llama3-8b-8192',
            'messages' => [
                ['role' => 'user', 'content' => $userQuestion],
            ],
        ]);
        
        $aiResponse = $response['choices'][0]['message']['content'];
        $messages[] = ['role' => 'ai', 'content' => $aiResponse];
    } catch (Exception $e) {
        $errorMessage = "Error: " . htmlspecialchars($e->getMessage());
        $messages[] = ['role' => 'ai', 'content' => $errorMessage];
    }

    $_SESSION['messages'] = $messages;

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
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
            display: flex;
            font-family: 'Arial', sans-serif;
            background-color: #0d1117;
            color: #c9d1d9;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 25%;
            background-color: #0d1117;
            padding: 20px;
            display: flex;
            flex-direction: column;
            border-right: 2px solid #4dffb3;
        }

        .sidebar h3 {
            margin-bottom: 20px;
            color: #4dffb3;
        }

        .main-content {
            flex-grow: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .title h1 {
            font-size: 48px;
            margin-bottom: 30px;
            letter-spacing: 5px;
            color: #4dffb3;
            text-align: center;
        }

        .chat-box {
            width: 100%;
            height: 70%;
            overflow-y: auto;
            background-color:#191c22;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message {
            padding: 10px;
            border-radius: 5px;
            max-width: 70%;
            word-wrap: break-word;
        }

        .user-message {
            background-color: #4dffb3;
            color: black;
            align-self: flex-end;
        }

        .ai-message {
            background-color: #0d1117;
            color: white;
            align-self: flex-start;
        }

        .chat-input-container {
            display: flex;
            justify-content: space-between;
        }

        #chatInput {
            width: 80%;
            padding: 10px;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #0d1117;
            color: #fff;
        }

        #sendBtn {
            padding: 10px 20px;
            border: none;
            background-color: #4dffb3;
            color: #000;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #sendBtn:hover {
            background-color: #33cc99;
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
        <h3>New Conversation</h3>
        <ul>
            <li style="color: #4dffb3; cursor: pointer;" onclick="document.getElementById('new-conversation-form').submit();">Start New Conversation</li>
        <form id="new-conversation-form" method="POST" action="" style="display: none;">
            <input type="hidden" name="new_conversation" value="1">
        </form>
        <div class="sidebar-links">
        <a href="image.php" class="sidebar-link">Image Generator</a>
        <a href="video.php" class="sidebar-link">Video Generator</a>
        <a href="https://feedback-support.onrender.com/" class="sidebar-link">Feedback and Support</a>
        </a>

        
    </div>

        
    </div>

    <div class="main-content">
        <div class="title">
            <h1>NEHABI AI FOR CONTENT</h1>
        </div>

        <div class="chat-box" id="chat-box">
            <?php foreach ($messages as $message): ?>
                <div class="message <?= $message['role'] === 'user' ? 'user-message' : 'ai-message' ?>">
                    <?= nl2br(htmlspecialchars($message['content'])) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="chat-input-container">
            <form method="POST" action="" style="width: 100%;">
                <input type="text" id="chatInput" name="question" placeholder="Ask me, Anything" required>
                <button id="sendBtn" type="submit">Send</button>
            </form>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    </script>
</body>
</html>
