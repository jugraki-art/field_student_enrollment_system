<?php
// API Endpoint for Kinondoni Municipal Council HQ Field Student System
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "kinondoni_pt_db";

// Connect to MySQL server
$conn = @new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database server connection failed: " . $conn->connect_error]);
    exit;
}

// Auto-create database & tables if missing
$conn->query("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($db);
$conn->set_charset("utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS field_students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    institution VARCHAR(150) NOT NULL,
    program VARCHAR(150) NOT NULL,
    edu_level ENUM('Certificate', 'Diploma', 'Degree') NOT NULL,
    year_of_study ENUM('Year 1', 'Year 2', 'Year 3', 'Year 4') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Ensure older databases created before the `program` column was added still work.
$programCheck = $conn->query("SHOW COLUMNS FROM field_students LIKE 'program'");
if ($programCheck && $programCheck->num_rows === 0) {
    $conn->query("ALTER TABLE field_students ADD COLUMN program VARCHAR(150) NOT NULL DEFAULT '' AFTER institution");
}

$conn->query("CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    position VARCHAR(50) DEFAULT 'Training Officer',
    phone_number VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $action = $_GET['action'] ?? 'students';

        if ($action === 'profile') {
            $username = trim($_GET['username'] ?? '');
            if ($username === '') {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Username is required."]);
                exit;
            }

            $stmt = $conn->prepare("SELECT username, position, phone_number FROM users WHERE username = ?");
            if (!$stmt) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Profile query could not be prepared."]);
                exit;
            }

            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if (!$user) {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "User profile not found."]);
                exit;
            }

            echo json_encode([
                "status" => "success",
                "user" => [
                    "username" => $user['username'],
                    "position" => $user['position'],
                    "phone" => $user['phone_number']
                ]
            ]);
        } elseif ($action === 'students') {
            $result = $conn->query("SELECT * FROM field_students ORDER BY student_id DESC");
            if ($result === false) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Query failed: " . $conn->error]);
                exit;
            }

            $students = [];
            while($row = $result->fetch_assoc()) {
                $students[] = [
                    "student_id" => (int)$row["student_id"],
                    "id"         => (int)$row["student_id"],
                    "fullName"   => $row["full_name"],
                    "institution"=> $row["institution"],
                    "program"    => $row["program"],
                    "eduLevel"   => $row["edu_level"],
                    "yearOfStudy"=> $row["year_of_study"],
                    "startDate"  => $row["start_date"],
                    "endDate"    => $row["end_date"],
                    "phone"      => $row["phone_number"],
                    "createdAt"  => $row["created_at"]
                ];
            }
            echo json_encode($students);
        }
        break;

    case 'POST':
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);
        if (!$data && !empty($_POST)) {
            $data = $_POST;
        }

        $action = $_GET['action'] ?? ($data['action'] ?? 'add_student');

        if ($action === 'login') {
            $username = trim($data['username'] ?? '');
            $password = trim($data['password'] ?? '');

            if (empty($username) || empty($password)) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Username and password are required."]);
                exit;
            }

            // Demo credentials check
            if ($username === 'admin' && $password === 'admin123') {
                echo json_encode([
                    "status" => "success",
                    "message" => "Login successful",
                    "user" => [
                        "username" => "admin",
                        "position" => "Training Officer"
                    ]
                ]);
                exit;
            }

            // Database user check
            $stmt = $conn->prepare("SELECT user_id, username, password_hash, position FROM users WHERE username = ?");
            if ($stmt) {
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    if (password_verify($password, $row['password_hash']) || $password === $row['password_hash']) {
                        echo json_encode([
                            "status" => "success",
                            "message" => "Login successful",
                            "user" => [
                                "username" => $row['username'],
                                "position" => $row['position']
                            ]
                        ]);
                        $stmt->close();
                        exit;
                    }
                }
                $stmt->close();
            }

            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Invalid username or password."]);
            exit;
        } 
        elseif ($action === 'register') {
            $username = trim($data['username'] ?? '');
            $role     = trim($data['role'] ?? 'Training Officer');
            $phone    = trim($data['phone'] ?? '');
            $password = trim($data['password'] ?? '');

            if (empty($username) || empty($password) || empty($phone)) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Username, password, and phone number are required."]);
                exit;
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, position, phone_number, password_hash) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param('ssss', $username, $role, $phone, $passwordHash);
                if ($stmt->execute()) {
                    echo json_encode(["status" => "success", "message" => "User registered successfully."]);
                } else {
                    http_response_code(400);
                    echo json_encode(["status" => "error", "message" => "Registration failed: " . $conn->error]);
                }
                $stmt->close();
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
            }
            exit;
        }
        else {
            // Default POST: Insert Student
            $fullName   = isset($data['fullName']) ? trim($data['fullName']) : '';
            $institution= isset($data['institution']) ? trim($data['institution']) : '';
            $program    = isset($data['program']) ? trim($data['program']) : '';
            $eduLevel   = isset($data['eduLevel']) ? trim($data['eduLevel']) : '';
            $yearOfStudy= isset($data['yearOfStudy']) ? trim($data['yearOfStudy']) : '';
            $startDate  = isset($data['startDate']) ? trim($data['startDate']) : '';
            $endDate    = isset($data['endDate']) ? trim($data['endDate']) : '';
            $phone      = isset($data['phone']) ? trim($data['phone']) : '';

            if (empty($fullName) || empty($institution) || empty($program) || empty($startDate) || empty($endDate) || empty($phone)) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "All required fields must be filled."]);
                exit;
            }

            // Enforce date validation: endDate > startDate
            if (strtotime($endDate) <= strtotime($startDate)) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "End date must be after start date."]);
                exit;
            }

            // Escape input strings for fallback / safety
            $escapedFullName   = $conn->real_escape_string($fullName);
            $escapedInstitution= $conn->real_escape_string($institution);
            $escapedProgram    = $conn->real_escape_string($program);
            $escapedEduLevel   = $conn->real_escape_string($eduLevel);
            $escapedYear       = $conn->real_escape_string($yearOfStudy);
            $escapedStart      = $conn->real_escape_string($startDate);
            $escapedEnd        = $conn->real_escape_string($endDate);
            $escapedPhone      = $conn->real_escape_string($phone);

            $stmt = $conn->prepare("INSERT INTO field_students (full_name, institution, program, edu_level, year_of_study, start_date, end_date, phone_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param('ssssssss', $fullName, $institution, $program, $eduLevel, $yearOfStudy, $startDate, $endDate, $phone);
                if ($stmt->execute()) {
                    echo json_encode(["status" => "success", "message" => "Student enrolled successfully.", "id" => $conn->insert_id]);
                } else {
                    http_response_code(500);
                    echo json_encode(["status" => "error", "message" => "Database insertion error: " . $stmt->error]);
                }
                $stmt->close();
            } else {
                $sql = "INSERT INTO field_students (full_name, institution, program, edu_level, year_of_study, start_date, end_date, phone_number) 
                        VALUES ('$escapedFullName', '$escapedInstitution', '$escapedProgram', '$escapedEduLevel', '$escapedYear', '$escapedStart', '$escapedEnd', '$escapedPhone')";
                if ($conn->query($sql)) {
                    echo json_encode(["status" => "success", "message" => "Student enrolled successfully.", "id" => $conn->insert_id]);
                } else {
                    http_response_code(500);
                    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
                }
            }
        }
        break;

    case 'DELETE':
        $id = null;
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = intval($_GET['id']);
        } else {
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true);
            if (!empty($data['id'])) {
                $id = intval($data['id']);
            }
        }

        if (!$id) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing id for deletion."]);
            break;
        }

        $stmt = $conn->prepare("DELETE FROM field_students WHERE student_id = ?");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
            break;
        }
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Record deleted."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Delete failed: " . $stmt->error]);
        }
        $stmt->close();
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Invalid Request"]);
        break;
}

$conn->close();
?>
