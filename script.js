// Updated script.js with API integration

// Login with API
const loginForm = document.getElementById("loginForm");
if (loginForm) {
    loginForm.addEventListener("submit", async e => {
        e.preventDefault();
        const reg = document.getElementById("regno").value;
        const pass = document.getElementById("password").value;
        const err = document.getElementById("loginError");

        if (!reg || !pass) {
            err.textContent = "All fields are required";
            return;
        }

        try {
            const response = await fetch('auth/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    reg_no: reg,
                    password: pass
                })
            });

            const data = await response.json();
            
            if (data.success) {
                window.location.href = "home.php";
            } else {
                err.textContent = data.message;
            }
        } catch (error) {
            err.textContent = "Network error. Please try again.";
        }
    });
}

// Registration with API
const registerForm = document.getElementById("registerForm");
if (registerForm) {
    registerForm.addEventListener("submit", async e => {
        e.preventDefault();
        
        const reg = document.getElementById("regno").value;
        const pass = document.getElementById("pass").value;
        const cpass = document.getElementById("cpass").value;
        const name = document.getElementById("name").value;
        const email = document.getElementById("email").value;
        const room = document.getElementById("room").value;
        const err = document.getElementById("regError");

        if (!reg || !pass || !cpass || !name || !email) {
            err.textContent = "All fields are required";
            return;
        }

        if (pass !== cpass) {
            err.textContent = "Passwords do not match";
            return;
        }

        try {
            const response = await fetch('auth/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    reg_no: reg,
                    password: pass,
                    cpass: cpass,
                    full_name: name,
                    email: email,
                    room: room
                })
            });

            const data = await response.json();
            
            if (data.success) {
                alert("Registration successful!");
                window.location.href = data.redirect;
            } else {
                err.textContent = data.message;
            }
        } catch (error) {
            err.textContent = "Network error. Please try again.";
        }
    });
}

