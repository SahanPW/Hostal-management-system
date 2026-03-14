<?php
// api/complaints.php - Fixed with proper HTTP method handling
require_once '../config/database.php';
require_once '../config/session.php';

header('Content-Type: application/json');

$session = new SessionManager();
$database = new Database();
$db = $database->getConnection();

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// For GET requests with query parameters
if ($method === 'GET') {
    $input = $_GET;
}

switch ($method) {
    case 'GET':
        // Get complaints for the logged-in student (excluding deleted ones)
        if (!isset($_SESSION['reg_no'])) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            exit;
        }
        
        $query = "SELECT c.*, s.full_name 
                  FROM complaints c 
                  LEFT JOIN students s ON c.student_reg_no = s.reg_no 
                  WHERE c.student_reg_no = :reg_no 
                  AND (c.is_deleted = FALSE OR c.is_deleted IS NULL)
                  ORDER BY c.created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':reg_no', $_SESSION['reg_no']);
        $stmt->execute();
        
        $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $complaints
        ]);
        break;
        
    case 'POST':
        // Check if it's a delete request
        if (isset($input['action']) && $input['action'] === 'delete') {
            // Handle delete complaint
            handleDeleteComplaint($input, $db);
        } else {
            // Handle new complaint submission
            handleNewComplaint($input, $db);
        }
        break;
        
    case 'DELETE':
        // Handle DELETE method for complaint deletion
        handleDeleteComplaint($input, $db);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method: ' . $method]);
        break;
}

// Function to handle new complaint submission
function handleNewComplaint($data, $db) {
    if (!isset($_SESSION['reg_no'])) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit;
    }
    
    if (!isset($data['type']) || !isset($data['description']) || empty($data['description'])) {
        echo json_encode(['success' => false, 'message' => 'Type and description are required']);
        exit;
    }
    
    // Trim and sanitize inputs
    $complaint_type = trim($data['type']);
    $description = trim($data['description']);
    
    // Check for duplicate complaint in the last 5 minutes
    $duplicateQuery = "SELECT id FROM complaints 
                      WHERE student_reg_no = :reg_no 
                      AND complaint_type = :type 
                      AND description = :description 
                      AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE) 
                      AND (is_deleted = FALSE OR is_deleted IS NULL)
                      LIMIT 1";
    
    $duplicateStmt = $db->prepare($duplicateQuery);
    $duplicateStmt->bindParam(':reg_no', $_SESSION['reg_no']);
    $duplicateStmt->bindParam(':type', $complaint_type);
    $duplicateStmt->bindParam(':description', $description);
    $duplicateStmt->execute();
    
    if ($duplicateStmt->rowCount() > 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'You have already submitted a similar complaint recently. Please wait a few minutes before submitting again.'
        ]);
        exit;
    }
    
    // Insert new complaint
    $query = "INSERT INTO complaints 
              (student_reg_no, complaint_type, description, status, created_at) 
              VALUES (:reg_no, :type, :description, 'pending', NOW())";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':reg_no', $_SESSION['reg_no']);
    $stmt->bindParam(':type', $complaint_type);
    $stmt->bindParam(':description', $description);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Complaint submitted successfully',
            'complaint_id' => $db->lastInsertId()
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to submit complaint. Please try again.'
        ]);
    }
}

// Function to handle complaint deletion
function handleDeleteComplaint($data, $db) {
    if (!isset($_SESSION['reg_no'])) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit;
    }
    
    if (!isset($data['complaint_id'])) {
        echo json_encode(['success' => false, 'message' => 'Complaint ID is required']);
        exit;
    }
    
    // First, verify that the complaint belongs to the logged-in student
    $verifyQuery = "SELECT id FROM complaints 
                   WHERE id = :id 
                   AND student_reg_no = :reg_no 
                   AND (is_deleted = FALSE OR is_deleted IS NULL)";
    
    $verifyStmt = $db->prepare($verifyQuery);
    $verifyStmt->bindParam(':id', $data['complaint_id']);
    $verifyStmt->bindParam(':reg_no', $_SESSION['reg_no']);
    $verifyStmt->execute();
    
    if ($verifyStmt->rowCount() === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Complaint not found or you do not have permission to delete it'
        ]);
        exit;
    }
    
    // Soft delete the complaint
    $query = "UPDATE complaints 
              SET is_deleted = TRUE, deleted_at = NOW() 
              WHERE id = :id 
              AND student_reg_no = :reg_no";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data['complaint_id']);
    $stmt->bindParam(':reg_no', $_SESSION['reg_no']);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Complaint deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete complaint'
        ]);
    }
}
?>