document.addEventListener('DOMContentLoaded', () => {
    const checkbox = document.querySelector('input[type="checkbox"]');

    window.showPassword = () => {
        let pass = document.getElementById('password');
        let conpass = document.getElementById('confirm-password');
        
        if (pass) {
            pass.type = (pass.type === 'password') ? 'text' : 'password';
        }

        if (conpass) {
            conpass.type = (conpass.type === 'password') ? 'text' : 'password';
        }
    };

    window.addEventListener('beforeunload', () => {
        checkbox.checked = false;
    });

    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', () => {
            checkbox.checked = false;
        });
    }
});
