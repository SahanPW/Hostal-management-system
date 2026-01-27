// admin_script.js - Admin-specific JavaScript

// Admin login
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

// Admin logout
function adminLogout() {
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "auth/admin_logout.php";
    }
}

// Load admin dashboard data
async function loadAdminDashboardData() {
    try {
        const response = await fetch('api/admin_dashboard.php');
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('totalStudents').textContent = data.data.total_students;
            document.getElementById('totalRooms').textContent = data.data.total_rooms;
            document.getElementById('pendingComplaints').textContent = data.data.pending_complaints;
            document.getElementById('pendingPayments').textContent = 'Rs ' + data.data.pending_payments;
        }
    } catch (error) {
        console.error('Error loading admin dashboard data:', error);
    }
}

// Load students for admin
async function loadAdminStudents() {
    try {
        const response = await fetch('api/admin_students.php');
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
                        <td>${student.faculty || 'N/A'}</td>
                        <td>
                            <button class="action-btn" onclick="editStudent('${student.reg_no}')" style="margin-right: 5px;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn btn-danger" onclick="deleteStudent('${student.reg_no}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            table.innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading students:', error);
    }
}

// Load rooms for admin
async function loadAdminRooms() {
    try {
        const response = await fetch('api/admin_rooms.php');
        const data = await response.json();
        
        const table = document.getElementById('roomsTable');
        if (!table) return;
        
        if (data.success) {
            let html = '';
            data.data.forEach(room => {
                const vacant = room.capacity - room.occupied;
                html += `
                    <tr>
                        <td>${room.room_number}</td>
                        <td>${room.floor}</td>
                        <td>${room.hostel_block}</td>
                        <td>${room.capacity}</td>
                        <td>${room.occupied}</td>
                        <td>${vacant}</td>
                        <td>
                            <span class="status-${room.status}">${room.status}</span>
                        </td>
                        <td>
                            <button class="action-btn" onclick="editRoom('${room.room_number}')" style="margin-right: 5px;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn" onclick="updateRoomStatus('${room.room_number}', 'available')" 
                                    style="background: #00b894; margin-right: 5px;">A</button>
                            <button class="action-btn" onclick="updateRoomStatus('${room.room_number}', 'occupied')" 
                                    style="background: #e17055; margin-right: 5px;">O</button>
                            <button class="action-btn" onclick="updateRoomStatus('${room.room_number}', 'maintenance')" 
                                    style="background: #fdcb6e;">M</button>
                        </td>
                    </tr>
                `;
            });
            table.innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading rooms:', error);
    }
}

// Load complaints for admin
async function loadAdminComplaints(filter = 'all') {
    try {
        const response = await fetch(`api/admin_complaints.php?status=${filter}`);
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
                        <td style="max-width: 300px;">${complaint.description.substring(0, 100)}...</td>
                        <td>${new Date(complaint.created_at).toLocaleDateString()}</td>
                        <td>
                            <span class="status-${complaint.status}">${complaint.status}</span>
                        </td>
                        <td>
                            <button class="action-btn" onclick="viewComplaint(${complaint.id})" style="margin-right: 5px;">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn" onclick="updateComplaintStatus(${complaint.id}, 'in-progress')" 
                                    ${complaint.status !== 'pending' ? 'disabled' : ''} style="margin-right: 5px;">
                                <i class="fas fa-play"></i>
                            </button>
                            <button class="action-btn" onclick="updateComplaintStatus(${complaint.id}, 'resolved')" 
                                    ${complaint.status === 'resolved' ? 'disabled' : ''}>
                                <i class="fas fa-check"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            table.innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading complaints:', error);
    }
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
            loadAdminComplaints(); // Refresh the list
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
            loadAdminRooms(); // Refresh the list
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
            loadAdminStudents(); // Refresh the list
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error deleting student:', error);
        alert('Network error. Please try again.');
    }
}

// Add new student
function showAddStudentForm() {
    document.getElementById('addStudentForm').style.display = 'block';
}

