<?php 
// Brain Buzz - Dual Environment Database Connection
// Works with both WAMP (local) and Render (production)

// Check if we're on Render (DATABASE_URL environment variable exists)
if (isset($_ENV['DATABASE_URL'])) {
    // RENDER PRODUCTION ENVIRONMENT (PostgreSQL)
    $database_url = $_ENV['DATABASE_URL'];
    $url_parts = parse_url($database_url);
    
    $host = $url_parts['host'];
    $port = $url_parts['port'] ?? 5432;
    $dbname = ltrim($url_parts['path'], '/');
    $user = $url_parts['user'];
    $password = $url_parts['pass'];
    
    // Create PostgreSQL PDO connection
    try {
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // For backward compatibility with existing code
        $conn = $pdo;
        
    } catch(PDOException $e) {
        die("PostgreSQL Connection failed: " . $e->getMessage());
    }
    
} else {
    // LOCAL DEVELOPMENT ENVIRONMENT (MySQL/WAMP)
    $host = 'localhost';
    $user = 'root';
    $password = '';  // Your $ps variable was empty
    $dbname = 'quiz'; // Your $project variable
    
    // Create MySQL PDO connection
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // For backward compatibility with existing code
        $conn = $pdo;
        
    } catch(PDOException $e) {
        die("MySQL Connection failed: " . $e->getMessage());
    }
}

// Legacy variable names for backward compatibility
$project = $dbname ?? 'quiz';
$ps = $password ?? '';

?>
