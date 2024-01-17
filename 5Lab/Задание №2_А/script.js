document.getElementById('processButton').addEventListener('click', function() {
    const input = document.getElementById('arrayInput').value;
    const numbers = input.split(',').map(num => parseFloat(num.trim())).filter(num => !isNaN(num));

    const result = sArray(numbers);
    document.getElementById('result').textContent = `Результат: ${result}`;
});

function sArray(numbers) {
    let sum = 0;
    let firstIndex = -1, lastIndex = -1;
    // Находим индексы первого и последнего элементов
    for (let i = 0; i < numbers.length; i++) {
        if (Math.abs(Math.sqrt(numbers[i]) - Math.cbrt(numbers[i])) <= 0.00001) {
            if (firstIndex === -1) firstIndex = i;
            lastIndex = i;
        }
    }
    // Считаем сумму элементов между первым и последним 
    if (firstIndex !== -1 && lastIndex !== -1) {
        for (let i = firstIndex + 1; i < lastIndex; i++) {
            sum += numbers[i];
        }
    }
    return sum;
}
