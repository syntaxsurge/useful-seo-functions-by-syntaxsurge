document.addEventListener('DOMContentLoaded', function() {
    const copyButton = document.getElementById('copyButton');
    const generatedPassword = document.getElementById('generatedPassword');

    copyButton.addEventListener('click', function() {
        const textArea = document.createElement('textarea');
        textArea.value = generatedPassword.textContent;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Password copied to clipboard!');
    });
});
