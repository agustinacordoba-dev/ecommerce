const inputBuscador = document.getElementById('inputBuscador');
const contenedor = document.getElementById('contenedorProductos');

// Guardamos los productos que Mustache cargó al inicio
const htmlInicial = contenedor.innerHTML;

inputBuscador.addEventListener('input', (e) => {
    const texto = e.target.value.trim();

    if (texto.length >= 3) {
        fetch(`/ecommerce/home/buscadorAjax?buscar=${encodeURIComponent(texto)}`)
            .then(response => response.json())
            .then(productos => {
                mostrarProductos(productos);
            });
    } else if (texto.length === 0) {
        // Si borra todo, volvemos a poner la grilla inicial completa
        contenedor.innerHTML = htmlInicial;
    }
});

function mostrarProductos(productos) {
    contenedor.innerHTML = ''; // Limpiamos la grilla

    if (productos.length === 0) {
        contenedor.innerHTML = `
            <div class="no-products">
                <p>No se encontraron productos coincidentes.</p>
            </div>
        `;
        return;
    }

    // Renderizamos cada tarjeta de producto
    productos.forEach(p => {
        // Fallback si el producto no tiene foto
        const imagenHTML = p.foto
            ? `<img src="/ecommerce/${p.foto}" alt="${p.nombre}" class="product-img" />`
            : `<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.6">
                <rect x="4" y="4" width="16" height="12" rx="1"/>
                <path d="M8 20h8M12 16v4"/>
               </svg>`;

        // Bloque dinámico para el precio según si está en oferta o no
        let precioHTML = '';
        let badgeHTML  = '';

        if (p.enOferta) {
            badgeHTML = `<span class="badge-discount">${p.descuento}% OFF</span>`;
            precioHTML = `
                <div class="price-wrapper">
                    <span class="product-price-old">$${p.precio}</span>
                    <span class="product-price-offer">$${p.precio_nuevo}</span>
                </div>
            `;
        } else {
            precioHTML = `<span class="product-price">$${p.precio}</span>`;
        }

        contenedor.innerHTML += `
            <article class="product-card">
                <a href="/ecommerce/producto/detalle?id=${p.id}" class="product-thumb">
                    ${badgeHTML}
                    ${imagenHTML}
                </a>

                <div class="product-info">
                    <span class="product-code">${p.codigo || ''}</span>
                    <a href="/ecommerce/producto/detalle?id=${p.id}" class="product-name">${p.nombre}</a>
                    <span class="product-category">${p.categoria || ''}</span>

                    <div class="product-footer-row">
                        ${precioHTML}
                        <button class="add-btn" aria-label="Agregar al carrito">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"/>
                                <circle cx="19" cy="21" r="1"/>
                                <path d="M2 3h2l2.4 12.4a2 2 0 002 1.6h9.2a2 2 0 002-1.6L21 7H6"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </article>
        `;
    });
}
