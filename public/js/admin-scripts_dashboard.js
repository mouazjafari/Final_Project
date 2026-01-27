// Sidebar collapse/expand (same behaviour as addresses file)
const sidebar = document.getElementById('sidebar');
const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');

// Toggle collapse (desktop)
sidebarToggleBtn && sidebarToggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
});

// Mobile open/close
mobileSidebarToggle && mobileSidebarToggle.addEventListener('click', () => {
    // for small screens we use open class to slide in/out
    if (window.innerWidth <= 990) {
        sidebar.classList.toggle('open');
    } else {
        // on larger screens, just toggle collapsed
        sidebar.classList.toggle('collapsed');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    }
});

// restore state on load
window.addEventListener('DOMContentLoaded', () => {
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
    }
});

// Close mobile sidebar when clicking outside (optional)
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 990) {
        if (!sidebar.contains(e.target) && mobileSidebarToggle && !mobileSidebarToggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    }
});

// handle window resize: if moving to desktop ensure sidebar visible
window.addEventListener('resize', () => {
    if (window.innerWidth > 990) {
        sidebar.classList.remove('open');
    }
});
// ==================== Dropdown Toggle Function ====================
function toggleDropdown(element) {
    const parent = element.parentElement;
    const dropdownMenu = parent.querySelector('.dropdown-menu');
    const arrow = element.querySelector('.dropdown-arrow');

    // Close other dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu !== dropdownMenu) {
            menu.style.display = 'none';
            const otherToggle = menu.parentElement.querySelector('.dropdown-toggle');
            if (otherToggle) {
                otherToggle.classList.remove('open');
            }
        }
    });

    // Toggle current dropdown
    if (dropdownMenu.style.display === 'none' || !dropdownMenu.style.display) {
        dropdownMenu.style.display = 'block';
        element.classList.add('open');
    } else {
        dropdownMenu.style.display = 'none';
        element.classList.remove('open');
    }
}

// ==================== Keep dropdown open on page load if active ====================
window.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.style.display === 'block') {
            const toggle = menu.parentElement.querySelector('.dropdown-toggle');
            if (toggle) {
                toggle.classList.add('open');
            }
        }
    });
});

// // ==================== Sidebar collapse/expand (existing code) ====================
// const sidebar = document.getElementById('sidebar');
// const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
// const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');

// Toggle collapse (desktop)
sidebarToggleBtn && sidebarToggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));

    // Close all dropdowns when collapsed
    if (sidebar.classList.contains('collapsed')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
        document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            toggle.classList.remove('open');
        });
    }
});

// Mobile open/close
mobileSidebarToggle && mobileSidebarToggle.addEventListener('click', () => {
    if (window.innerWidth <= 990) {
        sidebar.classList.toggle('open');
    } else {
        sidebar.classList.toggle('collapsed');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    }
});

// Restore state on load
window.addEventListener('DOMContentLoaded', () => {
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
    }
});

// Close mobile sidebar when clicking outside
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 990) {
        if (!sidebar.contains(e.target) && mobileSidebarToggle && !mobileSidebarToggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    }
});

// Handle window resize
window.addEventListener('resize', () => {
    if (window.innerWidth > 990) {
        sidebar.classList.remove('open');
    }
});
