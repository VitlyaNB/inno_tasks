// Переключение шагов восстановления пароля
function goToStep(stepId) {
    document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
    document.getElementById(stepId).classList.add('active');
}

// Обработка шага 1 (отправка email)
document.getElementById('step1').addEventListener('submit', function(e) {
    e.preventDefault();
    // Здесь можно добавить AJAX-запрос к send_reset_code.php
    // После успешной отправки переключаем на шаг 2
    goToStep('step2');
});

// Обработка шага 2 (проверка кода)
document.getElementById('step2').addEventListener('submit', function(e) {
    e.preventDefault();
    // Здесь можно добавить AJAX-запрос к check_reset_code.php
    // Если код верный → переключаем на шаг 3
    goToStep('step3');
    // Сохраняем email в скрытое поле
    document.getElementById('resetEmail').value = this.email.value;
});
