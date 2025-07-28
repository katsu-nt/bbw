<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" />

    <title>Đang xử lý</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #000;
            color: #fff;
            font-family: 'Arial', sans-serif;
            margin: 0;
            text-align: center;
            flex-direction: column;
        }

        .error-container {
            padding: 40px;
            border-radius: 12px;
            animation: fadeIn 1s ease-in-out, floatUp 3s ease-in-out infinite;
        }

        .error-text {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            animation: glow 1.5s infinite alternate;
        }

        .error-gif {
            width: 180px;
            margin-bottom: 20px;
            animation: fadeIn 1s ease-in-out, shake 2s infinite;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes glow {
            0% {
                text-shadow: 0 0 10px rgba(255, 255, 255, 0.6);
            }

            100% {
                text-shadow: 0 0 20px rgba(255, 255, 255, 0.9);
            }
        }

        @keyframes floatUp {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }

            100% {
                transform: translateY(0);
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: rotate(0);
            }

            25% {
                transform: rotate(2deg);
            }

            75% {
                transform: rotate(-2deg);
            }
        }

        .logo {
            width: 30%;
            margin-bottom: 2rem;
        }

        .progress-bar-container {
            width: 20rem;
            /* equivalent to Tailwind's w-48 */
            background-color: #e5e7eb;
            /* Tailwind's bg-gray-200 */
            border-radius: 9999px;
            height: 0.625rem;
            /* Tailwind's h-2.5 */
            position: relative;
            margin-top: 2rem;
        }

        @media (prefers-color-scheme: dark) {
            .progress-bar-container {
                background-color: #374151;
                /* Tailwind's dark:bg-gray-700 */
            }
        }

        .progress-bar-fill {
            /* background-color: #B99659; */
            background-color: white;
            height: 100%;
            border-radius: 9999px;
            animation: progress 5s ease-in-out infinite;
            width: 30%;
            /* Initial width */
        }

        @keyframes progress {
            0% {
                width: 0%;
            }

            100% {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- LOGO -->
    <div class="logo" style="width: auto;">
        <a href="/">
            <img src="{{ asset('images/logo-bbw.svg') }}" alt="logo" style="width: 100%;" />
        </a>
    </div>
    <img src="https://media2.giphy.com/media/v1.Y2lkPTc5MGI3NjExd213ZWlndDJuaHltajZhbnB2MDJwZjE1d3QwZXM2MXlqcnBoM3FxbSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/VX7yEoXAFf8as/giphy.gif" alt="Error Animation" class="error-gif">
    <div class="progress-bar-container">
        <div class="progress-bar-fill"></div>
    </div>

    <div class="error-container">
        <p class="error-text">Máy chủ đang xử lý. Vui lòng đợi trong giây lát!</p>
    </div>
</body>

<script>
    setTimeout(function() {
        window.location.href = "/";
    }, 5000);
</script>

</html>