// Load dashboard data
async function loadDashboardData() {
    try {
        const response = await fetch('api/dashboard.php');
        const data = await response.json();
        
        if (data.success) {
            // Update dashboard cards
            document.querySelector('.card:nth-child(1) strong').textContent = data.data.total_students;
            document.querySelector('.card:nth-child(2) strong').textContent = data.data.total_rooms;
            document.querySelector('.card:nth-child(3) strong').textContent = data.data.pending_complaints;
            document.querySelector('.card:nth-child(4) strong').textContent = 'Rs' + data.data.pending_payments;
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
}

// Load student data
async function loadStudentData() {
    try {
        const response = await fetch('api/students.php');
        const data = await response.json();
        
        if (data.success) {
            const table = document.querySelector('#students table');
            // Clear existing rows except header
            while (table.rows.length > 1) {
                table.deleteRow(1);
            }
            
            // Add new rows
            data.data.forEach(student => {
                const row = table.insertRow();
                row.innerHTML = `
                    <td>${student.reg_no}</td>
                    <td>${student.full_name}</td>
                    <td>${student.room_number}</td>
                    <td>${student.email|| 'N/A'}</td>
                `;
            });
        }
    } catch (error) {
        console.error('Error loading student data:', error);
    }
}

function showSection(id) {
    document.querySelectorAll(".section").forEach(s => s.classList.remove("active"));
    document.getElementById(id).classList.add("active");
    
    // Load data for the active section
    switch(id) {
        case 'dashboard':
            loadDashboardData();
            break;
        case 'students':
            loadStudentData();
            break;
        case 'rooms':
            loadRoomsData();
            break;
        case 'complaints':
            loadComplaintsData();
            break;
        case 'payment':
            loadPaymentData();
            break;
    }
}

function logout() {
    window.location.href = "auth/logout.php";
}

// Load dashboard data on page load
if (window.location.pathname.includes('home.php') || window.location.pathname.includes('home.html')) {
    document.addEventListener('DOMContentLoaded', loadDashboardData);
}
// Complaint Functions
function showComplaintForm() {
    document.getElementById('complaintForm').style.display = 'block';
    document.getElementById('newComplaintForm').reset();
    document.getElementById('complaintError').textContent = '';
}

function hideComplaintForm() {
    document.getElementById('complaintForm').style.display = 'none';
}

// Load complaints data
async function loadComplaintsData() {
    try {
        const response = await fetch('api/complaints.php');
        const data = await response.json();
        
        const complaintsList = document.getElementById('complaintsList');
        
        if (data.success && data.data.length > 0) {
            let html = `
                <div style="display: grid; gap: 15px;">
                    ${data.data.map(complaint => `
                        <div style="background: rgba(255,255,255,0.05); 
                                    padding: 15px; 
                                    border-radius: 10px;
                                    border-left: 4px solid ${getStatusColor(complaint.status)};">
                            <div style="display: flex; justify-content: space-between;">
                                <div>
                                    <h4 style="margin: 0 0 5px 0;">${complaint.complaint_type}</h4>
                                    <p style="margin: 0; opacity: 0.8;">${complaint.description}</p>
                                </div>
                                <div style="text-align: right;">
                                    <span style="padding: 4px 10px; 
                                                 border-radius: 15px; 
                                                 font-size: 12px;
                                                 background: ${getStatusColor(complaint.status, true)};
                                                 color: ${complaint.status === 'resolved' ? '#000' : '#fff'};">
                                        ${complaint.status}
                                    </span>
                                    <p style="margin: 5px 0 0 0; font-size: 12px; opacity: 0.7;">
                                        ${formatDate(complaint.created_at)}
                                    </p>
                                </div>
                            </div>
                            ${complaint.resolved_at ? `
                                <div style="margin-top: 10px; padding-top: 10px; 
                                            border-top: 1px solid rgba(255,255,255,0.1); 
                                            font-size: 14px;">
                                    <strong>Resolved on:</strong> ${formatDate(complaint.resolved_at)}
                                </div>
                            ` : ''}
                        </div>
                    `).join('')}
                </div>
            `;
            
            complaintsList.innerHTML = html;
        } else if (data.success && data.data.length === 0) {
            complaintsList.innerHTML = `
                <div style="text-align: center; padding: 40px; color: rgba(255,255,255,0.5);">
                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px;"></i>
                    <h3>No Complaints</h3>
                    <p>You haven't submitted any complaints yet.</p>
                    <button class="action-btn" onclick="showComplaintForm()" 
                            style="background: var(--accent); color: #000; margin-top: 10px;">
                        Submit Your First Complaint
                    </button>
                </div>
            `;
        } else {
            complaintsList.innerHTML = `<p class="error">${data.message || 'Failed to load complaints'}</p>`;
        }
    } catch (error) {
        console.error('Error loading complaints:', error);
        document.getElementById('complaintsList').innerHTML = 
            `<p class="error">Error loading complaints. Please try again.</p>`;
    }
}

// Submit new complaint
document.addEventListener('DOMContentLoaded', function() {
    const complaintForm = document.getElementById('newComplaintForm');
    if (complaintForm) {
        complaintForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const type = document.getElementById('complaintType').value;
            const description = document.getElementById('complaintDesc').value.trim();
            const errorEl = document.getElementById('complaintError');
            
            if (!description) {
                errorEl.textContent = 'Please enter a description';
                return;
            }
            
            if (description.length < 10) {
                errorEl.textContent = 'Description must be at least 10 characters';
                return;
            }
            
            try {
                const response = await fetch('api/complaints.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        type: type,
                        description: description
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Complaint submitted successfully!');
                    hideComplaintForm();
                    loadComplaintsData(); // Refresh the list
                } else {
                    errorEl.textContent = data.message;
                }
            } catch (error) {
                errorEl.textContent = 'Network error. Please try again.';
            }
        });
    }
});

