// ==================== Filter Functions ====================

document.getElementById('searchInput')?.addEventListener('input', filterCoupons);
document.getElementById('typeFilter')?.addEventListener('change', filterCoupons);
document.getElementById('statusFilter')?.addEventListener('change', filterCoupons);

function filterCoupons() {
    const searchTerm = (document.getElementById('searchInput')?.value || '').toLowerCase();
    const typeFilter = document.getElementById('typeFilter')?.value || '';
    const statusFilter = document.getElementById('statusFilter')?.value || '';

    const cards = document.querySelectorAll('.coupon-card');

    cards.forEach(card => {
        const code = card.dataset.code || '';
        const type = card.dataset.type || '';
        const status = card.dataset.status || '';

        let show = true;

        // Filter by search
        if (searchTerm && !code.includes(searchTerm)) {
            show = false;
        }

        // Filter by type
        if (typeFilter && type !== typeFilter) {
            show = false;
        }

        // Filter by status
        if (statusFilter && status !== statusFilter) {
            show = false;
        }

        card.style.display = show ? 'block' : 'none';
    });
}

// ==================== Toggle Status ====================

async function toggleStatus(couponId) {
    if (!confirm('هل أنت متأكد من تغيير حالة الكوبون؟')) {
        return;
    }

    try {
        const response = await fetch(`/admin/coupons/${couponId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });

        const data = await response.json();

        if (data.success) {
            showSuccessMessage(data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showErrorMessage(data.message || 'حدث خطأ أثناء التحديث');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorMessage('حدث خطأ في الاتصال بالخادم');
    }
}

// ==================== Delete Modal ====================

function confirmDelete(id, code) {
    document.getElementById('deleteItemName').textContent = code;
    document.getElementById('deleteForm').action = `/admin/coupons/${id}`;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        closeDeleteModal();
    }
};

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDeleteModal();
    }
});

// ==================== Success/Error Messages ====================

function showSuccessMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success';
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '99999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = message;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

function showErrorMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-error';
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '99999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = message;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}

// ==================== Auto-hide Alerts ====================

window.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.querySelector('.alert-success');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            setTimeout(() => successMessage.remove(), 300);
        }, 3000);
    }

    const errorMessage = document.querySelector('.alert-error');
    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.opacity = '0';
            setTimeout(() => errorMessage.remove(), 300);
        }, 5000);
    }
});

// ==================== Card Animations ====================

window.addEventListener('load', function() {
    const cards = document.querySelectorAll('.coupon-card');
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

console.log('✅ Coupons JavaScript loaded successfully!');
