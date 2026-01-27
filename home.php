<?php
// home.php
require_once 'config/session.php';

$session = new SessionManager();
$session->requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>UniNest Dashboard</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="glass-bg1">

<div class="layout">

<aside class="sidebar">
    <h2>UniNest</h2>
    <ul>
        <li class="active" onclick="showSection('dashboard')">Dashboard</li>
        <li onclick="showSection('students')">Students</li>
        <li onclick="showSection('rooms')">Rooms</li>
        <li onclick="showSection('complaints')">Complaints</li>
        <li onclick="showSection('payment')">Payment</li>
        <li onclick="logout()">Logout</li>
    </ul>

    <div class="logo-container">
        <img src="images/lo.png" class="logo">
        <h5>University of Vavuniya</h5>
        <p>Welcome, <?php echo $_SESSION['full_name']; ?></p>
    </div>
</aside>

<main class="content">
    <section id="dashboard" class="section active">
        <h1>Dashboard</h1>
        <div class="cards">
            <div class="card"><li onclick="showSection('students')">Students<br><strong>Loading...</strong></li></div>
            <div class="card"><li onclick="showSection('rooms')">Rooms<br><strong>Loading...</strong></li></div>
            <div class="card"><li onclick="showSection('complaints')">Complaints<br><strong>Loading...</strong></li></div>
            <div class="card"><li onclick="showSection('payment')">Pending Fees<br><strong>Loading...</strong></li></div>
        </div>
    </section>

    <section id="students" class="section">
        <h1>Student Details</h1>
        <table>
            <thead>
                <tr><th>Reg No</th><th>Name</th><th>Room</th><th>Email</th></tr>
            </thead>
            <tbody id="studentsTable">
                <!-- Will be populated by JavaScript -->
            </tbody>
        </table>
    </section>

    <<!-- In home.php, replace the rooms section -->
<section id="rooms" class="section">
    <h1>Room Availability</h1>
    <p>Browse and filter available rooms in the hostel</p>
    
    <div class="rooms-slider-container">
        <!-- Statistics Bar -->
        <div class="rooms-statistics">
            <div class="stat-card">
                <div>Total Rooms</div>
                <div class="stat-value" id="total-rooms">40</div>
                <div>All Floors</div>
            </div>
            <div class="stat-card">
                <div>Available</div>
                <div class="stat-value stat-available" id="available-rooms">0</div>
                <div>Ready to occupy</div>
            </div>
            <div class="stat-card">
                <div>Occupied</div>
                <div class="stat-value stat-occupied" id="occupied-rooms">0</div>
                <div>Currently in use</div>
            </div>
            <div class="stat-card">
                <div>Vacant Beds</div>
                <div class="stat-value stat-vacant" id="vacant-beds">0</div>
                <div>Available beds</div>
            </div>
        </div>
        
        <!-- Floor Filter -->
        <div class="floor-filter">
            <button class="filter-btn active" onclick="filterRooms('all')">All Floors</button>
            <button class="filter-btn" onclick="filterRooms(1)">1st Floor</button>
            <button class="filter-btn" onclick="filterRooms(2)">2nd Floor</button>
            <button class="filter-btn" onclick="filterRooms(3)">3rd Floor</button>
            <button class="filter-btn" onclick="filterRooms(4)">4th Floor</button>
        </div>
        
        <!-- Status Filter -->
        <div class="floor-filter" style="margin-top: 10px;">
            <button class="filter-btn active" onclick="filterByStatus('all')">All Status</button>
            <button class="filter-btn" onclick="filterByStatus('available')">Available</button>
            <button class="filter-btn" onclick="filterByStatus('occupied')">Occupied</button>
            <button class="filter-btn" onclick="filterByStatus('maintenance')">Maintenance</button>
        </div>
        
        <!-- Slider Header -->
        <div class="slider-header">
            <h2 class="slider-title">All Rooms</h2>
            <div class="slider-controls">
                <button class="slider-btn" onclick="scrollSlider(-300)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="slider-btn" onclick="scrollSlider(300)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        
        <!-- Rooms Slider -->
        <div class="rooms-slider" id="roomsSlider">
            <!-- Rooms will be loaded dynamically -->
            <div style="text-align: center; padding: 40px; color: rgba(255,255,255,0.5); width: 100%;">
                <i class="fas fa-spinner fa-spin" style="font-size: 24px; margin-bottom: 10px;"></i>
                <p>Loading rooms...</p>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 20px; opacity: 0.7;">
            <small>Scroll horizontally to view more rooms</small>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: center;">
        <button class="action-btn" onclick="showRoomDetails()" style="background: var(--accent); color: #000; padding: 10px 20px;">
            <i class="fas fa-search"></i> Search Room
        </button>
        <button class="action-btn" onclick="requestRoomChange()" style="background: rgba(255,255,255,0.1); padding: 10px 20px;">
            <i class="fas fa-exchange-alt"></i> Request Room Change
        </button>
    </div>
</section>

    <!-- Replace the entire complaints section in home.php -->
