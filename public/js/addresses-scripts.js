// Search functionality
(function() {
    const searchInput = document.getElementById('searchInput');
    const searchCityInput = document.getElementById('searchCityInput');
    const typeFilter = document.getElementById('typeFilter');

    function filterAddresses() {
        const searchTerm = (searchInput && searchInput.value || '').toLowerCase();
        const citySearch = (searchCityInput && searchCityInput.value || '').toLowerCase();
        const type = (typeFilter && typeFilter.value) || '';

        const cards = document.querySelectorAll('.address-card');
        cards.forEach(card => {
            const name = (card.querySelector('.customer-name') && card.querySelector('.customer-name')
                .textContent.toLowerCase()) || '';
            let city = '';
            const rows = card.querySelectorAll('.address-row');
            rows.forEach(r => {
                const lbl = (r.querySelector('.address-label') && r.querySelector(
                    '.address-label').textContent.trim());
                if (lbl && lbl.startsWith('المدينة')) {
                    city = (r.querySelector('.address-value') && r.querySelector(
                        '.address-value').textContent.trim()) || '';
                }
            });

            let show = true;
            if (searchTerm && !name.includes(searchTerm)) show = false;
            if (citySearch && !city.toLowerCase().includes(citySearch)) show = false;
            if (type === 'default' && !card.classList.contains('default')) show = false;

            card.style.display = show ? 'block' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterAddresses);
    if (searchCityInput) searchCityInput.addEventListener('input', filterAddresses);
    if (typeFilter) typeFilter.addEventListener('change', filterAddresses);
})();

// Delete confirmation
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function() {
        if (confirm('هل أنت متأكد من حذف هذا العنوان؟')) {
            // Add delete logic here
            alert('تم حذف العنوان بنجاح');
        }
    });
});

// ---------- Modal logic ----------
const modalOverlay = document.getElementById('modalOverlay');
const modalCloseBtn = document.getElementById('modalCloseBtn');
const modalCloseFooter = document.getElementById('modalCloseFooter');
const modalEditBtn = document.getElementById('modalEditBtn');

const modalCustomerName = document.getElementById('modalCustomerName');
const modalCustomerPhone = document.getElementById('modalCustomerPhone');
const modalCity = document.getElementById('modalCity');
const modalArea = document.getElementById('modalArea');
const modalStreet = document.getElementById('modalStreet');
const modalNotes = document.getElementById('modalNotes');

function openModalFromCard(card) {
    const nameEl = card.querySelector('.customer-name');
    const phoneEl = card.querySelector('.customer-phone');

    modalCustomerName.textContent = nameEl ? nameEl.textContent.trim() : '-';
    modalCustomerPhone.textContent = phoneEl ? phoneEl.textContent.replace('📱', '').trim() : '-';

    const rows = card.querySelectorAll('.address-row');
    let city = '-',
        area = '-',
        street = '-',
        notes = '-';
    rows.forEach(r => {
        const labelEl = r.querySelector('.address-label');
        const valueEl = r.querySelector('.address-value');
        if (!labelEl || !valueEl) return;
        const labelText = labelEl.textContent.trim();
        const valText = valueEl.textContent.trim();
        if (labelText.startsWith('المدينة')) city = valText || '-';
        else if (labelText.startsWith('الحي')) area = valText || '-';
        else if (labelText.startsWith('الشارع')) street = valText || '-';
        else if (labelText.startsWith('ملاحظات')) notes = valText || '-';
    });

    modalCity.textContent = city;
    modalArea.textContent = area;
    modalStreet.textContent = street;
    modalNotes.textContent = notes;

    const editBtn = card.querySelector('.btn-edit');
    if (editBtn && editBtn.dataset && editBtn.dataset.editUrl) {
        modalEditBtn.style.display = 'inline-block';
        modalEditBtn.onclick = () => {
            window.location.href = editBtn.dataset.editUrl;
        };
    } else {
        modalEditBtn.style.display = 'none';
        modalEditBtn.onclick = null;
    }

    modalOverlay.style.display = 'flex';
    modalOverlay.setAttribute('aria-hidden', 'false');
    modalCloseBtn.focus();
}

document.querySelectorAll('.btn-view').forEach(btn => {
    btn.addEventListener('click', function(e) {
        const card = e.currentTarget.closest('.address-card');
        if (!card) return;
        openModalFromCard(card);
    });
});

function closeModal() {
    modalOverlay.style.display = 'none';
    modalOverlay.setAttribute('aria-hidden', 'true');
}

modalCloseBtn.addEventListener('click', closeModal);
modalCloseFooter.addEventListener('click', closeModal);

modalOverlay.addEventListener('click', function(e) {
    if (e.target === modalOverlay) closeModal();
});

window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modalOverlay.style.display === 'flex') {
        closeModal();
    }
});
// ---------- end modal logic ----------
```

---

## 📂 مكان حفظ الملفات:
```
public/css/addresses-styles.css
public/js/addresses-scripts.js
