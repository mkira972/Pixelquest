import './bootstrap';

// Bootstrap 5 (dropdowns, modals, offcanvas, toasts...)
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

/**
 * Petites interactions JavaScript de la boutique.
 */
document.addEventListener('DOMContentLoaded', () => {
    // Boutons +/- de quantite dans le panier.
    document.querySelectorAll('[data-qty-step]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const input = document.querySelector(btn.dataset.qtyTarget);
            if (!input) return;
            const step = parseInt(btn.dataset.qtyStep, 10);
            const max = parseInt(input.max || '99', 10);
            const next = Math.min(Math.max(parseInt(input.value, 10) + step, 1), max);
            input.value = next;
            input.form?.requestSubmit?.();
        });
    });

    // Confirmation avant suppression.
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            if (!window.confirm(form.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    // Filtre instantane des tableaux de l'admin.
    const filtre = document.querySelector('#table-filter');
    if (filtre) {
        filtre.addEventListener('input', () => {
            const terme = filtre.value.toLowerCase();
            document.querySelectorAll('[data-filterable] tbody tr').forEach((tr) => {
                tr.style.display = tr.textContent.toLowerCase().includes(terme) ? '' : 'none';
            });
        });
    }

    // Disparition automatique des messages flash.
    document.querySelectorAll('.alert-auto').forEach((alerte) => {
        setTimeout(() => alerte.classList.add('fade-out'), 4000);
    });
});
