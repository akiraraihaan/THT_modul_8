// Quiz questions data with complete set of questions
const questions = [
    {
        id: 1,
        type: 'multiple',
        question: 'What is the capital of Indonesia?',
        options: {
            A: 'Bandung',
            B: 'Jakarta',
            C: 'Surabaya',
            D: 'Medan'
        },
        correctAnswer: 'B',
        points: 5
    },
    {
        id: 2,
        type: 'text',
        question: 'What is the name of Indonesian currency?',
        correctAnswer: 'Rupiah',
        points: 5
    },
    {
        id: 3,
        type: 'multiple',
        question: 'Which of these is NOT an Indonesian island?',
        options: {
            A: 'Sumatra',
            B: 'Java',
            C: 'Borneo',
            D: 'Tasmania'
        },
        correctAnswer: 'D',
        points: 5
    },
    {
        id: 4,
        type: 'text',
        question: 'In which year did Indonesia gain independence?',
        correctAnswer: '1945',
        points: 5
    },
    {
        id: 5,
        type: 'multiple',
        question: 'What is the largest Buddhist temple in Indonesia?',
        options: {
            A: 'Borobudur',
            B: 'Prambanan',
            C: 'Mendut',
            D: 'Sewu'
        },
        correctAnswer: 'A',
        points: 5
    },
    {
        id: 6,
        type: 'text',
        question: 'What is Indonesia\'s national motto?',
        correctAnswer: 'Bhinneka Tunggal Ika',
        points: 5
    },
    {
        id: 7,
        type: 'multiple',
        question: 'Which programming language is used for Android app development?',
        options: {
            A: 'Swift',
            B: 'Kotlin',
            C: 'Ruby',
            D: 'PHP'
        },
        correctAnswer: 'B',
        points: 5
    },
    {
        id: 8,
        type: 'text',
        question: 'What does HTML stand for?',
        correctAnswer: 'Hypertext Markup Language',
        points: 5
    },
    {
        id: 9,
        type: 'multiple',
        question: 'Which of these is a JavaScript framework?',
        options: {
            A: 'Django',
            B: 'Flask',
            C: 'React',
            D: 'Laravel'
        },
        correctAnswer: 'C',
        points: 5
    },
    {
        id: 10,
        type: 'text',
        question: 'What port number does HTTP typically use?',
        correctAnswer: '80',
        points: 5
    }
];

let currentQuestionIndex = 0;
let answers = new Array(questions.length).fill(null);
let timer;
let timeLeft = 30;

// Initialize the quiz
document.addEventListener('DOMContentLoaded', () => {
    // Check if user info exists
    const playerName = localStorage.getItem('playerName');
    const playerNIM = localStorage.getItem('playerNIM');
    
    if (!playerName || !playerNIM) {
        window.location.href = 'login.php';
        return;
    }

    // Display user info
    document.getElementById('playerName').textContent = playerName;
    document.getElementById('playerNIM').textContent = playerNIM;

    loadQuestion(currentQuestionIndex);
    startTimer();
    updateProgress();
    updateAnsweredStatus();
});

function loadQuestion(index) {
    const question = questions[index];
    document.getElementById('questionText').textContent = question.question;
    document.getElementById('currentQuestion').textContent = index + 1;
    document.getElementById('totalQuestions').textContent = questions.length;

    // Reset timer
    timeLeft = 30;
    document.getElementById('timer').textContent = timeLeft;
    resetTimerAnimation();
    if (timer) clearInterval(timer);
    startTimer();

    const multipleChoiceContainer = document.getElementById('multipleChoiceContainer');
    const textInputContainer = document.getElementById('textInputContainer');

    if (question.type === 'multiple') {
        multipleChoiceContainer.classList.remove('hidden');
        textInputContainer.classList.add('hidden');
        
        Object.keys(question.options).forEach(key => {
            const label = document.getElementById(`label${key}`);
            const input = document.getElementById(`option${key}`);
            label.textContent = question.options[key];
            input.checked = answers[index] === key;
        });
    } else {
        multipleChoiceContainer.classList.add('hidden');
        textInputContainer.classList.remove('hidden');
        
        const textInput = document.getElementById('textAnswer');
        textInput.value = answers[index] || '';
    }

    document.getElementById('prevButton').disabled = index === 0;
    document.getElementById('nextButton').textContent = 
        index === questions.length - 1 ? 'Finish' : 'Next';

    updateProgress();
}

