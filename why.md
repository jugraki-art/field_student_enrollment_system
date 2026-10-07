+--------------------------------------------------------------------------------------------------------------------+
|                         SYSTEM ARCHITECTURE: FIELD STUDENT ENROLLMENT SYSTEM (KMC)                                 |
+--------------------------------------------------------------------------------------------------------------------+

       [ CLIENT TIER ]                         [ SERVER TIER ]                        [ DATABASE TIER ]
  
  +-----------------------+               +-----------------------+              +-----------------------+
  |   CLIENT WEB INTERFACE|               |   APACHE WEB SERVER   |              |    MYSQL DATABASE     |
  | (HTML5 / CSS3 / JS)   |               |     (XAMPP Runtime)   |              |    (XAMPP Engine)     |
  +-----------------------+               +-----------------------+              +-----------------------+
  |                       |  HTTP POST /  |                       |  SQL Queries |                       |
  | - Student Register Form|=============> | - PHP Routing Engine  |=============>| - `users` Table       |
  | - Daily Attendance UI |   Form Data   | - Session Validation  |  (PDO / SQL) | - `enrollments` Table |
  | - Logbook Submission  |               | - Admin Access Control|              | - `attendance` Table  |
  | - Supervisor Dashboard|<============= | - Logbook Processor   |<=============| - `logbooks` Table    |
  |                       |   JSON / HTML |                       |   Data Record|                       |
  | [Port: 80 / 443]      |   Responses   | [PHP Processing Core] |   Result Sets| [Port: 3306]          |
  +-----------------------+               +-----------------------+              +-----------------------+
              ^                                       ^                                      ^
              |                                       |                                      |
              +------------------- (Git Version Control / GitHub Sync) ---------------------+
                                 `jugraki-art/field_student_enrollment_system`
