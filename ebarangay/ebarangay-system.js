/* ================================================
   Barangay Daniog - eBarangay System
   Complete JavaScript Functionality
   ================================================ */

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', function() {
    initializeSystem();
});

function initializeSystem() {
    setupNavigation();
    setupModals();
    setupForms();
    setupNotifications();
    setupMobileMenu();
    setupSearch();
    loadDashboardData();
    animateStatNumbers();
    setupCharts();
    loadResidentsData();
    loadClearancesData();

    console.log('✅ Barangay Daniog eBarangay System initialized successfully!');
}

// ===== NAVIGATION SYSTEM =====
function setupNavigation() {
    const navItems = document.querySelectorAll('.nav-item');

    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();

            // Remove active class from all items
            navItems.forEach(nav => nav.classList.remove('active'));

            // Add active class to clicked item
            this.classList.add('active');

            // Get section to display
            const section = this.getAttribute('data-section');
            showSection(section);

            // Update page title
            const sectionTitle = this.querySelector('span').textContent;
            document.getElementById('pageTitle').textContent = sectionTitle;

            // Close mobile menu if open
            closeMobileMenu();
        });
    });
}

function showSection(sectionName) {
    // Hide all sections
    const allSections = document.querySelectorAll('.content-section');
    allSections.forEach(section => {
        section.classList.remove('active');
    });

    // Show requested section
    const targetSection = document.getElementById(sectionName + '-section');
    if (targetSection) {
        targetSection.classList.add('active');

        // Load section-specific data
        loadSectionData(sectionName);
    }
}

function loadSectionData(section) {
    switch(section) {
        case 'residents':
            loadResidentsData();
            break;
        case 'clearances':
            loadClearancesData();
            break;
        case 'permits':
            loadPermitsData();
            break;
        case 'blotter':
            loadBlotterData();
            break;
        case 'complaints':
            loadComplaintsData();
            break;
        default:
            console.log('Section:', section);
    }
}

// ===== MODAL SYSTEM =====
function setupModals() {
    const overlay = document.getElementById('modalOverlay');

    if (overlay) {
        overlay.addEventListener('click', function() {
            closeModal();
        });
    }
}

function openModal(modalName, type = null) {
    const modal = document.getElementById(modalName + 'Modal');
    const overlay = document.getElementById('modalOverlay');

    if (modal && overlay) {
        modal.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Set modal type if specified
        if (type && modal.dataset) {
            modal.dataset.type = type;
        }
    }
}

function closeModal() {
    const modals = document.querySelectorAll('.modal');
    const overlay = document.getElementById('modalOverlay');

    modals.forEach(modal => {
        modal.classList.remove('active');
    });

    if (overlay) {
        overlay.classList.remove('active');
    }

    document.body.style.overflow = 'auto';

    // Reset forms
    const forms = document.querySelectorAll('.modal form');
    forms.forEach(form => form.reset());
}

// ===== FORM HANDLING =====
function setupForms() {
    // Add Resident Form
    const addResidentForm = document.getElementById('addResidentForm');
    if (addResidentForm) {
        addResidentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitResident();
        });
    }

    // Issue Clearance Form
    const issueClearanceForm = document.getElementById('issueClearanceForm');
    if (issueClearanceForm) {
        issueClearanceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitClearance();
        });
    }
}

function submitResident() {
    const form = document.getElementById('addResidentForm');
    const formData = new FormData(form);

    // Simulate API call
    showLoading('Saving resident information...');

    setTimeout(() => {
        hideLoading();
        showNotification('Resident added successfully!', 'success');
        closeModal();
        loadResidentsData();
    }, 1500);
}

function submitClearance() {
    const form = document.getElementById('issueClearanceForm');
    const formData = new FormData(form);

    showLoading('Generating clearance certificate...');

    setTimeout(() => {
        hideLoading();
        showNotification('Clearance issued successfully!', 'success');
        closeModal();
        loadClearancesData();
        // Optionally open print dialog
        // window.print();
    }, 1500);
}

// ===== DATA LOADING =====
function loadDashboardData() {
    // Simulate loading dashboard statistics
    console.log('Loading dashboard data...');
}

