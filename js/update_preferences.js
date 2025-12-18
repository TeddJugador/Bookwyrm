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