// results.js
document.addEventListener('DOMContentLoaded', () => {
    const results = JSON.parse(localStorage.getItem('quizResults'));
    if (!results) {
        window.location.href = 'index.php';
        return;
    }

    // Display student information
    document.getElementById('studentName').textContent = results.name;
    document.getElementById('studentNIM').textContent = results.nim;

    // Calculate total possible score
    const totalPossibleScore = results.questions.reduce((sum, q) => sum + q.points, 0);

    // Display score summary
    document.getElementById('totalScore').textContent = 
        `${results.totalScore}/${totalPossibleScore}`;
    document.getElementById('questionsAttempted').textContent = 
        `answered ${results.answeredQuestions} of ${results.questions.length}`;
    document.getElementById('correctAnswers').textContent = results.correctAnswers;

    // Add score percentage
    document.getElementById('scorePercentage').textContent = 
        `${Math.round((results.totalScore / totalPossibleScore) * 100)}%`;

    // Display question breakdown
    const breakdownContainer = document.getElementById('questionBreakdown');
    results.questions.forEach((question, index) => {
        const userAnswer = results.answers[index];
        const isCorrect = question.type === 'multiple' 
            ? userAnswer === question.correctAnswer
            : userAnswer?.toLowerCase() === question.correctAnswer.toLowerCase();

        const questionElement = document.createElement('div');
        questionElement.className = `p-4 border rounded-lg ${isCorrect ? 'bg-green-50' : 'bg-red-50'}`;
        
        let answerDisplay = userAnswer || 'Not answered';
        if (question.type === 'multiple' && userAnswer) {
            answerDisplay = `${userAnswer}: ${question.options[userAnswer]}`;
        }

        questionElement.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="font-medium">Question ${index + 1}</p>
                    <p class="text-gray-600">${question.question}</p>
                    <p class="mt-2">Your answer: <span class="font-medium">${answerDisplay}</span></p>
                    <p>Correct answer: <span class="font-medium">
                        ${question.type === 'multiple' ? 
                          `${question.correctAnswer}: ${question.options[question.correctAnswer]}` : 
                          question.correctAnswer}</span>
                    </p>
                </div>
                <div class="ml-4">
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        ${isCorrect ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${isCorrect ? '+' + question.points : '0'} points
                    </span>
                </div>
            </div>
        `;
        
        breakdownContainer.appendChild(questionElement);
    });

    // Add final grade section
    const gradeContainer = document.createElement('div');
    gradeContainer.className = 'mt-8 p-6 bg-gray-50 rounded-lg text-center';
    const percentage = (results.totalScore / totalPossibleScore) * 100;
    let grade = 'A';
    if (percentage < 90) grade = 'B';
    if (percentage < 80) grade = 'C';
    if (percentage < 70) grade = 'D';
    if (percentage < 60) grade = 'F';

    gradeContainer.innerHTML = `
        <h3 class="text-2xl font-bold mb-2">Final Grade</h3>
        <p class="text-4xl font-bold ${
            grade === 'A' ? 'text-green-600' :
            grade === 'B' ? 'text-blue-600' :
            grade === 'C' ? 'text-yellow-600' :
            grade === 'D' ? 'text-orange-600' :
            'text-red-600'
        }">${grade}</p>
        <p class="text-gray-600 mt-2">${Math.round(percentage)}% Overall</p>
    `;
    
    document.getElementById('resultContainer').appendChild(gradeContainer);
});