function loadResidentsData() {
    const residents = [
        {
            id: 'RES-001',
            name: 'Juan Dela Cruz',
            age: 35,
            gender: 'Male',
            address: 'Purok 1, Daniog',
            contact: '0912-345-6789',
            status: 'Active'
        },
        {
            id: 'RES-002',
            name: 'Maria Santos',
            age: 28,
            gender: 'Female',
            address: 'Purok 2, Daniog',
            contact: '0923-456-7890',
            status: 'Active'
        },
        {
            id: 'RES-003',
            name: 'Pedro Reyes',
            age: 42,
            gender: 'Male',
            address: 'Purok 3, Daniog',
            contact: '0934-567-8901',
            status: 'Active'
        },
        {
            id: 'RES-004',
            name: 'Ana Garcia',
            age: 31,
            gender: 'Female',
            address: 'Purok 1, Daniog',
            contact: '0945-678-9012',
            status: 'Active'
        },
        {
            id: 'RES-005',
            name: 'Carlos Mendoza',
            age: 55,
            gender: 'Male',
            address: 'Purok 2, Daniog',
            contact: '0956-789-0123',
            status: 'Active'
        }
    ];

    const tbody = document.getElementById('residentsTableBody');
    if (tbody) {
        tbody.innerHTML = residents.map(resident => `
            <tr>
                <td><strong>${resident.id}</strong></td>
                <td>${resident.name}</td>
                <td>${resident.age}</td>
                <td>${resident.gender}</td>
                <td>${resident.address}</td>
                <td>${resident.contact}</td>
                <td><span class="badge badge-success">${resident.status}</span></td>
                <td>
                    <button class="btn-icon" onclick="viewResident('${resident.id}')" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-icon" onclick="editResident('${resident.id}')" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon" onclick="deleteResident('${resident.id}')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }
}

function loadClearancesData() {
    const clearances = [
        {
            certNo: 'BC-2025-145',
            name: 'Juan Dela Cruz',
            type: 'Barangay Clearance',
            purpose: 'Employment Requirement',
            dateIssued: 'Nov 30, 2025',
            issuedBy: 'Admin User'
        },
        {
            certNo: 'CR-2025-089',
            name: 'Maria Santos',
            type: 'Certificate of Residency',
            purpose: 'School Requirements',
            dateIssued: 'Nov 29, 2025',
            issuedBy: 'Admin User'
        },
        {
            certNo: 'CI-2025-034',
            name: 'Pedro Reyes',
            type: 'Certificate of Indigency',
            purpose: 'Financial Assistance',
            dateIssued: 'Nov 28, 2025',
            issuedBy: 'Admin User'
        }
    ];

    const tbody = document.getElementById('clearancesTableBody');
    if (tbody) {
        tbody.innerHTML = clearances.map(cert => `
            <tr>
                <td><strong>${cert.certNo}</strong></td>
                <td>${cert.name}</td>
                <td>${cert.type}</td>
                <td>${cert.purpose}</td>
                <td>${cert.dateIssued}</td>
                <td>${cert.issuedBy}</td>
                <td>
                    <button class="btn-icon" onclick="printClearance('${cert.certNo}')" title="Print">
                        <i class="fas fa-print"></i>
                    </button>
                    <button class="btn-icon" onclick="downloadClearance('${cert.certNo}')" title="Download">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="btn-icon" onclick="viewClearance('${cert.certNo}')" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }
}

function loadPermitsData() {
    showNotification('Loading business permits...', 'info');
}

function loadBlotterData() {
    showNotification('Loading blotter records...', 'info');
}

function loadComplaintsData() {
    showNotification('Loading complaints...', 'info');
}

// ===== CRUD OPERATIONS =====
function viewResident(id) {
    showNotification(`Viewing resident details: ${id}`, 'info');
}

function editResident(id) {
    showNotification(`Opening edit form for: ${id}`, 'info');
    // You would populate the form with resident data here
    openModal('addResident');
}

function deleteResident(id) {
    if (confirm('Are you sure you want to delete this resident? This action cannot be undone.')) {
        showLoading('Deleting resident...');
        setTimeout(() => {
            hideLoading();
            showNotification(`Resident ${id} deleted successfully`, 'success');
            loadResidentsData();
        }, 1000);
    }
}

function printClearance(certNo) {
    showNotification(`Preparing to print: ${certNo}`, 'info');
    // Implement print functionality
    setTimeout(() => {
        window.print();
    }, 500);
}

function downloadClearance(certNo) {
    showNotification(`Downloading certificate: ${certNo}`, 'info');
    // Implement download functionality
}

function viewClearance(certNo) {
    showNotification(`Viewing certificate: ${certNo}`, 'info');
}

// ===== NOTIFICATION SYSTEM =====
function setupNotifications() {
    const notificationBtn = document.querySelector('.notifications');
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleNotifications();
        });
    }

    // Close notifications when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('notificationDropdown');
        if (dropdown && !e.target.closest('.notifications')) {
            dropdown.classList.remove('active');
        }
    });
}

