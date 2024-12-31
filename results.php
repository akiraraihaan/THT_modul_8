<?php
session_start();

if (!isset($_SESSION['quiz_completed'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'test';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT * FROM quiz_results WHERE nama = ? AND nim = ? ORDER BY submission_date DESC LIMIT 1");
$stmt->bind_param("ss", $_SESSION['nama'], $_SESSION['nim']);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

// Clear session after getting results
unset($_SESSION['quiz_completed']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Quiz Results</h1>
                <div class="h-1 w-24 bg-blue-500 mx-auto"></div>
            </div>

            <!-- Student Information -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Student Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Name:</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($result['nama']); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Student ID:</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($result['nim']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Score Summary -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Score Summary</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-gray-600">Total Score</p>
                        <p class="text-3xl font-bold text-blue-600"><?php echo $result['total_score']; ?></p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <p class="text-gray-600">Correct Answers</p>
                        <p class="text-3xl font-bold text-green-600"><?php echo $result['correct_answers']; ?>/10</p>
                    </div>
                </div>
            </div>

            <!-- Question Breakdown -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Question Breakdown</h2>
                <div class="space-y-4">
                    <?php
                    $answers = json_decode($result['answers'], true);
                    require_once 'quiz.js'; // This should be modified to get questions from a PHP file or database
                    foreach ($answers as $index => $answer) {
                        $questionNumber = $index + 1;
                        $isCorrect = false; // You need to implement the logic to check if answer is correct
                        $class = $isCorrect ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
                    ?>
                        <div class="p-4 rounded-lg border <?php echo $class; ?>">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold">Question <?php echo $questionNumber; ?></p>
                                    <p class="text-gray-600">Your answer: <?php echo htmlspecialchars($answer); ?></p>
                                </div>
                                <div class="flex items-center">
                                    <?php if ($isCorrect): ?>
                                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    <?php else: ?>
                                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center">
                <a href="login.php" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-300">
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>