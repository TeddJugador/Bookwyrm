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


const books = document.querySelectorAll('.card');
books.forEach(card => {
    card.addEventListener('click', () => {
        //check if card is flipped
        card.classList.toggle('flipped');
    });
});

document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function(event) {
        if (!confirm('Are you sure you want to delete this manga?')) {
            event.preventDefault();
        }
    });
});