// Helper functions
function getStatusColor(status, light = false) {
    const colors = {
        'pending': light ? 'rgba(253, 203, 110, 0.2)' : '#fdcb6e',
        'in-progress': light ? 'rgba(0, 184, 148, 0.2)' : '#00b894',
        'resolved': light ? 'rgba(255, 255, 255, 0.3)' : '#4facfe'
    };
    return colors[status] || '#999';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function showSection(id) {
    document.querySelectorAll(".section").forEach(s => s.classList.remove("active"));
    document.getElementById(id).classList.add("active");
    
    // Update sidebar active state
    document.querySelectorAll(".sidebar li").forEach(li => li.classList.remove("active"));
    const activeLi = Array.from(document.querySelectorAll(".sidebar li")).find(li => 
        li.textContent.toLowerCase().includes(id.toLowerCase()) || 
        (id === 'dashboard' && li.textContent.toLowerCase() === 'dashboard')
    );
    if (activeLi) activeLi.classList.add("active");
    
    // Load data for the active section
    switch(id) {
        case 'dashboard':
            loadDashboardData();
            break;
        case 'students':
            loadStudentData();
            break;
        case 'rooms':
            loadRoomsData();
            break;
        case 'complaints':
            loadComplaintsData();
            break;
        case 'payment':
            loadPaymentData();
            break;
    }
}

// Add this at the beginning of script.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing complaint system');
    
    // Check if we're on home.php
    if (window.location.pathname.includes('home.php') || window.location.pathname.includes('home.html')) {
        // Initialize complaint form
        const complaintForm = document.getElementById('newComplaintForm');
        if (complaintForm) {
            console.log('Found complaint form');
            complaintForm.addEventListener('submit', handleComplaintSubmit);
        }
        
        // Initialize complaint button
        const newComplaintBtn = document.querySelector('button[onclick="showComplaintForm()"]');
        if (newComplaintBtn) {
            console.log('Found complaint button');
            // Remove onclick and use event listener
            newComplaintBtn.removeAttribute('onclick');
            newComplaintBtn.addEventListener('click', showComplaintForm);
        }
    }
});

// Global function declarations
function showComplaintForm() {
    console.log('showComplaintForm called');
    const form = document.getElementById('complaintForm');
    if (form) {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
        document.getElementById('newComplaintForm').reset();
        document.getElementById('complaintError').textContent = '';
    } else {
        console.error('Complaint form not found');
    }
}

function hideComplaintForm() {
    console.log('hideComplaintForm called');
    const form = document.getElementById('complaintForm');
    if (form) {
        form.style.display = 'none';
    }
}

async function handleComplaintSubmit(e) {
    e.preventDefault();
    console.log('Complaint form submitted');
    
    const type = document.getElementById('complaintType').value;
    const description = document.getElementById('complaintDesc').value.trim();
    const errorEl = document.getElementById('complaintError');
    
    if (!description) {
        errorEl.textContent = 'Please enter a description';
        return;
    }
    
    if (description.length < 10) {
        errorEl.textContent = 'Description must be at least 10 characters';
        return;
    }
    
    try {
        console.log('Submitting complaint:', { type, description });
        const response = await fetch('api/complaints.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                type: type,
                description: description
            })
        });
        
        const data = await response.json();
        console.log('Response:', data);
        
        if (data.success) {
            alert('Complaint submitted successfully!');
            hideComplaintForm();
            loadComplaintsData();
        } else {
            errorEl.textContent = data.message || 'Failed to submit complaint';
        }
    } catch (error) {
        console.error('Error submitting complaint:', error);
        errorEl.textContent = 'Network error. Please try again.';
    }
}

// Admin Login
const adminLoginForm = document.getElementById("adminLoginForm");
if (adminLoginForm) {
    adminLoginForm.addEventListener("submit", async e => {
        e.preventDefault();
        const username = document.getElementById("username").value;
        const password = document.getElementById("password").value;
        const err = document.getElementById("loginError");

        if (!username || !password) {
            err.textContent = "All fields are required";
            return;
        }

        try {
            const response = await fetch('auth/admin_login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    username: username,
                    password: password
                })
            });

            const data = await response.json();
            
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                err.textContent = data.message;
            }
        } catch (error) {
            err.textContent = "Network error. Please try again.";
        }
    });
}