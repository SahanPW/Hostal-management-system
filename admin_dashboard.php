<?php
// admin_dashboard.php
require_once 'config/database.php';
require_once 'config/session.php';

$session = new SessionManager();

// Check if admin is logged in (you'll need to modify your session to track admin vs student)
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.html");
    exit;
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - UniNest</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="glass-bg1">
<div class="layout">
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li class="active" onclick="showSection('dashboard')">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </li>
            <li onclick="showSection('students')">
                <i class="fas fa-users"></i> Manage Students
            </li>
            <li onclick="showSection('rooms')">
                <i class="fas fa-bed"></i> Manage Rooms
            </li>
            <li onclick="showSection('complaints')">
                <i class="fas fa-exclamation-circle"></i> Manage Complaints
            </li>
            <li onclick="showSection('payments')">
                <i class="fas fa-credit-card"></i> Manage Payments
            </li>
            <li onclick="adminLogout()">
                <i class="fas fa-sign-out-alt"></i> Logout
            </li>
        </ul>
        
        <div class="logo-container">
            <img src="images/lo.png" class="logo">
            <h5>University of Vavuniya</h5>
            <p>Welcome, <?php echo htmlspecialchars($admin_username); ?></p>
            <p style="color: var(--accent); font-size: 12px;">Administrator</p>
        </div>
    </aside>

    <main class="content">
        <!-- Dashboard Section -->
        <section id="dashboard" class="section active">
            <h1>Admin Dashboard</h1>
            <div class="cards">
                <div class="card" onclick="showSection('students')">
                    <i class="fas fa-users fa-3x" style="margin-bottom: 15px; color: var(--accent);"></i>
                    <h3>Total Students</h3>
                    <p id="totalStudents" style="font-size: 32px; font-weight: bold;">0</p>
                </div>
                <div class="card" onclick="showSection('rooms')">
                    <i class="fas fa-bed fa-3x" style="margin-bottom: 15px; color: var(--accent);"></i>
                    <h3>Total Rooms</h3>
                    <p id="totalRooms" style="font-size: 32px; font-weight: bold;">0</p>
                </div>
                <div class="card" onclick="showSection('complaints')">
                    <i class="fas fa-exclamation-circle fa-3x" style="margin-bottom: 15px; color: var(--accent);"></i>
                    <h3>Pending Complaints</h3>
                    <p id="pendingComplaints" style="font-size: 32px; font-weight: bold;">0</p>
                </div>
                <div class="card" onclick="showSection('payments')">
                    <i class="fas fa-credit-card fa-3x" style="margin-bottom: 15px; color: var(--accent);"></i>
                    <h3>Pending Payments</h3>
                    <p id="pendingPayments" style="font-size: 32px; font-weight: bold;">Rs 0</p>
                </div>
            </div>
        </section>

        <!-- Manage Students Section -->
        <section id="students" class="section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1>Manage Students</h1>
                <button class="action-btn" onclick="showAddStudentForm()" 
                        style="background: var(--accent); color: #000; padding: 10px 20px;">
                    <i class="fas fa-plus"></i> Add Student
                </button>
            </div>

            <!-- Students Table -->
            <table>
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Room</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="studentsTable">
                    <tr><td colspan="6" style="text-align: center;">Loading students...</td></tr>
                </tbody>
            </table>
        </section>

        <!-- Manage Rooms Section -->
        <section id="rooms" class="section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1>Manage Rooms</h1>
                <button class="action-btn" onclick="showAddRoomForm()" 
                        style="background: var(--accent); color: #000; padding: 10px 20px;">
                    <i class="fas fa-plus"></i> Add Room
                </button>
            </div>

            <!-- Rooms Table -->
            <table>
                <thead>
                    <tr>
                        <th>Room No</th>
                        <th>Floor</th>
                        <th>Block</th>
                        <th>Capacity</th>
                        <th>Occupied</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="roomsTable">
                    <tr><td colspan="7" style="text-align: center;">Loading rooms...</td></tr>
                </tbody>
            </table>
        </section>

        <!-- Manage Complaints Section -->
        <section id="complaints" class="section">
            <h1>Manage Complaints</h1>
            
            <!-- Status Filter -->
            <div class="floor-filter" style="margin-bottom: 20px;">
                <button class="filter-btn active" onclick="filterComplaints('all')">All</button>
                <button class="filter-btn" onclick="filterComplaints('pending')">Pending</button>
                <button class="filter-btn" onclick="filterComplaints('in-progress')">In Progress</button>
                <button class="filter-btn" onclick="filterComplaints('resolved')">Resolved</button>
            </div>

            <!-- Complaints Table -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="complaintsTable">
                    <tr><td colspan="7" style="text-align: center;">Loading complaints...</td></tr>
                </tbody>
            </table>
        </section>

        <!-- Manage Payments Section -->
        <section id="payments" class="section">
            <h1>Manage Payments</h1>
            
            <!-- Payments Table -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="paymentsTable">
                    <tr><td colspan="7" style="text-align: center;">Loading payments...</td></tr>
                </tbody>
            </table>
        </section>
    </main>
