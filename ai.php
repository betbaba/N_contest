<?php
require 'vendor/autoload.php'; // Ensure you have installed the GroqPHP library via Composer

use LucianoTonet\GroqPHP\Groq;

$apiKey = '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'; // Your API key
$response = '';
$errorMessage = '';
$messages = [];

// Start the session to store messages across requests
session_start();

// Clear the messages if 'new_conversation' is set
if (isset($_POST['new_conversation'])) {
    unset($_SESSION['messages']); // Clear messages from the session
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to clear the form submission
    exit();
}

// Load existing messages from the session if available
if (isset($_SESSION['messages'])) {
    $messages = $_SESSION['messages'];
}

// Handle form submission for user question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question'])) {
    $userQuestion = htmlspecialchars($_POST['question']); // Sanitize user input
    $messages[] = ['role' => 'user', 'content' => $userQuestion]; // Store user question

    $groq = new Groq($apiKey);

    try {
        // Make the API request
        $response = $groq->chat()->completions()->create([
            'model' => 'llama3-8b-8192',
            'messages' => [
                ['role' => 'user', 'content' => $userQuestion],
            ],
        ]);
        
        // Extract the AI's response
        $aiResponse = $response['choices'][0]['message']['content'];
        $messages[] = ['role' => 'ai', 'content' => $aiResponse]; // Store AI response
    } catch (Exception $e) {
        $errorMessage = "Error: " . htmlspecialchars($e->getMessage()); // Sanitize error message
        $messages[] = ['role' => 'ai', 'content' => $errorMessage]; // Store error message
    }

    // Save messages back to the session
    $_SESSION['messages'] = $messages;

    // Redirect to the same page to avoid form resubmission
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

        /* Sidebar styling */
        .sidebar {
            width: 25%;
            background-color: #0d1117;
            padding: 20px;
            display: flex;
            flex-direction: column;
            border-right: 2px solid #4dffb3; /* Updated border color */
        }

        .sidebar h3 {
            margin-bottom: 20px;
            color: #4dffb3; /* Updated sidebar heading color */
        }

        /* Main content styling */
        .main-content {
            flex-grow: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Added for better spacing */
        }

        .title h1 {
            font-size: 48px;
            margin-bottom: 30px;
            letter-spacing: 5px;
            color: #4dffb3; /* Updated title color */
            text-align: center; /* Center align title */
        }

        .chat-box {
            width: 100%; /* Make the chat box full width */
            height: 70%; /* Set height for chat box */
            overflow-y: auto;
            background-color:#191c22;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px; /* Add gap between messages */
        }

        .message {
            padding: 10px;
            border-radius: 5px;
            max-width: 70%;
            word-wrap: break-word; /* Allow long words to break */
        }

        .user-message {
            background-color: #4dffb3; /* Updated background for user messages */
            color: black;
            align-self: flex-end; /* Align user messages to the right */
        }

        .ai-message {
            background-color: #0d1117; /* Keep the AI message background color */
            color: white;
            align-self: flex-start; /* Align AI messages to the left */
        }

        .chat-input-container {
            display: flex;
            justify-content: space-between; /* Added for spacing between input and button */
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
            background-color: #4dffb3; /* Updated button background color */
            color: #000;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s; /* Add transition for hover effect */
        }

        #sendBtn:hover {
            background-color: #33cc99; /* Change color on hover */
        }
        /* Styling the sidebar links */
.sidebar-links {
    margin-top: 20px;
}

.sidebar-link {
    display: flex;
    flex-direction: column;
    align-items: left;
    margin-bottom: 20px;
    text-decoration: none; /* Remove underline from link */
    color: #4dffb3; /* Link color */
}

.sidebar-image {
    width: 100%;
    height: auto;
    border-radius: 8px;
    margin-bottom: 10px; /* Space between image and text */
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
        <a href="image.php" class="sidebar-link">Image Generator
        </a>

        <a href="video.php" class="sidebar-link"><p>Video Generator</p>
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
        // Auto-scroll to the bottom of the chat box
        const chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    </script>
</body>
</html>
