<?php
session_start();
// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

header("Content-Type: application/json");

// Include database connection
require_once 'config.php';

// Function to expand query semantically (very basic example)
function expandQuery($query) {
    $expansions = [
        "AI" => ["artificial intelligence", "machine learning", "neural networks"],
        "ML" => ["machine learning", "deep learning", "AI"],
        "robotics" => ["automation", "robots", "mechanical systems"],
        "data" => ["big data", "data science", "data analysis"]
        // You can expand this list or link it to a database or external API later
    ];

    $query = strtolower($query);
    $expandedTerms = [$query];

    foreach ($expansions as $keyword => $synonyms) {
        if (strpos($query, strtolower($keyword)) !== false) {
            $expandedTerms = array_merge($expandedTerms, $synonyms);
        }
    }

    return array_unique($expandedTerms);
}

// Validate and sanitize the search query
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if (empty($query)) {
    http_response_code(400);
    echo json_encode(["error" => "Empty search query"]);
    exit();
}

// Add query to search log
$logStmt = $conn->prepare("INSERT INTO search_logs (user_id, query, timestamp) VALUES (?, ?, NOW())");
$logStmt->bind_param("is", $_SESSION['user_id'], $query);
$logStmt->execute();
$logStmt->close();

// Expand query semantically
$expandedTerms = expandQuery($query);

// Build SQL dynamically
$sql = "SELECT id, title, snippet FROM research_data WHERE ";
$conditions = [];
$params = [];
$types = '';

foreach ($expandedTerms as $term) {
    $conditions[] = "(title LIKE ? OR snippet LIKE ?)";
    $likeTerm = "%" . $term . "%";
    $params[] = $likeTerm;
    $params[] = $likeTerm;
    $types .= 'ss';
}

$sql .= implode(" OR ", $conditions) . " LIMIT 10";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to prepare statement"]);
    exit();
}

// Bind parameters dynamically
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $results = [];

    while ($row = $result->fetch_assoc()) {
        $results[] = [
            "id" => $row['id'],
            "title" => $row['title'],
            "snippet" => $row['snippet']
        ];
    }

    echo json_encode($results);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to execute query"]);
}

$stmt->close();
$conn->close();
?>