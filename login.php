<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="flex justify-center h-[100vh]">
        <div class="w-full max-w-md p-8 space-y-6 bg-white shadow-2xl rounded-2xl h-fit my-auto">
            <h2 class="text-2xl font-extrabold text-center text-blue-900">Who are you?</h2>
            <form action="quiz.php" method="POST">
                <div class="mb-3 flex flex-col">
                    <label class="text-gray-700" for="name">
                        Name
                    </label>
                    <input type="text" id="name" name="nama" placeholder="Enter your name" class="form-control border border-black p-2 rounded-lg focus:ring-[1px] focus:ring-blue-500 focus:outline-none hover:ring hover:ring-blue-200 hover:outline-none transition duration-500">
                </div>
                <div class="mb-3 flex flex-col">
                    <label class="text-gray-700" for="nim">
                        Student ID
                    </label>
                    <input type="text" id="nim" name="nim" placeholder="Enter your 9-digit NIM" maxlength="9" class="form-control border border-black p-2 rounded-lg focus:ring-[1px] focus:ring-blue-500 focus:outline-none hover:ring hover:ring-blue-200 hover:outline-none transition duration-500">
                </div>
                <button class="w-fit mt-4 ml-[132px] py-2 px-8 font-semibold bg-blue-600 transition duration-500 text-white rounded-xl hover:bg-blue-800 hover:text-gray-300">
                    Next
                </button>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>