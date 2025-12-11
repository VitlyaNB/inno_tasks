function openEdit(id, interest) {
    document.getElementById('editId').value = id;
    document.getElementById('editInterest').value = interest;
    document.getElementById('editOverlay').classList.remove('hidden');
    document.body.classList.add('blurred');
}

function closeModal() {
    document.getElementById('editOverlay').classList.add('hidden');
    document.body.classList.remove('blurred');
}

document.addEventListener('DOMContentLoaded', () => {
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const response = await fetch('/php/interests_edit.php', {
                method: 'POST',
                body: formData
            });
            if (response.ok) {
                location.reload();
            } else {
                alert("Ошибка при сохранении");
            }
        });
    }

    // Дополнительно: закрытие по Esc
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape") {
            closeModal();
        }
    });

    // Дополнительно: закрытие по клику вне модального окна
    const overlay = document.getElementById('editOverlay');
    if (overlay) {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                closeModal();
            }
        });
    }
});
