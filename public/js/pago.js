
    const subtotal = parseFloat("{{totalCarrito}}");

    function toggleEnvio(esCorreo) {
    const noticeLocal = document.getElementById('notice-local');
    const formCorreo = document.getElementById('form-correo');
    const labelEnvio = document.getElementById('label-envio');
    const labelTotal = document.getElementById('label-total');

    if (esCorreo) {
    noticeLocal.style.display = 'none';
    formCorreo.style.display = 'block';
} else {
    noticeLocal.style.display = 'flex';
    formCorreo.style.display = 'none';
    labelEnvio.innerText = 'Gratis';
    labelEnvio.className = 'free-text';
    labelTotal.innerText = '$' + subtotal.toFixed(2);
}
}

    function cotizarEnvio() {
    const cp = document.getElementById('codigo_postal').value;
    if (!cp.trim()) {
    alert('Por favor ingresa tu código postal');
    return;
}

    const tarifaEnvio = 3500.00;
    const total = subtotal + tarifaEnvio;

    const labelEnvio = document.getElementById('label-envio');
    labelEnvio.innerText = '$' + tarifaEnvio.toFixed(2);
    labelEnvio.className = 'cost-text';

    document.getElementById('label-total').innerText = '$' + total.toFixed(2);
}
