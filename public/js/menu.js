document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.site-header');
    const navRow = document.querySelector('.nav-row');
    if (!header || !navRow) return;

    // Botón hamburguesa (3 líneas fijas, sin animación a X)
    const toggle = document.createElement('button');
    toggle.className = 'nav-toggle';
    toggle.setAttribute('aria-label', 'Abrir menú');
    toggle.innerHTML = '<span></span><span></span><span></span>';
    navRow.appendChild(toggle);

    toggle.addEventListener('click', () => {
        header.classList.toggle('nav-open');
    });

    // Toggle del submenú "Categorías" en mobile (clic en vez de hover)
    const dropdownToggle = document.querySelector('.has-dropdown > .nav-link');
    if (dropdownToggle) {
        dropdownToggle.addEventListener('click', (e) => {
            if (window.innerWidth <= 767) {
                e.preventDefault();
                dropdownToggle.closest('.has-dropdown').classList.toggle('open');
            }
        });
    }
});