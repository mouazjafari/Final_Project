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
