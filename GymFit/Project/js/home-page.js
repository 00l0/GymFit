var signupForm, loginForm, menuSlider, menuIcon;

function showSignupForm() {
    window.scrollTo({ top: 0, behavior: 'smooth' }); 
    if (loginForm) {
        loginForm.classList.remove('show'); 
        setTimeout(() => (loginForm.style.display = 'none'), 300); 
    }
    if (signupForm) {
        signupForm.style.display = 'block';
        setTimeout(() => signupForm.classList.add('show'), 10); 
    }
}

function showLoginForm() {
    window.scrollTo({ top: 0, behavior: 'smooth' }); 
    if (signupForm) {
        signupForm.classList.remove('show'); 
        setTimeout(() => (signupForm.style.display = 'none'), 300);
    }
    if (loginForm) {
        loginForm.style.display = 'block';
        setTimeout(() => loginForm.classList.add('show'), 10); 
    }
}

function hideForm(form) {
    form.classList.remove('show');
    setTimeout(() => (form.style.display = 'none'), 300);
}

document.addEventListener('DOMContentLoaded', () => {
    menuIcon = document.querySelector('.menu i');
    menuSlider = document.querySelector('.menu-slider');
    signupForm = document.querySelector('.signup-form');
    loginForm = document.querySelector('.login-form');
    const closeSignupForm = document.querySelector('.signup-form i.bx-x');
    const closeLoginForm = document.querySelector('.login-form i.bx-x');
    const signupButtons = document.querySelectorAll('.signup-btn, .btn-signup');
    const loginButtons = document.querySelectorAll('.login-btn, .btn-login');
    const joinNowButtons = document.querySelectorAll('.joinnow-btn, .BoxJoinNow-btn');
    const joinUsNowButtons = document.querySelectorAll('.joinUsNow');
    const showSignupLinks = document.querySelectorAll('.show-signup-form');
    const showLoginLinks = document.querySelectorAll('.show-login-form');

    menuSlider.style.display = 'none';
    signupForm.style.display = 'none';
    loginForm.style.display = 'none';

    signupButtons.forEach(button => {
        button.addEventListener('click', () => {
            showSignupForm();
        });
    });

    loginButtons.forEach(button => {
        button.addEventListener('click', () => {
            showLoginForm();
        });
    });

    joinNowButtons.forEach(button => {
        button.addEventListener('click', () => {
            showSignupForm();
        });
    });

    joinUsNowButtons.forEach(button => {
        button.addEventListener('click', () => {
            showSignupForm();
        });
    });

    showSignupLinks.forEach(link => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            showSignupForm();
        });
    });

    showLoginLinks.forEach(link => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            showLoginForm();
        });
    });

    closeSignupForm.addEventListener('click', () => {
        hideForm(signupForm);
    });
    closeLoginForm.addEventListener('click', () => {
        hideForm(loginForm);
    });

    menuIcon.addEventListener('click', () => {
        menuSlider.classList.toggle('active'); 
        if (menuSlider.classList.contains('active')) {
            menuSlider.style.display = 'block';
        } else {
            setTimeout(() => menuSlider.style.display = 'none', 300);
        }
    });

    document.addEventListener('click', (event) => {
        if (!menuSlider.contains(event.target) && !menuIcon.contains(event.target)) {
            menuSlider.classList.remove('active');
            setTimeout(() => menuSlider.style.display = 'none', 300);
        }
    });

    const menuLinks = document.querySelectorAll('.menu-links');
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            menuSlider.classList.remove('active');
            setTimeout(() => menuSlider.style.display = 'none', 300);
        });
    });
});
