document.addEventListener('DOMContentLoaded', updateButton);

const buttons = document.querySelectorAll('.genre-btn');
function updateButton() {
    for (const button of buttons) {
        button.addEventListener('click', () => {
            const checkbox = button.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            button.classList.toggle('genre-btn-active', checkbox.checked);
        });
    }
}

const clearButton = document.getElementById('reset-btn');

function resetButtons() {
    clearButton.addEventListener('click', () => {
        for (const button of buttons) {
            const checkbox = button.querySelector('input[type="checkbox"]');
            checkbox.checked = false;
            button.classList.remove('genre-btn-active');
        }
        updateButton();
    });
    
}
