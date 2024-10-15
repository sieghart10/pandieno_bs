window.showPassword = () => {
    let pass = document.getElementById('password');
    let conpass = document.getElementById('confirm-password');
    if (pass.type === 'password' || conpass === 'password') {
      pass.type = 'text';
      conpass.type = 'text';
    } else {
      pass.type = 'password';
      conpass.type = 'password'
    }
};