<?php
include_once "./base/header.php";
include_once "./database/db.php";

// Проверка, авторизован ли пользователь
if (isset($_SESSION['user_id'])) {
    header('Location: profile'); // Перенаправить на страницу профиля или другую страницу
    exit();
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<style>
.form-control, .input-group-text, .form-text, .invalid-feedback {
    font-size: 0.875rem; /* Small font size */
    font-family: 'Arial', sans-serif; /* Minimalist font */
}
.input-group-text {
    cursor: pointer;
}
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="registration-form">
                <h2 class="text-center mb-4">Регистрация</h2>
                <form id="registrationForm" method="POST">
                    <div class="mb-3">
                        <label for="firstName" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" maxlength="25" required placeholder="Иван">
                        <div class="invalid-feedback" id="firstNameError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Фамилия</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" maxlength="25" required placeholder="Иванов">
                        <div class="invalid-feedback" id="lastNameError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="middleName" class="form-label">Отчество</label>
                        <input type="text" class="form-control" id="middleName" name="middleName" maxlength="25" placeholder="Иванович">
                        <div class="invalid-feedback" id="middleNameError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="groupName" class="form-label">Группа</label>
                        <input type="text" class="form-control" id="groupName" name="groupName" required placeholder="АС-20-1">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="example@asu.ru">
                        <div class="invalid-feedback" id="emailError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="phoneNumber" class="form-label">Номер телефона</label>
                        <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" required>
                        <div class="invalid-feedback" id="phoneNumberError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="input-group-append">
                                <span class="input-group-text" id="generatePassword">Сгенерировать</span>
                                <span class="input-group-text" id="togglePassword">Показать</span>
                            </div>
                        </div>
                        <div id="passwordStrength" class="form-text"></div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Подтвердите пароль</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                            <div class="input-group-append">
                                <span class="input-group-text" id="toggleConfirmPassword">Показать</span>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="confirmPasswordError"></div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
                </form>
                <div class="alert alert-danger mt-3 d-none" id="validationAlert">
                    Пожалуйста, заполните все поля корректно.
                </div>
                <div class="alert alert-success mt-3 d-none" id="successMessage">
                    Регистрация успешна! Пожалуйста, подтвердите ваш email.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const validationAlert = document.getElementById('validationAlert');
    const successMessage = document.getElementById('successMessage');
    const firstName = document.getElementById('firstName');
    const lastName = document.getElementById('lastName');
    const middleName = document.getElementById('middleName');
    const email = document.getElementById('email');
    const phoneNumber = document.getElementById('phoneNumber');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    const passwordStrength = document.getElementById('passwordStrength');
    const confirmPasswordError = document.getElementById('confirmPasswordError');
    const generatePasswordButton = document.getElementById('generatePassword');
    const togglePasswordButton = document.getElementById('togglePassword');
    const toggleConfirmPasswordButton = document.getElementById('toggleConfirmPassword');
    let tempEmailDomains = [];

    // Fetch the list of disposable email domains
    fetch('https://raw.githubusercontent.com/andreis/disposable-email-domains/master/domains.txt')
        .then(response => response.text())
        .then(data => {
            tempEmailDomains = data.split('\n');
        })
        .catch(error => console.error('Error fetching disposable email domains:', error));

    function validateField(field, errorMsgId, regex, maxLength) {
        const errorMsg = document.getElementById(errorMsgId);
        field.classList.remove('is-invalid');
        errorMsg.textContent = '';

        if (!regex.test(field.value) || field.value.length > maxLength) {
            field.classList.add('is-invalid');
            if (field.value.length > maxLength) {
                errorMsg.textContent = `Поле не должно превышать ${maxLength} символов.`;
            } else {
                errorMsg.textContent = 'Поле не должно содержать цифры и начинаться с них.';
            }
        }
    }

    function limitInputLength(field, maxLength) {
        if (field.value.length > maxLength) {
            field.value = field.value.substring(0, maxLength);
        }
    }

    function validateEmail() {
        const emailValue = email.value;
        const emailError = document.getElementById('emailError');
        
        email.classList.remove('is-invalid');
        emailError.textContent = '';

        // Check for temporary email domain
        const emailDomain = emailValue.split('@')[1];
        if (tempEmailDomains.includes(emailDomain)) {
            email.classList.add('is-invalid');
            emailError.textContent = 'Временные email-адреса не допускаются.';
            return;
        }

        // AJAX проверка существующего email
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './database/check_email.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                if (xhr.responseText === 'exists') {
                    email.classList.add('is-invalid');
                    emailError.textContent = 'Этот email уже используется.';
                }
            }
        };
        xhr.send('email=' + encodeURIComponent(emailValue));
    }

    function checkPasswordStrength() {
        const value = password.value;
        let strength = 'Легкий';
        let color = 'red';
        if (value.length >= 8 && /[A-Z]/.test(value) && /[a-z]/.test(value) && /[0-9]/.test(value) && /[^A-Za-z0-9]/.test(value)) {
            strength = 'Сложный';
            color = 'green';
        } else if (value.length >= 6) {
            strength = 'Средний';
            color = 'orange';
        }
        passwordStrength.textContent = `Сложность пароля: ${strength}`;
        passwordStrength.style.color = color;
    }

    function validatePasswordMatch() {
        confirmPassword.classList.remove('is-invalid');
        confirmPasswordError.textContent = '';

        if (password.value !== confirmPassword.value) {
            confirmPassword.classList.add('is-invalid');
            confirmPasswordError.textContent = 'Пароли не совпадают.';
        }
    }

    function generateStrongPassword() {
        const upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        const lower = "abcdefghijklmnopqrstuvwxyz";
        const numbers = "0123456789";
        const symbols = "!@#$%^&*()";
        const allChars = upper + lower + numbers + symbols;
        let password = "";
        password += upper.charAt(Math.floor(Math.random() * upper.length));
        password += lower.charAt(Math.floor(Math.random() * lower.length));
        password += numbers.charAt(Math.floor(Math.random() * numbers.length));
        password += symbols.charAt(Math.floor(Math.random() * symbols.length));
        for (let i = 4; i < 12; i++) {
            password += allChars.charAt(Math.floor(Math.random() * allChars.length));
        }
        return password;
    }

    function togglePasswordVisibility(input, button) {
        if (input.type === "password") {
            input.type = "text";
            button.textContent = "Скрыть";
        } else {
            input.type = "password";
            button.textContent = "Показать";
        }
    }

    generatePasswordButton.addEventListener('click', () => {
        const newPassword = generateStrongPassword();
        password.value = newPassword;
        confirmPassword.value = newPassword;
        checkPasswordStrength();
        validatePasswordMatch();
    });

    togglePasswordButton.addEventListener('click', () => {
        togglePasswordVisibility(password, togglePasswordButton);
    });

    toggleConfirmPasswordButton.addEventListener('click', () => {
        togglePasswordVisibility(confirmPassword, toggleConfirmPasswordButton);
    });

    firstName.addEventListener('input', () => {
        validateField(firstName, 'firstNameError', /^[^\d\s][\D]*$/, 25);
        limitInputLength(firstName, 25);
    });

    lastName.addEventListener('input', () => {
        validateField(lastName, 'lastNameError', /^[^\d\s][\D]*$/, 25);
        limitInputLength(lastName, 25);
    });

    middleName.addEventListener('input', () => {
        validateField(middleName, 'middleNameError', /^[^\d\s][\D]*$/, 25);
        limitInputLength(middleName, 25);
    });

    email.addEventListener('input', validateEmail);
    email.addEventListener('blur', validateEmail);

    password.addEventListener('input', checkPasswordStrength);
    confirmPassword.addEventListener('input', validatePasswordMatch);
    confirmPassword.addEventListener('blur', validatePasswordMatch);

    firstName.addEventListener('blur', () => validateField(firstName, 'firstNameError', /^[^\d\s][\D]*$/, 25));
    lastName.addEventListener('blur', () => validateField(lastName, 'lastNameError', /^[^\d\s][\D]*$/, 25));
    middleName.addEventListener('blur', () => validateField(middleName, 'middleNameError', /^[^\d\s][\D]*$/, 25));

    // Initialize intl-tel-input
    const iti = window.intlTelInput(phoneNumber, {
        initialCountry: "auto",
        geoIpLookup: function(callback) {
            fetch('https://ipinfo.io/json')
                .then(response => response.json())
                .then(data => callback(data.country))
                .catch(() => callback('us'));
        },
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
    });

    phoneNumber.addEventListener('blur', function() {
        const phoneNumberError = document.getElementById('phoneNumberError');
        phoneNumber.classList.remove('is-invalid');
        phoneNumberError.textContent = '';

        if (!iti.isValidNumber()) {
            phoneNumber.classList.add('is-invalid');
            phoneNumberError.textContent = 'Введите действительный номер телефона.';
        }
    });

    form.addEventListener('submit', function(event) {
        let isValid = true;
        
        // Validate each field
        validateField(firstName, 'firstNameError', /^[^\d\s][\D]*$/, 25);
        validateField(lastName, 'lastNameError', /^[^\d\s][\D]*$/, 25);
        validateField(middleName, 'middleNameError', /^[^\d\s][\D]*$/, 25);
        validateEmail();
        validatePasswordMatch();

        // Check if any field has the 'is-invalid' class
        const fields = [firstName, lastName, middleName, email, phoneNumber, password, confirmPassword];
        fields.forEach(field => {
            if (field.classList.contains('is-invalid')) {
                isValid = false;
            }
        });

        if (!isValid) {
            event.preventDefault();
            validationAlert.classList.remove('d-none');
        } else {
            validationAlert.classList.add('d-none');
            
            // Отправка формы через AJAX
            event.preventDefault();
            const formData = new FormData(form);
            fetch('./database/register_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    form.reset();
                    successMessage.classList.remove('d-none');
                } else {
                    validationAlert.classList.remove('d-none');
                    validationAlert.innerHTML = data.messages.join('<br>');
                }
            })
            .catch(error => {
                validationAlert.classList.remove('d-none');
                validationAlert.innerHTML = 'Произошла ошибка при регистрации. Попробуйте еще раз.';
            });
        }
    });
});
</script>

<?php
include_once "./base/footer.php";
?>
