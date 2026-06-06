function showError(el, msg) {
    el.classList.add('is-invalid');
    var err = el.parentNode.querySelector('.field-error');
    if (!err) {
        err = document.createElement('div');
        err.className = 'field-error';
        el.parentNode.appendChild(err);
    }
    err.textContent = msg;
}

function clearError(el) {
    el.classList.remove('is-invalid');
    var err = el.parentNode.querySelector('.field-error');
    if (err) err.remove();
}

function clearErrors(form) {
    form.querySelectorAll('.is-invalid').forEach(function(el) {
        el.classList.remove('is-invalid');
    });
    form.querySelectorAll('.field-error').forEach(function(el) {
        el.remove();
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            var identifier = document.getElementById('identifier');
            var password = document.getElementById('password');
            var valid = true;

            clearErrors(this);

            if (identifier.value.trim().length < 3) {
                showError(identifier, 'Поне 3 символа');
                valid = false;
            }

            if (password.value.length < 8) {
                showError(password, 'Поне 8 символа');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });

        loginForm.querySelectorAll('.form-control').forEach(function(el) {
            el.addEventListener('blur', function() {
                clearError(this);

                if (this.id === 'identifier' && this.value.trim().length > 0 && this.value.trim().length < 3) {
                    showError(this, 'Поне 3 символа');
                }

                if (this.id === 'password' && this.value.length > 0 && this.value.length < 8) {
                    showError(this, 'Поне 8 символа');
                }
            });
        });
    }

    var registerForm = document.querySelector('.auth-card-wide form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            var username = document.getElementById('username');
            var email = document.getElementById('email');
            var password = document.getElementById('password');
            var confirm = document.getElementById('confirm-password');
            var valid = true;

            clearErrors(this);

            if (username.value.trim().length < 3) {
                showError(username, 'Поне 3 символа');
                valid = false;
            }

            if (!email.value.trim() || !email.validity.valid) {
                showError(email, 'Въведете валиден имейл');
                valid = false;
            }

            if (password.value.length < 8) {
                showError(password, 'Поне 8 символа');
                valid = false;
            }

            if (confirm.value !== password.value) {
                showError(confirm, 'Паролите не съвпадат');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });

        registerForm.querySelectorAll('.form-control').forEach(function(el) {
            el.addEventListener('blur', function() {
                clearError(this);

                if (this.id === 'username' && this.value.trim().length > 0 && this.value.trim().length < 3) {
                    showError(this, 'Поне 3 символа');
                }

                if (this.id === 'email' && this.value.trim().length > 0 && !this.validity.valid) {
                    showError(this, 'Невалиден имейл');
                }

                if (this.id === 'password' && this.value.length > 0 && this.value.length < 8) {
                    showError(this, 'Поне 8 символа');
                }

                if (this.id === 'confirm-password' && this.value.length > 0) {
                    var pw = document.getElementById('password');
                    if (this.value !== pw.value) {
                        showError(this, 'Паролите не съвпадат');
                    }
                }
            });
        });
    }
});
