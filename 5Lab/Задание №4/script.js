document.getElementById('processButton').addEventListener('click', function() {
    const inputString = document.getElementById('stringInput').value;
    const result = splitStrIntoChunks(inputString, 3);
    document.getElementById('result').textContent = `Фрагменты: ${result.join(', ')}`;
});

function splitStrIntoChunks(str, chunkSize) {
    const chunks = [];
    for (let i = 0; i < str.length; i += chunkSize) {
        chunks.push(str.substring(i, i + chunkSize));
    }
    return chunks;
}
