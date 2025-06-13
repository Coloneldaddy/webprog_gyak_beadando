document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    if (form) {
        form.onsubmit = function(e) {
            let errors = [];
            let name = document.getElementById('name').value.trim();
            let message = document.getElementById('message').value.trim();
            if (name.length < 2) errors.push("A név legalább 2 karakter legyen!");
            if (message.length < 5) errors.push("Az üzenet legalább 5 karakter legyen!");
            if (errors.length) {
                document.getElementById('formError').innerHTML = errors.join('<br>');
                e.preventDefault();
            }
        };
    }
});
