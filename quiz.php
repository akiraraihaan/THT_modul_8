<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'test';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if not exists
$createTable = "CREATE TABLE IF NOT EXISTS quiz_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    nim VARCHAR(50) NOT NULL,
    answers TEXT NOT NULL,
    total_score INT NOT NULL,
    correct_answers INT NOT NULL,
    submission_date DATETIME NOT NULL
)";

if (!$conn->query($createTable)) {
    die("Error creating table: " . $conn->error);
}

// Store user info in session when first loaded
if (isset($_POST['nama']) && isset($_POST['nim'])) {
    $_SESSION['nama'] = $_POST['nama'];
    $_SESSION['nim'] = $_POST['nim'];
}

// Handle quiz submission
if (isset($_POST['submit_quiz'])) {
    $nama = $_SESSION['nama'];
    $nim = $_SESSION['nim'];
    $answers = json_decode($_POST['answers'], true);
    $total_score = $_POST['total_score'];
    $correct_answers = $_POST['correct_answers'];
    
    // Save to database
    $stmt = $conn->prepare("INSERT INTO quiz_results (nama, nim, answers, total_score, correct_answers, submission_date) VALUES (?, ?, ?, ?, ?, NOW())");
    $answers_json = json_encode($answers);
    $stmt->bind_param("sssii", $nama, $nim, $answers_json, $total_score, $correct_answers);
    
    if ($stmt->execute()) {
        $_SESSION['quiz_completed'] = true;
        header("Location: results.php");
        exit();
    }
}

// Redirect if no session
if (!isset($_SESSION['nama']) || !isset($_SESSION['nim'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$isError = false;

if(isset($_POST['nama']) && $_POST['nama'] !== '') {
    if(isset($_POST['nim']) && $_POST['nim'] !== '') {
        $error = '';
        $isError = false;
    } else {
        $error = 'NIM belum diisi';
        $isError = true;
    }
} else {
    $error = 'Nama belum diisi';
    $isError = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes countdown {
            from { stroke-dashoffset: 0; }
            to { stroke-dashoffset: 251.2; }
        }
        .timer-circle {
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }
        .timer-circle circle {
            stroke-dasharray: 251.2;
            stroke-linecap: round;
            transition: stroke-dashoffset 1s linear;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <!-- Header with Student Info -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-gray-600">Name: </span>
                        <span class="font-semibold"><?= htmlspecialchars($_SESSION['nama']) ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">NIM: </span>
                        <span class="font-semibold"><?= htmlspecialchars($_SESSION['nim']) ?></span>
                    </div>
                </div>
            </div>

            <form id="quizForm" method="POST" action="quiz.php">
                <input type="hidden" name="answers" id="answersInput">
                <input type="hidden" name="total_score" id="totalScoreInput">
                <input type="hidden" name="correct_answers" id="correctAnswersInput">

                <!-- Timer and Progress -->
                <div class="flex justify-between items-center mb-6">
                    <!-- Circular Timer -->
                    <div class="relative w-16 h-16">
                        <svg class="timer-circle w-16 h-16">
                            <circle cx="32" cy="32" r="28" fill="none" stroke="#e5e7eb" stroke-width="4"/>
                            <circle id="timerCircle" cx="32" cy="32" r="28" fill="none" stroke="#3b82f6" 
                                    stroke-width="4" style="animation: countdown 30s linear infinite"/>
                        </svg>
                        <span id="timer" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 
                                             text-xl font-bold text-blue-600">30</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="flex-1 ml-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-sm text-gray-600">Progress</span>
                            <span class="text-sm text-gray-600">
                                Question <span id="currentQuestion">1</span> of <span id="totalQuestions">10</span>
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" 
                                 id="progressBar" style="width: 10%"></div>
                        </div>
                    </div>
                </div>

                <!-- Question Card -->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4" id="questionText"></h2>
                    
                    <!-- Multiple Choice Container -->
                    <div id="multipleChoiceContainer" class="space-y-3 mb-4 hidden">
                        <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition cursor-pointer">
                            <input type="radio" name="answer" id="optionA" value="A" class="mr-3">
                            <label for="optionA" id="labelA" class="text-gray-700 cursor-pointer w-full">Option A</label>
                        </div>
                        <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition cursor-pointer">
                            <input type="radio" name="answer" id="optionB" value="B" class="mr-3">
                            <label for="optionB" id="labelB" class="text-gray-700 cursor-pointer w-full">Option B</label>
                        </div>
                        <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition cursor-pointer">
                            <input type="radio" name="answer" id="optionC" value="C" class="mr-3">
                            <label for="optionC" id="labelC" class="text-gray-700 cursor-pointer w-full">Option C</label>
                        </div>
                        <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition cursor-pointer">
                            <input type="radio" name="answer" id="optionD" value="D" class="mr-3">
                            <label for="optionD" id="labelD" class="text-gray-700 cursor-pointer w-full">Option D</label>
                        </div>
                    </div>

                    <!-- Text Input Container -->
                    <div id="textInputContainer" class="hidden">
                        <input type="text" id="textAnswer" placeholder="Type your answer here" 
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 
                                      focus:outline-none hover:border-blue-300 transition">
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex justify-between items-center">
                    <button type="button" id="prevButton" onclick="previousQuestion()" 
                            class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 
                                   transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <div class="text-center">
                        <span class="text-gray-600">
                            Answered: <span id="answeredCount" class="font-bold text-blue-600">0</span>
                            /<span id="totalCount">10</span>
                        </span>
                    </div>
                    <button type="button" id="nextButton" onclick="nextQuestion()"
                            class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 
                                   transition duration-300">
                        Next
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Modified JavaScript for handling quiz submission
    function finishQuiz() {
        clearInterval(timer);
        const results = calculateScore();
        
        // Update hidden form inputs
        document.getElementById('answersInput').value = JSON.stringify(answers);
        document.getElementById('totalScoreInput').value = results.totalScore;
        document.getElementById('correctAnswersInput').value = results.correctAnswers;
        
        // Add submit input and submit form
        const submitInput = document.createElement('input');
        submitInput.type = 'hidden';
        submitInput.name = 'submit_quiz';
        submitInput.value = '1';
        document.getElementById('quizForm').appendChild(submitInput);
        document.getElementById('quizForm').submit();
    }
    </script>

    <script src="quiz.js"></script>
</body>
</html>