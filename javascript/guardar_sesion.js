const params = new URLSearchParams(window.location.search);

if (params.get('login') === 'ok') {
    localStorage.setItem('usuario', params.get('u'));
    localStorage.setItem('rol', params.get('r'));

    window.history.replaceState({}, document.title, window.location.pathname);
}