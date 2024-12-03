<?php
// Handle Pexels API interaction when the user submits a query via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Your Pexels API Key
    $apiKey = 'xcmVHov9ubqFIpCumize1Y6zXHdPVom8cVMklyiFZjsNXqwrmg7NLOrp';  // Your Pexels API key

    // Retrieve the user's message
    $userMessage = $_POST['message'];

    // Initialize the bot's response
    $botResponse = '';

    // Normalize the user input
    $searchQuery = trim($userMessage); // Accepting any query as a search

    if (!empty($searchQuery)) {
        // Call Pexels API to get images
        $apiUrl = 'https://api.pexels.com/v1/search';
        $params = http_build_query([
            'query' => $searchQuery,
            'per_page' => 6  // Limit to 6 images for two rows
        ]);

        // Initialize cURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl . '?' . $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $apiKey
        ]);

        // Execute the API request
        $response = curl_exec($ch);
        curl_close($ch);

        // Decode the API response
        $data = json_decode($response, true);

        // Check if photos are available
        if (!empty($data['photos'])) {
            $botResponse = 'Here are some images of ' . htmlspecialchars($searchQuery) . ':<br>';
            foreach ($data['photos'] as $photo) {
                // Adjusted to use 'large' images
                $botResponse .= '<div style="margin: 10px; display: inline-block; position: relative;">';
                $botResponse .= '<a href="' . $photo['src']['original'] . '" onclick="openModal(\'' . $photo['src']['original'] . '\'); return false;">';
                $botResponse .= '<img src="' . $photo['src']['large'] . '" alt="' . htmlspecialchars($searchQuery) . '" style="width: 290px; height: auto; border-radius: 10px;">'; // Set width for 2 images per row
                $botResponse .= '</a>';
                $botResponse .= '</div>';
            }
        } else {
            $botResponse = 'Sorry, I couldn’t find any images for "' . htmlspecialchars($searchQuery) . '".';
        }
    } else {
        $botResponse = 'Please provide a more specific search query, e.g., "image of cats".';
    }

    // Return the bot's response as JSON
    echo json_encode(['response' => $botResponse]);
    exit;  // Stop further execution since we just need to return JSON
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
            border-right: 2px solid #4dffb3; /* Aesthetic border */
        }

        .sidebar h3 {
            margin-bottom: 20px;
            color: #4dffb3; /* Updated color */
        }

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
            color: #4dffb3; /* Updated color */
            text-align: center; /* Center align title */
        }

        .chat-box {
            width: 100%;
            height: 80%; /* Increased height for larger scrollable area */
            overflow-y: auto; /* Keeps the scroll functionality */
            background-color: #191c22;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px; /* Gap between messages */
        }

        .message {
            padding: 10px;
            border-radius: 5px;
            max-width: 70%;
            word-wrap: break-word; /* Allow long words to break */
        }

        .user-message {
            background-color: #4dffb3; /* Updated user message color */
            color: #0d1117;
            align-self: flex-end; /* Align user messages to the right */
        }

        .ai-message {
            background-color: #0d1117; /* AI message color */
            color: white;
            align-self: flex-start; /* Align AI messages to the left */
        }

        .chat-input-container {
            display: flex;
            justify-content: space-between; /* Space between input and button */
        }

        #chatInput {
            width: 100%; /* Set to 100% width */
            padding: 10px;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #0d1117;
            color: #fff;
        }

        #sendBtn {
            padding: 10px 20px;
            border: none;
            background-color: #4dffb3; /* Updated button color */
            color: #000;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s; /* Hover effect */
        }

        #sendBtn:hover {
            background-color: #40e6a4; /* Change color on hover */
        }

        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0, 0, 0); /* Fallback color */
            background-color: rgba(0, 0, 0, 0.9); /* Black w/ opacity */
            justify-content: center; /* Center modal */
            align-items: center; /* Center modal */
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
            color: #4dffb3; /* Updated close button hover color */
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
            <li style="color: #4dffb3; cursor: pointer;" onclick="newConversation()">Start New Conversation</li>
        </ul>
        <div class="sidebar-links">
        <a href="AI.php" class="sidebar-link">Text Generator
        </a>

        <a href="video.php" class="sidebar-link"><p>Video Generator</p>
        </a>
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

    <!-- Modal for full-screen image display -->
    <div id="myModal" class="modal" onclick="closeModal()">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <script>
        function sendMessage() {
            const userMessage = document.getElementById('chatInput').value;
            if (userMessage.trim()) {
                displayMessage(userMessage, 'user-message'); // Display user message
                document.getElementById('chatInput').value = ''; // Clear input field

                // Send the message to the server
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({ message: userMessage }),
                })
                .then(response => response.json())
                .then(data => {
                    displayMessage(data.response, 'ai-message'); // Display AI response
                });
            }
        }

        function displayMessage(message, type) {
            const chatBox = document.getElementById('chat-box');
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', type);
            messageDiv.innerHTML = message;
            chatBox.appendChild(messageDiv);
            chatBox.scrollTop = chatBox.scrollHeight; // Scroll to the bottom
        }

        function openModal(imageSrc) {
            const modal = document.getElementById("myModal");
            const modalImage = document.getElementById("modalImage");
            modal.style.display = "flex"; // Change to flex for center alignment
            modalImage.src = imageSrc;
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        function newConversation() {
            document.getElementById('chat-box').innerHTML = ''; // Clear chat
        }
    </script>
</body>
</html>
