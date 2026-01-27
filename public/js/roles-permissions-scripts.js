// ==================== Filter Functions ====================

document.getElementById('searchInput')?.addEventListener('input', filterItems);
document.getElementById('guardFilter')?.addEventListener('change', filterItems);

function filterItems() {
    const searchTerm = (document.getElementById('searchInput')?.value || '').toLowerCase();
    const guardFilter = document.getElementById('guardFilter')?.value || '';

    const cards = document.querySelectorAll('.role-card, .permissions-table tbody tr');

    cards.forEach(card => {
        const name = card.dataset.name || '';
        const guard = card.dataset.guard || '';

        let show = true;

        // Filter by search
        if (searchTerm && !name.includes(searchTerm)) {
            show = false;
        }

        // Filter by guard
        if (guardFilter && guard !== guardFilter) {
            show = false;
        }

        card.style.display = show ? '' : 'none';
    });
}

// ==================== Role Delete Modal ====================

function confirmDelete(id, name) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteForm').action = `/admin/roles/${id}`;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// ==================== Permission Modals ====================

function openAddModal() {
    document.getElementById('addModal').style.display = 'block';
}

function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
}

function confirmDeletePermission(id, name) {
    document.getElementById('deletePermissionName').textContent = name;
    document.getElementById('deletePermissionForm').action = `/admin/permissions/${id}`;
    document.getElementById('deletePermissionModal').style.display = 'block';
}

function closeDeletePermissionModal() {
    document.getElementById('deletePermissionModal').style.display = 'none';
}

// ==================== Close Modals ====================

window.onclick = function(event) {
    const deleteModal = document.getElementById('deleteModal');
    const addModal = document.getElementById('addModal');
    const deletePermissionModal = document.getElementById('deletePermissionModal');

    if (event.target === deleteModal) {
        closeDeleteModal();
    }
    if (event.target === addModal) {
        closeAddModal();
    }
    if (event.target === deletePermissionModal) {
        closeDeletePermissionModal();
    }
};

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDeleteModal();
        closeAddModal();
        closeDeletePermissionModal();
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
    const cards = document.querySelectorAll('.role-card');
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

console.log('✅ Roles & Permissions JavaScript loaded successfully!');
