<?php
$host = 'localhost';
$db = 'booking';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);

  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : null;

    if ($action === 'get_users' || $action === 'get_appointments') {
      $data = [];
      if ($action === 'get_users') {
        $stmt = $pdo->query("SELECT * FROM users");
        $data = $stmt->fetchAll();
      } elseif ($action === 'get_appointments') {
        $stmt = $pdo->query("SELECT * FROM appointments");
        $data = $stmt->fetchAll();
      }

      if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla') !== false) {
        // If accessed via a browser, return HTML
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8">
          <title>Display Data</title>
          <style>
            body {
              font-family: Arial, sans-serif;
              margin: 20px;
            }
            table {
              width: 100%;
              border-collapse: collapse;
            }
            table, th, td {
              border: 1px solid black;
            }
            th, td {
              padding: 10px;
              text-align: center; /* Center-align text in both tables */
            }
            th {
              background-color: #f2f2f2;
            }
            .users-table td {
              white-space: nowrap; /* Prevent text wrapping in users table */
            }
          </style>
        </head>
        <body>';

        if ($action === 'get_users') {
          echo '<center><h2>Created Users</h2><center>';
          echo '<table class="users-table">';
          echo '<thead>
                <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Email Address</th>
                </tr>
                </thead>';
          echo '<tbody>';
          foreach ($data as $row) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['userID'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($row['username'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($row['email'] ?? '') . '</td>';
            echo '</tr>';
          }
          echo '</tbody></table>';
        } elseif ($action === 'get_appointments') {
          echo '<center><h2>Appointments</h2><center>';
          echo '<table class="appointments-table">';
          echo '<thead>
                <tr>
                <th>Appointment ID</th>
                <th>UserID</th>
                <th>Appointment Date</th>
                <th>Appointment Time</th>
                <th>Description</th>
                </tr>
                </thead>';
          echo '<tbody>';
          foreach ($data as $row) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['appointmentID'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($row['userID'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($row['appointmentDate'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($row['appointmentTime'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($row['description'] ?? '') . '</td>';
            echo '</tr>';
          }
          echo '</tbody></table>';
        }

        echo '</body></html>';
      } else {
        // If accessed via an API client, return JSON
        header("Content-Type: application/json");
        echo json_encode($data);
      }
    } else {
      // Handle other GET requests or provide default response
      header("Content-Type: application/json");
      echo json_encode(['message' => 'Valid actions: get_users, get_appointments']);
    }
  } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Content-Type: application/json");
    $action = isset($_GET['action']) ? $_GET['action'] : null;

    if ($action === 'create_user') {
      $input = json_decode(file_get_contents('php://input'), true);
      $sql = "INSERT INTO users (username, email) VALUES (?, ?)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$input['username'], $input['email']]);
      echo json_encode(['message' => 'User added successfully']);
    } elseif ($action === 'create_appointments') {
      $input = json_decode(file_get_contents('php://input'), true);
      $sql = "INSERT INTO appointments (userID, appointmentDate, appointmentTime, description) VALUES (?, ?, ?, ?)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$input['userID'], $input['appointmentDate'], $input['appointmentTime'], $input['description']]);
      echo json_encode(['message' => 'Appointment created successfully']);
    } else {
      echo json_encode(['error' => 'Invalid action parameter']);
    }
  }
} catch(PDOException $e) {
  // Handle PDO exceptions
header("Content-Type: application/json");
echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
