<?php
// Quick password hash generator
// Delete this file after use for security

$password = '2026';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Password Hash</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #1e4d9f; }
        .hash { background: #f0f0f0; padding: 15px; border-radius: 5px; word-break: break-all; margin: 20px 0; font-family: monospace; }
        .sql { background: #e8f4f8; padding: 15px; border-radius: 5px; border-left: 4px solid #1e4d9f; margin: 20px 0; }
        .warning { background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin: 20px 0; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔐 Password Hash Generated</h1>
        
        <h3>Password:</h3>
        <div class='hash'>2026</div>
        
        <h3>Hash:</h3>
        <div class='hash'>$hash</div>
        
        <h3>SQL Query to Update:</h3>
        <div class='sql'>
            <code>UPDATE users SET email = 'cultoonmovic4@gmail.com', password = '$hash' WHERE username = 'admin';</code>
        </div>
        
        <div class='warning'>
            <strong>⚠️ Security Warning:</strong> Delete this file (hash_password.php) after copying the SQL query!
        </div>
        
        <h3>Steps:</h3>
        <ol>
            <li>Copy the SQL query above</li>
            <li>Open phpMyAdmin</li>
            <li>Select your database</li>
            <li>Click 'SQL' tab</li>
            <li>Paste and execute the query</li>
            <li>Delete this file for security</li>
        </ol>
    </div>
</body>
</html>";
?>
