<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apiKey = 'xcmVHov9ubqFIpCumize1Y6zXHdPVom8cVMklyiFZjsNXqwrmg7NLOrp';
    $userMessage = $_POST['message'];

    if (!isset($_SESSION['conversation'])) {
        $_SESSION['conversation'] = [];
    }

    $botResponse = '';
    $searchQuery = trim($userMessage);

    if (!empty($searchQuery)) {
        $apiUrl = 'https://api.pexels.com/v1/search';
        $params = http_build_query([
            'query' => $searchQuery,
            'per_page' => 6
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl . '?' . $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $apiKey
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if (!empty($data['photos'])) {
            $botResponse = 'Here are some images of ' . htmlspecialchars($searchQuery) . ':<br>';
            foreach ($data['photos'] as $photo) {
                $botResponse .= '<div style="margin: 10px; display: inline-block; position: relative;">';
                $botResponse .= '<a href="' . $photo['src']['original'] . '" onclick="openModal(\'' . $photo['src']['original'] . '\'); return false;">';
                $botResponse .= '<img src="' . $photo['src']['large'] . '" alt="' . htmlspecialchars($searchQuery) . '" style="width: 290px; height: auto; border-radius: 10px;">';
                $botResponse .= '</a>';
                $botResponse .= '</div>';
            }
        } else {
            $botResponse = 'Sorry, I couldn’t find any images for "' . htmlspecialchars($searchQuery) . '".';
        }
    } else {
        $botResponse = 'Please provide a more specific search query, e.g., "image of cats".';
    }

    echo json_encode(['response' => $botResponse]);
    exit;
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
            height: 80%;
            overflow-y: auto;
            background-color: #191c22;
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
            color: #0d1117;
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
            width: 100%;
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
            background-color: #40e6a4;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.9);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 90%;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 25px;
            color: white;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #4dffb3;
            text-decoration: none;
            cursor: pointer;
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
        </form>
        <div class="sidebar-links">
        <a href="ai.php" class="sidebar-link">Content Generator</a>
        <a href="video.php" class="sidebar-link">Video Generator</a>
        <a href="https://feedback-support.onrender.com/" class="sidebar-link">Feedback and Support</a>
    </div>
    </div>

    <div class="main-content">
        <div class="title">
            <h1>NEHABI AI FOR IMAGE</h1>
        </div>

        <div class="chat-box" id="chat-box"></div>

        <div class="chat-input-container">
            <input type="text" id="chatInput" placeholder="Ask me, Anything" required>
            <button id="sendBtn" type="button" onclick="sendMessage()">Send</button>
        </div>
    </div>

    <div id="myModal" class="modal" onclick="closeModal()">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <script>
        function sendMessage() {
            const userMessage = document.getElementById('chatInput').value;
            if (userMessage.trim()) {
                displayMessage(userMessage, 'user-message');
                document.getElementById('chatInput').value = '';

                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({ message: userMessage }),
                })
                .then(response => response.json())
                .then(data => {
                    displayMessage(data.response, 'ai-message');
                });
            }
        }

        function displayMessage(message, type) {
            const chatBox = document.getElementById('chat-box');
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', type);
            messageDiv.innerHTML = message;
            chatBox.appendChild(messageDiv);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function openModal(imageSrc) {
            const modal = document.getElementById("myModal");
            const modalImage = document.getElementById("modalImage");
            modal.style.display = "flex";
            modalImage.src = imageSrc;
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }
    </script>
</body>
</html>