<section id="complaints" class="section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Complaints</h1>
        <button id="newComplaintBtn" class="action-btn" 
                style="background: var(--accent); color: #000; padding: 10px 20px;">
            <i class="fas fa-plus"></i> New Complaint
        </button>
    </div>
    
    <!-- Complaint Form -->
    <div id="complaintForm" style="display: none; margin-bottom: 30px;">
        <div style="background: rgba(35, 40, 11, 0.42); padding: 20px; border-radius: 10px;">
            <h3 style="margin-bottom: 20px;">Submit New Complaint</h3>
            <form id="newComplaintForm">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: rgba(244, 233, 233, 0.8);">
                        Complaint Type
                    </label>
                    <select id="complaintType" style="width: 100%; padding: 12px; border-radius: 8px; font-size: 14px;color: rgba(49, 44, 44, 0.8);">
                        <option value="Electrical">Electrical Issue</option>
                        <option value="Plumbing">Plumbing Issue</option>
                        <option value="Cleaning">Cleaning Required</option>
                        <option value="Furniture">Furniture Repair</option>
                        <option value="Internet">Internet Problem</option>
                        <option value="Security">Security Concern</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; color: rgba(237, 228, 228, 0.8);">
                        Description
                    </label>
                    <textarea id="complaintDesc" 
                              placeholder="Describe your complaint in detail..." 
                              rows="4" 
                              style="width: 100%; padding: 12px; border-radius: 8px; font-size: 14px;color: rgba(237, 228, 228, 0.8)"></textarea>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="action-btn" 
                            style="background: var(--accent); color: #000; flex: 1; padding: 12px;">
                        <i class="fas fa-paper-plane"></i> Submit Complaint
                    </button>
                    <button type="button" id="cancelComplaintBtn" class="action-btn" 
                            style="flex: 1; padding: 12px;">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
                <p id="complaintError" class="error" style="margin-top: 15px; text-align: center;"></p>
            </form>
        </div>
    </div>
    
    <!-- Complaints List -->
    <div id="complaintsList">
        <div style="text-align: center; padding: 40px; color: rgba(255,255,255,0.5);">
            <i class="fas fa-spinner fa-spin" style="font-size: 24px; margin-bottom: 10px;"></i>
            <p>Loading complaints...</p>
        </div>
    </div>
</section>
<script>
// Debug: Check if functions exist
console.log('showComplaintForm function exists:', typeof showComplaintForm);
console.log('loadComplaintsData function exists:', typeof loadComplaintsData);

// Add event listener directly (temporary fix)
document.addEventListener('DOMContentLoaded', function() {
    const newComplaintBtn = document.querySelector('button[onclick="showComplaintForm()"]');
    if (newComplaintBtn) {
        console.log('New complaint button found');
        newComplaintBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Button clicked');
            showComplaintForm();
        });
    }
});
</script>

    <section id="payment" class="section">
        <h1>Payment</h1>
        <div id="paymentsList"></div>
    </section>
</main>
</div>
<script>
// Direct event listeners for debugging
document.addEventListener('DOMContentLoaded', function() {
    console.log('Home page loaded');
    
    // New Complaint Button
    const newBtn = document.getElementById('newComplaintBtn');
    if (newBtn) {
        console.log('Found new complaint button');
        newBtn.addEventListener('click', function() {
            console.log('New complaint button clicked');
            document.getElementById('complaintForm').style.display = 'block';
            this.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    }
    
    // Cancel Complaint Button
    const cancelBtn = document.getElementById('cancelComplaintBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            document.getElementById('complaintForm').style.display = 'none';
        });
    }
    
    // Complaint Form Submission
    const complaintForm = document.getElementById('newComplaintForm');
    if (complaintForm) {
        complaintForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Complaint form submit event');
            
            const type = document.getElementById('complaintType').value;
            const desc = document.getElementById('complaintDesc').value.trim();
            const errorEl = document.getElementById('complaintError');
            
            if (!desc) {
                errorEl.textContent = 'Please enter a description';
                return;
            }
            
            if (desc.length < 10) {
                errorEl.textContent = 'Description must be at least 10 characters';
                return;
            }
            
            errorEl.textContent = '';
            errorEl.style.color = 'var(--accent)';
            errorEl.textContent = 'Submitting...';
            
            try {
                const response = await fetch('api/complaints.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ type: type, description: desc })
                });
                
                const data = await response.json();
                console.log('Submission response:', data);
                
                if (data.success) {
                    alert('✅ Complaint submitted successfully!');
                    document.getElementById('complaintForm').style.display = 'none';
                    document.getElementById('newComplaintForm').reset();
                    loadComplaintsData(); // Refresh list
                } else {
                    errorEl.style.color = 'var(--danger)';
                    errorEl.textContent = data.message || 'Submission failed';
                }
            } catch (error) {
                console.error('Error:', error);
                errorEl.style.color = 'var(--danger)';
                errorEl.textContent = 'Network error. Please try again.';
            }
        });
    }
});

// Make loadComplaintsData globally accessible
window.loadComplaintsData = async function() {
    console.log('Loading complaints data...');
    try {
        const response = await fetch('api/complaints.php');
        const data = await response.json();
        console.log('Complaints data:', data);
        
        const list = document.getElementById('complaintsList');
        if (!list) return;
        
        if (data.success && data.data && data.data.length > 0) {
            let html = '<div style="display: grid; gap: 15px;">';
            data.data.forEach(complaint => {
                html += `
                    <div class="complaint-item">
                        <div class="complaint-header">
                            <div>
                                <h4 style="margin: 0 0 5px 0; color: var(--accent);">
                                    ${complaint.complaint_type}
                                </h4>
                                <p style="margin: 0; opacity: 0.9;">${complaint.description}</p>
                            </div>
                            <div style="text-align: right;">
                                <span class="complaint-status status-${complaint.status}">
                                    ${complaint.status}
                                </span>
                                <p class="complaint-date">
                                    ${new Date(complaint.created_at).toLocaleDateString()}
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            list.innerHTML = html;
        } else {
            list.innerHTML = `
                <div style="text-align: center; padding: 40px; color: rgba(255,255,255,0.5);">
                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px;"></i>
                    <h3>No Complaints Found</h3>
                    <p>You haven't submitted any complaints yet.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading complaints:', error);
        document.getElementById('complaintsList').innerHTML = 
            '<p class="error">Failed to load complaints. Please refresh.</p>';
    }
};
</script>
<script src="script.js"></script>
</body>
</html>