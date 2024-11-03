document.addEventListener('DOMContentLoaded', () => {
    const checkbox = document.querySelector('input[type="checkbox"]');

    window.showPassword = () => {
        let pass = document.getElementById('password');
        let conpass = document.getElementById('confirm-password'); // Keep this for future use
        
        if (pass) {
            pass.type = (pass.type === 'password') ? 'text' : 'password';
        }

        // Only toggle confirm password if it exists
        if (conpass) {
            conpass.type = (conpass.type === 'password') ? 'text' : 'password';
        }
    };

    // Uncheck the checkbox on page unload
    window.addEventListener('beforeunload', () => {
        checkbox.checked = false;
    });

    // Uncheck the checkbox upon form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', () => {
            checkbox.checked = false;
        });
    }
});