function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    if (dropdown) {
        dropdown.classList.toggle('active');
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;

    const icons = {
        success: 'fa-check-circle',
        error: 'fa-times-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };

    const colors = {
        success: '#28a745',
        error: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    };

    notification.innerHTML = `
        <i class="fas ${icons[type]}"></i>
        <span>${message}</span>
    `;

    Object.assign(notification.style, {
        position: 'fixed',
        top: '100px',
        right: '20px',
        padding: '1rem 1.5rem',
        borderRadius: '8px',
        color: 'white',
        backgroundColor: colors[type],
        boxShadow: '0 4px 15px rgba(0,0,0,0.2)',
        zIndex: '3000',
        display: 'flex',
        alignItems: 'center',
        gap: '0.8rem',
        fontSize: '0.95rem',
        fontWeight: '500',
        animation: 'slideInRight 0.3s ease-out',
        minWidth: '300px'
    });

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// ===== LOADING INDICATOR =====
function showLoading(message = 'Loading...') {
    const loader = document.createElement('div');
    loader.id = 'loadingIndicator';
    loader.innerHTML = `
        <div style="background: rgba(0,0,0,0.7); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 4000; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(3px);">
            <div style="background: white; padding: 2rem 3rem; border-radius: 12px; text-align: center; box-shadow: 0 8px 30px rgba(0,0,0,0.3);">
                <div style="width: 50px; height: 50px; border: 4px solid #f3f3f3; border-top: 4px solid #1e3c72; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                <p style="color: #333; font-size: 1rem; font-weight: 600;">${message}</p>
            </div>
        </div>
    `;
    document.body.appendChild(loader);
}

function hideLoading() {
    const loader = document.getElementById('loadingIndicator');
    if (loader) {
        document.body.removeChild(loader);
    }
}

// ===== MOBILE MENU =====
function setupMobileMenu() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
}

function closeMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar && window.innerWidth <= 968) {
        sidebar.classList.remove('active');
    }
}

// ===== SEARCH FUNCTIONALITY =====
function setupSearch() {
    const searchInputs = document.querySelectorAll('#globalSearch, #residentSearch');

    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterTableData(searchTerm);
        });
    });
}

function filterTableData(searchTerm) {
    const rows = document.querySelectorAll('.data-table tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

// ===== ANIMATED STATISTICS =====
function animateStatNumbers() {
    const statNumbers = document.querySelectorAll('.stat-number');

    statNumbers.forEach(stat => {
        const target = parseInt(stat.getAttribute('data-target'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                stat.textContent = target.toLocaleString();
                clearInterval(timer);
            } else {
                stat.textContent = Math.floor(current).toLocaleString();
            }
        }, 16);
    });
}

// ===== CHARTS =====
function setupCharts() {
    const chartCanvas = document.getElementById('activityChart');

    if (chartCanvas && typeof Chart !== 'undefined') {
        const ctx = chartCanvas.getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'],
                datasets: [{
                    label: 'Clearances',
                    data: [120, 135, 142, 138, 145, 145],
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Permits',
                    data: [45, 52, 48, 55, 60, 58],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Blotters',
                    data: [8, 12, 10, 7, 9, 8],
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// ===== EXPORT FUNCTIONALITY =====
function exportData(type) {
    showNotification(`Exporting ${type} data to Excel...`, 'info');

    // Implement actual export functionality
    setTimeout(() => {
        showNotification(`${type} data exported successfully!`, 'success');
    }, 1500);
}

// ===== PROFILE MENU =====
function toggleProfileMenu() {
    showNotification('Profile menu coming soon!', 'info');
}

// ===== LOGOUT =====
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        showLoading('Logging out...');
        setTimeout(() => {
            hideLoading();
            showNotification('Logged out successfully!', 'success');
            // Redirect to login page
            // window.location.href = 'login.html';
        }, 1500);
    }
}

// ===== ANIMATIONS CSS =====
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .btn-icon {
        background: none;
        border: none;
        padding: 0.5rem;
        cursor: pointer;
        color: #1e3c72;
        font-size: 1rem;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .btn-icon:hover {
        background: #f8f9fa;
        color: #c94b4b;
        transform: scale(1.1);
    }
`;
document.head.appendChild(styleSheet);

// ===== UTILITY FUNCTIONS =====
function formatDate(date) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(date).toLocaleDateString('en-US', options);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount);
}

// ===== CONSOLE WELCOME MESSAGE =====
console.log('%c🏛️ Barangay Daniog Digital Governance System', 'color: #1e3c72; font-size: 20px; font-weight: bold;');
console.log('%c📍 San Jose, Camarines Sur', 'color: #c94b4b; font-size: 14px;');
console.log('%c✨ Empowering communities through digital transformation', 'color: #666; font-size: 12px;');
console.log('');
console.log('System Status: ✅ Online');
console.log('Version: 1.0.0');
console.log('Last Updated: November 30, 2025');