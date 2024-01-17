document.getElementById('checkButton').addEventListener('click', function() {
    const year = document.getElementById('yearInput').value;
    if (year) {
        const isLeapYear = (year % 4 === 0 && year % 100 !== 0) || year % 400 === 0;
        const message = isLeapYear ? "Високосный год. 366 дней." : "Не високосный год. 365 дней.";
        console.log(message);
        document.getElementById('result').textContent = message;
    } else {
        console.log("Введите год.");
        document.getElementById('result').textContent = "Введите год.";
    }
});
