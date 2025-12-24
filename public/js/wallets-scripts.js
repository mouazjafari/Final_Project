// ==================== Filter Functions ====================

document.getElementById('searchInput')?.addEventListener('input', filterWallets);
document.getElementById('balanceFilter')?.addEventListener('change', filterWallets);

function filterWallets() {
    const searchTerm = (document.getElementById('searchInput')?.value || '').toLowerCase();
    const balanceFilter = document.getElementById('balanceFilter')?.value || '';

    const walletCards = document.querySelectorAll('.wallet-card');

    walletCards.forEach(card => {
        const userName = card.dataset.userName || '';
        const balance = parseFloat(card.dataset.balance) || 0;

        let show = true;

        // فلتر البحث
        if (searchTerm && !userName.includes(searchTerm)) {
            show = false;
        }

        // فلتر الرصيد
        if (balanceFilter) {
            if (balanceFilter === 'zero' && balance !== 0) {
                show = false;
            } else if (balanceFilter === 'positive' && balance <= 0) {
                show = false;
            } else if (balanceFilter === 'high' && balance <= 500) {
                show = false;
            }
        }

        card.style.display = show ? 'block' : 'none';
    });
}

// ==================== Modal Functions ====================

function openAddModal() {
    document.getElementById('addModal').style.display = 'block';
}

function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
}

function openWithdrawModal() {
    document.getElementById('withdrawModal').style.display = 'block';
}

function closeWithdrawModal() {
    document.getElementById('withdrawModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const addModal = document.getElementById('addModal');
    const withdrawModal = document.getElementById('withdrawModal');

    if (event.target === addModal) {
        closeAddModal();
    }

    if (event.target === withdrawModal) {
        closeWithdrawModal();
    }
};

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAddModal();
        closeWithdrawModal();
    }
});

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
    const cards = document.querySelectorAll('.wallet-card');
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