function resetTimerAnimation() {
    const timerCircle = document.getElementById('timerCircle');
    timerCircle.style.strokeDashoffset = '0';
    
    // Hitung total panjang circle
    const circumference = 2 * Math.PI * 28; // 2πr where r=28 
    
    // Update stroke-dasharray
    timerCircle.style.strokeDasharray = `${circumference}`;
    
    // Animasi stroke-dashoffset
    timerCircle.style.transition = 'stroke-dashoffset 100s linear';
    setTimeout(() => {
        timerCircle.style.strokeDashoffset = circumference;
    }, 50);
}

function startTimer() {
    resetTimerAnimation();
    timer = setInterval(() => {
        timeLeft-=0.5;
        document.getElementById('timer').textContent = timeLeft;
        
        // Update timer circle setiap detik
        const timerCircle = document.getElementById('timerCircle');
        const circumference = 2 * Math.PI * 28;
        const offset = circumference * (timeLeft / 30);
        timerCircle.style.strokeDashoffset = circumference - offset;
        
        if (timeLeft <= 0) {
            clearInterval(timer);
            saveAnswer();
            if (currentQuestionIndex < questions.length - 1) {
                nextQuestion();
            } else {
                finishQuiz();
            }
        }
    }, 1000);
}

function saveAnswer() {
    const question = questions[currentQuestionIndex];
    if (question.type === 'multiple') {
        const selectedOption = document.querySelector('input[name="answer"]:checked');
        answers[currentQuestionIndex] = selectedOption ? selectedOption.value : null;
    } else {
        const textAnswer = document.getElementById('textAnswer').value.trim();
        answers[currentQuestionIndex] = textAnswer || null;
    }
    updateAnsweredStatus();
}

function previousQuestion() {
    saveAnswer();
    if (currentQuestionIndex > 0) {
        currentQuestionIndex--;
        loadQuestion(currentQuestionIndex);
    }
}

function nextQuestion() {
    saveAnswer();
    if (currentQuestionIndex < questions.length - 1) {
        currentQuestionIndex++;
        loadQuestion(currentQuestionIndex);
    } else {
        finishQuiz();
    }
}

function updateProgress() {
    const progress = ((currentQuestionIndex + 1) / questions.length) * 100;
    document.getElementById('progressBar').style.width = `${progress}%`;
}

function updateAnsweredStatus() {
    const answeredCount = answers.filter(answer => answer !== null).length;
    document.getElementById('answeredCount').textContent = answeredCount;
    document.getElementById('totalCount').textContent = questions.length;
}

function calculateScore() {
    let totalScore = 0;
    let correctAnswers = 0;

    questions.forEach((question, index) => {
        const userAnswer = answers[index];
        if (userAnswer) {
            if (question.type === 'multiple') {
                if (userAnswer === question.correctAnswer) {
                    totalScore += question.points;
                    correctAnswers++;
                }
            } else {
                if (userAnswer.toLowerCase() === question.correctAnswer.toLowerCase()) {
                    totalScore += question.points;
                    correctAnswers++;
                }
            }
        }
    });

    return {
        totalScore,
        correctAnswers,
        answeredQuestions: answers.filter(a => a !== null).length
    };
}

function finishQuiz() {
    clearInterval(timer);
    const results = calculateScore();
    
    // Save results to localStorage
    const quizResults = {
        name: localStorage.getItem('playerName'),
        nim: localStorage.getItem('playerNIM'),
        ...results,
        answers: answers,
        questions: questions
    };
    localStorage.setItem('quizResults', JSON.stringify(quizResults));
    
    // Redirect to results page
    window.location.href = 'results.php';
}