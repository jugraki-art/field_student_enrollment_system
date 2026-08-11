<?php
// API Endpoint for Kinondoni Municipal Council HQ Field Student System
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "kinondoni_pt_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $result = $conn->query("SELECT * FROM field_students ORDER BY student_id DESC");
        if ($result === false) {
            http_response_code(500);
            echo json_encode(["error" => "Query failed: " . $conn->error]);
            exit;
        }

        $students = [];
        while($row = $result->fetch_assoc()) {
            $students[] = [
                "student_id" => $row["student_id"],
                "fullName" => $row["full_name"],
                "institution" => $row["institution"],
                "eduLevel" => $row["edu_level"],
                "yearOfStudy" => $row["year_of_study"],
                "startDate" => $row["start_date"],
                "endDate" => $row["end_date"],
                "phone" => $row["phone_number"],
                "createdAt" => $row["created_at"]
            ];
        }
        echo json_encode($students);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $fullName = $conn->real_escape_string($data['fullName']);
        $institution = $conn->real_escape_string($data['institution']);
        $eduLevel = $conn->real_escape_string($data['eduLevel']);
        $yearOfStudy = $conn->real_escape_string($data['yearOfStudy']);
        $startDate = $conn->real_escape_string($data['startDate']);
        $endDate = $conn->real_escape_string($data['endDate']);
        $phone = $conn->real_escape_string($data['phone']);

        if(strtotime($endDate) <= strtotime($startDate)) {
            echo json_encode(["status" => "error", "message" => "End date must be after start date."]);
            exit;
        }

        $sql = "INSERT INTO field_students (full_name, institution, edu_level, year_of_study, start_date, end_date, phone_number) 
                VALUES ('$fullName', '$institution', '$eduLevel', '$yearOfStudy', '$startDate', '$endDate', '$phone')";

        if($conn->query($sql)) {
            echo json_encode(["status" => "success", "message" => "Student enrolled successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
        break;

    default:
        echo json_encode(["message" => "Invalid Request"]);
        break;
}

$conn->close();
?>
