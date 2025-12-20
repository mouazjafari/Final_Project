// Search functionality
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    filterCards();
});

// Type filter functionality
document.getElementById('typeFilter')?.addEventListener('change', function(e) {
    filterCards();
});

function filterCards() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const typeFilter = document.getElementById('typeFilter').value;
    const cards = document.querySelectorAll('.address-card');

    cards.forEach(card => {
        const customerName = card.querySelector('.customer-name')?.textContent.toLowerCase() || '';
        const customerPhone = card.querySelector('.customer-phone')?.textContent.toLowerCase() || '';
        const cardType = card.getAttribute('data-type');

        const matchesSearch = customerName.includes(searchTerm) || customerPhone.includes(searchTerm);
        const matchesType = !typeFilter || cardType === typeFilter;

        if (matchesSearch && matchesType) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Modal Functions
function openAddModal() {
    document.getElementById('modalTitle').textContent = '➕ إضافة خيار تصميم جديد';
    document.getElementById('optionForm').reset();
    document.getElementById('optionId').value = '';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('optionForm').action = '/admin/design-options';
    document.getElementById('optionModal').style.display = 'block';
}

function openEditModal(id, nameAr, nameEn, type) {
    document.getElementById('modalTitle').textContent = '✏️ تعديل خيار التصميم';
    document.getElementById('optionId').value = id;
    document.getElementById('nameAr').value = nameAr;
    document.getElementById('nameEn').value = nameEn;
    document.getElementById('typeSelect').value = type;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('optionForm').action = '/admin/design-options/' + id;
    document.getElementById('optionModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('optionModal').style.display = 'none';
}

function confirmDelete(id, name) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteForm').action = '/admin/design-options/' + id;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const optionModal = document.getElementById('optionModal');
    const deleteModal = document.getElementById('deleteModal');

    if (event.target == optionModal) {
        closeModal();
    }
    if (event.target == deleteModal) {
        closeDeleteModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
        closeDeleteModal();
    }
});

// Form validation
document.getElementById('optionForm')?.addEventListener('submit', function(e) {
    const nameAr = document.getElementById('nameAr').value.trim();
    const nameEn = document.getElementById('nameEn').value.trim();
    const type = document.getElementById('typeSelect').value;

    if (!nameAr || !nameEn || !type) {
        e.preventDefault();
        alert('⚠️ الرجاء ملء جميع الحقول المطلوبة');
        return false;
    }
});

// Success/Error messages (if you're using session flash messages)
window.addEventListener('DOMContentLoaded', function() {
    // Check for success message
    const successMessage = document.querySelector('.alert-success');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            setTimeout(() => successMessage.remove(), 300);
        }, 3000);
    }

    // Check for error message
    const errorMessage = document.querySelector('.alert-error');
    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.opacity = '0';
            setTimeout(() => errorMessage.remove(), 300);
        }, 5000);
    }
});

// Smooth animations for card appearance
window.addEventListener('load', function() {
    const cards = document.querySelectorAll('.address-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 50);
        }, index * 50);
    });
});