function hideAddStudentForm() {
    document.getElementById('addStudentForm').style.display = 'none';
    document.getElementById('newStudentForm').reset();
}

// Add new student form submission
const newStudentForm = document.getElementById('newStudentForm');
if (newStudentForm) {
    newStudentForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            reg_no: document.getElementById('studentRegNo').value,
            full_name: document.getElementById('studentName').value,
            email: document.getElementById('studentEmail').value,
            phone: document.getElementById('studentPhone').value,
            room_number: document.getElementById('studentRoom').value,
            faculty: document.getElementById('studentFaculty').value,
            password: document.getElementById('studentPassword').value,
            confirm_password: document.getElementById('studentConfirmPassword').value
        };
        
        const errorEl = document.getElementById('studentFormError');
        
        // Validation
        if (formData.password !== formData.confirm_password) {
            errorEl.textContent = 'Passwords do not match';
            return;
        }
        
        try {
            const response = await fetch('api/add_student.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('Student added successfully!');
                hideAddStudentForm();
                loadAdminStudents(); // Refresh the list
            } else {
                errorEl.textContent = data.message;
            }
        } catch (error) {
            console.error('Error adding student:', error);
            errorEl.textContent = 'Network error. Please try again.';
        }
    });
}

// Add new room
function showAddRoomForm() {
    document.getElementById('addRoomForm').style.display = 'block';
}

function hideAddRoomForm() {
    document.getElementById('addRoomForm').style.display = 'none';
    document.getElementById('newRoomForm').reset();
}

// Add new room form submission
const newRoomForm = document.getElementById('newRoomForm');
if (newRoomForm) {
    newRoomForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            room_number: document.getElementById('roomNumber').value,
            floor: document.getElementById('roomFloor').value,
            hostel_block: document.getElementById('roomBlock').value,
            capacity: document.getElementById('roomCapacity').value,
            occupied: document.getElementById('roomOccupied').value,
            status: document.getElementById('roomStatus').value
        };
        
        const errorEl = document.getElementById('roomFormError');
        
        // Validation
        if (parseInt(formData.occupied) > parseInt(formData.capacity)) {
            errorEl.textContent = 'Occupied cannot be greater than capacity';
            return;
        }
        
        try {
            const response = await fetch('api/add_room.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('Room added successfully!');
                hideAddRoomForm();
                loadAdminRooms(); // Refresh the list
            } else {
                errorEl.textContent = data.message;
            }
        } catch (error) {
            console.error('Error adding room:', error);
            errorEl.textContent = 'Network error. Please try again.';
        }
    });
}

// Filter functions
function filterComplaints(status) {
    // Update active button
    document.querySelectorAll('#complaints .filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    loadAdminComplaints(status);
}

function filterPayments(status) {
    // Update active button
    document.querySelectorAll('#payments .filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Implement payment filtering
    console.log('Filter payments by:', status);
}

// Show/hide sections
function showSection(id) {
    // Hide all sections
    document.querySelectorAll(".section").forEach(s => s.classList.remove("active"));
    
    // Show selected section
    document.getElementById(id).classList.add("active");
    
    // Update sidebar active state
    document.querySelectorAll(".sidebar li").forEach(li => li.classList.remove("active"));
    const activeLi = Array.from(document.querySelectorAll(".sidebar li")).find(li => 
        li.textContent.toLowerCase().includes(id.toLowerCase()) || 
        (id === 'dashboard' && li.textContent.toLowerCase().includes('dashboard'))
    );
    if (activeLi) activeLi.classList.add("active");
    
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
            // loadAdminPayments();
            break;
        case 'reports':
            // loadAdminReports();
            break;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('admin_dashboard.php')) {
        loadAdminDashboardData();
        
        // Load data for current section
        const activeSection = document.querySelector('.section.active');
        if (activeSection) {
            switch(activeSection.id) {
                case 'students':
                    loadAdminStudents();
                    break;
                case 'rooms':
                    loadAdminRooms();
                    break;
                case 'complaints':
                    loadAdminComplaints();
                    break;
            }
        }
    }
});