</div>

<!-- Add this JavaScript at the end -->
<script>
// Show/hide sections
function showSection(id) {
    // Hide all sections
    document.querySelectorAll(".section").forEach(s => s.classList.remove("active"));
    
    // Show selected section
    document.getElementById(id).classList.add("active");
    
    // Update sidebar active state
    document.querySelectorAll(".sidebar li").forEach(li => li.classList.remove("active"));
    event.target.closest('li').classList.add("active");
    
    // Load data for the active section
    switch(id) {
        case 'dashboard':
            loadAdminDashboardData();
            break;
        case 'students':
            loadAdminStudents();
            break;
        case 'rooms':
            loadAdminRooms();
            break;
        case 'complaints':
            loadAdminComplaints();
            break;
        case 'payments':
            loadAdminPayments();
            break;
    }
}

// Logout function
function adminLogout() {
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "auth/admin_logout.php";
    }
}

// Load admin dashboard data
async function loadAdminDashboardData() {
    try {
        const response = await fetch('api/admin_stats.php');
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('totalStudents').textContent = data.total_students || 0;
            document.getElementById('totalRooms').textContent = data.total_rooms || 0;
            document.getElementById('pendingComplaints').textContent = data.pending_complaints || 0;
            document.getElementById('pendingPayments').textContent = 'Rs ' + (data.pending_payments || 0);
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
}

// Load students
async function loadAdminStudents() {
    try {
        const response = await fetch('api/get_students.php');
        const data = await response.json();
        
        const table = document.getElementById('studentsTable');
        if (!table) return;
        
        if (data.success) {
            let html = '';
            data.data.forEach(student => {
                html += `
                    <tr>
                        <td>${student.reg_no}</td>
                        <td>${student.full_name}</td>
                        <td>${student.email}</td>
                        <td>${student.room_number || 'N/A'}</td>
                        <td>${student.phone || 'N/A'}</td>
                        <td>
                            <button class="action-btn" onclick="editStudent('${student.reg_no}')" 
                                    style="margin-right: 5px; background: #4facfe;">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="action-btn btn-danger" onclick="deleteStudent('${student.reg_no}')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                `;
            });
            table.innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading students:', error);
        document.getElementById('studentsTable').innerHTML = 
            '<tr><td colspan="6" style="text-align: center; color: var(--danger);">Error loading students</td></tr>';
    }
}

// Load rooms
async function loadAdminRooms() {
    try {
        const response = await fetch('api/get_rooms.php');
        const data = await response.json();
        
        const table = document.getElementById('roomsTable');
        if (!table) return;
        
        if (data.success) {
            let html = '';
            data.data.forEach(room => {
                html += `
                    <tr>
                        <td>${room.room_number}</td>
                        <td>${room.floor}</td>
                        <td>${room.hostel_block}</td>
                        <td>${room.capacity}</td>
                        <td>${room.occupied}</td>
                        <td>
                            <span class="status-${room.status}" style="padding: 4px 10px; border-radius: 15px;">
                                ${room.status}
                            </span>
                        </td>
                        <td>
                            <button class="action-btn" onclick="updateRoomStatus('${room.room_number}', 'available')" 
                                    style="background: #00b894; margin-right: 5px;">Available</button>
                            <button class="action-btn" onclick="updateRoomStatus('${room.room_number}', 'occupied')" 
                                    style="background: #e17055; margin-right: 5px;">Occupied</button>
                            <button class="action-btn" onclick="updateRoomStatus('${room.room_number}', 'maintenance')" 
                                    style="background: #fdcb6e;">Maintenance</button>
                        </td>
                    </tr>
                `;
            });
            table.innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading rooms:', error);
        document.getElementById('roomsTable').innerHTML = 
            '<tr><td colspan="7" style="text-align: center; color: var(--danger);">Error loading rooms</td></tr>';
    }
}

// Load complaints
async function loadAdminComplaints(filter = 'all') {
    try {
        const url = filter === 'all' ? 'api/get_complaints.php' : `api/get_complaints.php?status=${filter}`;
        const response = await fetch(url);
        const data = await response.json();
        
        const table = document.getElementById('complaintsTable');
        if (!table) return;
        
        if (data.success) {
            let html = '';
            data.data.forEach(complaint => {
                html += `
                    <tr>
                        <td>${complaint.id}</td>
                        <td>${complaint.student_reg_no}</td>
                        <td>${complaint.complaint_type}</td>
                        <td style="max-width: 200px;">${complaint.description.substring(0, 50)}...</td>
                        <td>${new Date(complaint.created_at).toLocaleDateString()}</td>
                        <td>
                            <span class="status-${complaint.status}">
                                ${complaint.status}
                            </span>
                        </td>
                        <td>
                            <button class="action-btn" onclick="updateComplaintStatus(${complaint.id}, 'in-progress')" 
                                    ${complaint.status !== 'pending' ? 'disabled' : ''} 
                                    style="margin-right: 5px; background: #fdcb6e;">
                                <i class="fas fa-play"></i> In Progress
                            </button>
                            <button class="action-btn" onclick="updateComplaintStatus(${complaint.id}, 'resolved')" 
                                    ${complaint.status === 'resolved' ? 'disabled' : ''} 
                                    style="background: #00b894;">
                                <i class="fas fa-check"></i> Resolve
                            </button>
                        </td>
                    </tr>
                `;
            });
            table.innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading complaints:', error);
        document.getElementById('complaintsTable').innerHTML = 
            '<tr><td colspan="7" style="text-align: center; color: var(--danger);">Error loading complaints</td></tr>';
    }
}

// Filter complaints
function filterComplaints(status) {
    // Update active button
    document.querySelectorAll('#complaints .filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    loadAdminComplaints(status);
}

// Update complaint status
async function updateComplaintStatus(complaintId, status) {
    if (!confirm(`Are you sure you want to mark this complaint as ${status}?`)) {
        return;
    }
    
    try {
        const response = await fetch('api/update_complaint.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                complaint_id: complaintId,
                status: status
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Complaint status updated successfully!');
            loadAdminComplaints(); // Refresh
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error updating complaint:', error);
        alert('Network error. Please try again.');
    }
}

// Update room status
async function updateRoomStatus(roomNumber, status) {
    try {
        const response = await fetch('api/update_room.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                room_number: roomNumber,
                status: status
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(`Room ${roomNumber} status updated to ${status}`);
            loadAdminRooms(); // Refresh
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error updating room:', error);
        alert('Network error. Please try again.');
    }
}

// Delete student
async function deleteStudent(regNo) {
    if (!confirm(`Are you sure you want to delete student ${regNo}? This action cannot be undone.`)) {
        return;
    }
    
    try {
        const response = await fetch('api/delete_student.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                reg_no: regNo
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Student deleted successfully!');
            loadAdminStudents(); // Refresh
            loadAdminDashboardData(); // Update dashboard
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error deleting student:', error);
        alert('Network error. Please try again.');
    }
}

// Load payments
async function loadAdminPayments() {
    try {
        const response = await fetch('api/get_payments.php');
        const data = await response.json();
        
        const table = document.getElementById('paymentsTable');
        if (!table) return;
        
        if (data.success) {
            let html = '';
            data.data.forEach(payment => {
                html += `
                    <tr>
                        <td>${payment.id}</td>
                        <td>${payment.student_reg_no}</td>
                        <td>Rs ${payment.amount}</td>
                        <td>${payment.payment_type}</td>
                        <td>${payment.due_date}</td>
                        <td>
                            <span class="status-${payment.status}">
                                ${payment.status}
                            </span>
                        </td>
                        <td>
                            <button class="action-btn" onclick="markPaymentPaid(${payment.id})" 
                                    ${payment.status === 'paid' ? 'disabled' : ''} 
                                    style="background: #00b894;">
                                <i class="fas fa-check"></i> Mark Paid
                            </button>
                        </td>
                    </tr>
                `;
            });
            table.innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading payments:', error);
        document.getElementById('paymentsTable').innerHTML = 
            '<tr><td colspan="7" style="text-align: center; color: var(--danger);">Error loading payments</td></tr>';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadAdminDashboardData();
    
    // Load data for current section
    const activeSection = document.querySelector('.section.active');
    if (activeSection && activeSection.id === 'students') {
        loadAdminStudents();
    }
});
</script>
</body>
</html>