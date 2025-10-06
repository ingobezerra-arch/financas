// Sidebar functionality with collapse support
console.log('Sidebar.js loaded');
let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
console.log('Initial sidebar state from localStorage:', sidebarCollapsed);

// Define toggleSidebar globally so it can be called from onclick
window.toggleSidebar = function() {
    console.log('toggleSidebar called, current state:', sidebarCollapsed);
    
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const toggleButton = document.getElementById('sidebar-toggle');
    
    // Check if elements exist
    if (!sidebar) {
        console.error('Sidebar element not found');
        return;
    }
    if (!mainContent) {
        console.error('Main content element not found');
        return;
    }
    if (!toggleButton) {
        console.error('Toggle button element not found');
        return;
    }
    
    // Check current state from DOM instead of variable
    const isCurrentlyCollapsed = sidebar.classList.contains('sidebar-collapsed');
    console.log('Current DOM state - collapsed:', isCurrentlyCollapsed);
    
    if (isCurrentlyCollapsed) {
        // Expand sidebar
        console.log('Expanding sidebar');
        sidebar.classList.remove('sidebar-collapsed');
        sidebar.style.width = '16rem';
        mainContent.classList.remove('main-content-collapsed');
        mainContent.style.marginLeft = '16rem';
        mainContent.style.width = 'calc(100% - 16rem)';
        sidebar.setAttribute('data-collapsed', 'false');
        
        // Update toggle button icon
        toggleButton.innerHTML = '<i class="fas fa-chevron-left"></i>';
        toggleButton.title = 'Recolher Menu';
        
        // Re-open active submenu if any
        const activeSublink = document.querySelector('.sidebar-sublink.active');
        if (activeSublink) {
            const sidebarGroup = activeSublink.closest('.sidebar-group');
            if (sidebarGroup) {
                sidebarGroup.classList.add('open');
            }
        }
        
        sidebarCollapsed = false;
    } else {
        // Collapse sidebar
        console.log('Collapsing sidebar');
        sidebar.classList.add('sidebar-collapsed');
        sidebar.style.width = '4rem';
        mainContent.classList.add('main-content-collapsed');
        mainContent.style.marginLeft = '4rem';
        mainContent.style.width = 'calc(100% - 4rem)';
        sidebar.setAttribute('data-collapsed', 'true');
        
        // Update toggle button icon
        toggleButton.innerHTML = '<i class="fas fa-chevron-right"></i>';
        toggleButton.title = 'Expandir Menu';
        
        // Close any open submenus when collapsing
        document.querySelectorAll('.sidebar-group').forEach(group => {
            group.classList.remove('open');
        });
        
        sidebarCollapsed = true;
    }
    
    // Save state to localStorage
    localStorage.setItem('sidebarCollapsed', sidebarCollapsed.toString());
    console.log('New state saved:', sidebarCollapsed);
    
    // Trigger window resize event to update any charts or responsive elements
    setTimeout(() => {
        window.dispatchEvent(new Event('resize'));
    }, 350);
};

// Sidebar submenu functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing sidebar...');
    
    // Add event listener to toggle button
    const toggleButton = document.getElementById('sidebar-toggle');
    if (toggleButton) {
        toggleButton.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Toggle button clicked');
            window.toggleSidebar();
        });
        console.log('Toggle button event listener added');
    } else {
        console.error('Toggle button not found');
    }
    
    const sidebarGroups = document.querySelectorAll('.sidebar-group-title');
    
    sidebarGroups.forEach(group => {
        group.addEventListener('click', function() {
            // Don't toggle submenus when sidebar is collapsed
            const sidebar = document.getElementById('sidebar');
            if (sidebar && sidebar.classList.contains('sidebar-collapsed')) {
                return;
            }
            
            const parent = this.parentElement;
            const submenu = parent.querySelector('.sidebar-submenu');
            
            // Close other submenus
            sidebarGroups.forEach(otherGroup => {
                if (otherGroup !== this) {
                    otherGroup.parentElement.classList.remove('open');
                }
            });
            
            // Toggle current submenu
            parent.classList.toggle('open');
        });
    });
    
    // Initialize sidebar state from localStorage
    initializeSidebarState();
    
    // Auto-open active submenu only if sidebar is not collapsed
    const activeSublink = document.querySelector('.sidebar-sublink.active');
    if (activeSublink && !sidebarCollapsed) {
        const sidebarGroup = activeSublink.closest('.sidebar-group');
        if (sidebarGroup) {
            sidebarGroup.classList.add('open');
        }
    }
    
    console.log('Sidebar initialization complete');
});

// Initialize sidebar state
function initializeSidebarState() {
    console.log('Initializing sidebar state, collapsed:', sidebarCollapsed);
    
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const toggleButton = document.getElementById('sidebar-toggle');
    
    if (!sidebar || !mainContent) {
        console.error('Required elements not found during initialization');
        return;
    }
    
    if (sidebarCollapsed) {
        sidebar.classList.add('sidebar-collapsed');
        sidebar.style.width = '4rem';
        mainContent.classList.add('main-content-collapsed');
        mainContent.style.marginLeft = '4rem';
        mainContent.style.width = 'calc(100% - 4rem)';
        sidebar.setAttribute('data-collapsed', 'true');
        
        if (toggleButton) {
            toggleButton.innerHTML = '<i class="fas fa-chevron-right"></i>';
            toggleButton.title = 'Expandir Menu';
        }
    } else {
        sidebar.classList.remove('sidebar-collapsed');
        sidebar.style.width = '16rem';
        mainContent.classList.remove('main-content-collapsed');
        mainContent.style.marginLeft = '16rem';
        mainContent.style.width = 'calc(100% - 16rem)';
        sidebar.setAttribute('data-collapsed', 'false');
        
        if (toggleButton) {
            toggleButton.innerHTML = '<i class="fas fa-chevron-left"></i>';
            toggleButton.title = 'Recolher Menu';
        }
    }
}

// Responsive sidebar - updated for collapse functionality
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    
    // On mobile, always hide sidebar
    if (window.innerWidth < 1024) {
        sidebar.style.transform = 'translateX(-100%)';
        mainContent.style.marginLeft = '0';
        mainContent.style.width = '100%';
    } else {
        // On desktop, restore sidebar state
        sidebar.style.transform = 'translateX(0)';
        if (sidebarCollapsed) {
            sidebar.style.width = '4rem';
            mainContent.style.marginLeft = '4rem';
            mainContent.style.width = 'calc(100% - 4rem)';
        } else {
            sidebar.style.width = '16rem';
            mainContent.style.marginLeft = '16rem';
            mainContent.style.width = 'calc(100% - 16rem)';
        }
    }
});

// Initialize responsive behavior
document.addEventListener('DOMContentLoaded', function() {
    // Apply mobile styles if needed
    if (window.innerWidth < 1024) {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        
        sidebar.style.transform = 'translateX(-100%)';
        mainContent.style.marginLeft = '0';
        mainContent.style.width = '100%';
    }
});

// Handle mobile sidebar toggle
function toggleMobileSidebar() {
    if (window.innerWidth < 1024) {
        const sidebar = document.getElementById('sidebar');
        const currentTransform = sidebar.style.transform;
        
        if (currentTransform === 'translateX(-100%)' || !currentTransform) {
            sidebar.style.transform = 'translateX(0)';
        } else {
            sidebar.style.transform = 'translateX(-100%)';
        }
    }
}