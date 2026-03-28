<?php

$errorCode = http_response_code();
if ($errorCode == 200) {
    $errorCode = isset($_GET['code']) ? (int) $_GET['code'] : 404;
}

http_response_code($errorCode);

$messages = [
    403 => [
        'title' => 'Forbidden',
        'message' => "Sorry, this page isn't available directly."
    ],
    404 => [
        'title' => 'Page Not Found',
        'message' => "The page you're looking for does not exist."
    ],
    500 => [
        'title' => 'Server Error',
        'message' => "Something went wrong on our end."
    ],
];

$title = $messages[$errorCode]['title'] ?? 'Error';
$message = $messages[$errorCode]['message'] ?? 'An unexpected error has occurred.';

function generateErrorCodeWithEmoji($errorCode)
{
    $errorCodeStr = (string) $errorCode;
    $emoji = '<div class="emoji-face"><div class="emoji-mouth"></div></div>';

    return str_replace('0', $emoji, $errorCodeStr);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($errorCode) ?> <?= htmlspecialchars($title) ?></title>
    <style>
        :root {
            --bg-color: #ffffff;
            --text-color: #333;
            --accent-color: #ffc107;
            --button-bg: #333;
            --button-text: #fff;
        }

        body {
            margin: 0;
            padding: 0;
            background: var(--bg-color);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        .clouds {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--accent-color);
            height: 25vh;
            border-top-left-radius: 50% 40%;
            border-top-right-radius: 50% 40%;
            z-index: 1;
        }

        .error-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 2rem;
            animation: fadeIn 1.2s ease-in-out;
        }

        @keyframes fadeIn {
            0% {
                transform: translateY(30px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .error-code {
            font-size: 6rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1.2rem;
            color: var(--accent-color);
            font-weight: bold;
        }

        .emoji-face {
            width: 6rem;
            height: 6rem;
            background-color: var(--accent-color);
            border-radius: 50%;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .emoji-face::before,
        .emoji-face::after {
            content: '';
            position: absolute;
            background-color: var(--text-color);
            border-radius: 50%;
            width: 10px;
            height: 10px;
        }

        .emoji-face::before {
            top: 30%;
            left: 30%;
        }

        .emoji-face::after {
            top: 30%;
            right: 30%;
        }

        .emoji-mouth {
            position: absolute;
            bottom: 25%;
            left: 50%;
            width: 50%;
            height: 20%;
            border-bottom: 4px solid var(--text-color);
            border-radius: 0 0 50% 50%;
            transform: translateX(-50%) rotate(180deg);
        }

        .error-title {
            font-size: 2rem;
            font-weight: bold;
            margin-top: 1rem;
            text-transform: uppercase;
            color: var(--text-color);
        }

        .error-message {
            font-size: 1rem;
            color: #444;
            margin: 1rem 0;
        }

        .back-btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.8rem 1.8rem;
            background-color: var(--button-bg);
            color: var(--button-text);
            border: none;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .footer {
            margin-top: 1rem;
            font-size: 0.85rem;
            color: #ffffff;
            position: relative;
            z-index: 2;
        }

        @media (max-width: 768px) {
            .footer {
                font-size: 0.75rem;
                margin-top: 1.2rem;
            }

            .error-code {
                font-size: 20vw;
            }

            .emoji-face {
                width: 14vw;
                height: 14vw;
            }

            .emoji-mouth {
                border-bottom-width: 2px;
            }

            .error-message {
                font-size: 4vw;
            }
        }
    </style>
</head>

<body>
    <div class="error-content">
        <div class="error-code">
            <?= generateErrorCodeWithEmoji($errorCode) ?>
        </div>
        <div class="error-title"><?= htmlspecialchars($title) ?></div>
        <p class="error-message"><?= htmlspecialchars($message) ?></p>

        <a href="javascript:history.back()" class="back-btn">Go Back</a>
    </div>

    <div class="clouds"></div>

    <div class="footer">© <?= date('Y') ?> Serbisyos</div>
</body>

</html>
