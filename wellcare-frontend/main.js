//شغل الانيميشن للفورم
const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});







//test الاكتئاب
function calculateResult() {
    let score = 0;
    const questions = document.querySelectorAll('.question');
    questions.forEach((question) => {
        const checked = question.querySelector('input[type="radio"]:checked');
        if (checked) {
            score += parseInt(checked.value);
        }
    });

    let resultText = '';
    if (score >= 6) {
        resultText = 'يبدو أنك قد تعاني من الاكتئاب. يُفضل استشارة مختص.';
    } else if (score >= 3) {
        resultText = 'لديك بعض الأعراض التي قد تشير إلى الاكتئاب. يُفضل مراقبة حالتك واستشارة مختص إذا استمرت الأعراض.';
    } else {
        resultText = 'لا توجد علامات قوية على الاكتئاب. لكن إذا كنت تشعر بالقلق، يُفضل استشارة مختص.';
    }

    document.getElementById('result').innerText = resultText;
}






