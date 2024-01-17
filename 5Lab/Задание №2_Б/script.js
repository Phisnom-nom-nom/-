document.getElementById('processButton').addEventListener('click', function() {
    const input = document.getElementById('arrayInput').value;
    let numbers = input.split(',').map(num => parseFloat(num.trim())).filter(num => !isNaN(num));

    // Обработка для части А задания
    const sumResult = sArray(numbers);
    document.getElementById('result').textContent = `Сумма: ${sumResult}`;

    // Обработка для части Б задания 
    numbers = numbers.filter(num => !isEvenSumDigits(Math.abs(Math.trunc(num))));
    document.getElementById('result').textContent += ` | Отфильтрованный массив: [${numbers.join(', ')}]`;
});

function sArray(numbers) {
    let sum = 0;
    let firstIndex = -1, lastIndex = -1;
    for (let i = 0; i < numbers.length; i++) {
        if (Math.abs(Math.sqrt(numbers[i]) - Math.cbrt(numbers[i])) <= 0.00001) {
            if (firstIndex === -1) firstIndex = i;
            lastIndex = i;
        }
    }
    if (firstIndex !== -1 && lastIndex !== -1) {
        for (let i = firstIndex + 1; i < lastIndex; i++) {
            sum += numbers[i];
        }
    }
    return sum;
}

function isEvenSumDigits(number) {
    return number.toString().split('').reduce((sum, digit) => sum + parseInt(digit, 10), 0) % 2 === 0;
}
