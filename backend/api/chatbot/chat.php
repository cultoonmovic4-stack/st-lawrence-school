<?php
/**
 * St. Lawrence School AI Assistant - Chat API
 * Handles chat requests and returns intelligent responses
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'knowledge_base.php';

// Start session for conversation tracking
session_start();

// Initialize knowledge base
$kb = new KnowledgeBase();

// Get request data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    $input = $_POST;
}

$action = $input['action'] ?? 'chat';

// Handle different actions
switch ($action) {
    case 'init':
        // Initialize chat session
        $_SESSION['chat_history'] = [];
        $_SESSION['chat_started'] = time();
        
        echo json_encode([
            'success' => true,
            'message' => "Hello! 👋 I'm your St. Lawrence Junior School assistant. How can I help you today?",
            'quickActions' => $kb->getQuickActions(),
            'sessionId' => session_id()
        ]);
        break;
        
    case 'chat':
        $question = $input['message'] ?? '';
        
        if (empty($question)) {
            echo json_encode([
                'success' => false,
                'error' => 'No message provided'
            ]);
            exit;
        }
        
        // Find answer from knowledge base
        $result = $kb->findAnswer($question);
        
        // Store in chat history
        if (!isset($_SESSION['chat_history'])) {
            $_SESSION['chat_history'] = [];
        }
        
        $_SESSION['chat_history'][] = [
            'user' => $question,
            'bot' => $result['response'],
            'timestamp' => time(),
            'category' => $result['category']
        ];
        
        echo json_encode([
            'success' => true,
            'response' => $result['response'],
            'found' => $result['found'],
            'category' => $result['category'],
            'timestamp' => date('H:i')
        ]);
        break;
        
    case 'quickAction':
        $question = $input['question'] ?? '';
        
        if (empty($question)) {
            echo json_encode([
                'success' => false,
                'error' => 'No question provided'
            ]);
            exit;
        }
        
        // Find answer from knowledge base
        $result = $kb->findAnswer($question);
        
        echo json_encode([
            'success' => true,
            'response' => $result['response'],
            'found' => $result['found'],
            'category' => $result['category'],
            'timestamp' => date('H:i')
        ]);
        break;
        
    case 'history':
        // Get chat history
        $history = $_SESSION['chat_history'] ?? [];
        
        echo json_encode([
            'success' => true,
            'history' => $history
        ]);
        break;
        
    case 'clear':
        // Clear chat history
        $_SESSION['chat_history'] = [];
        
        echo json_encode([
            'success' => true,
            'message' => 'Chat history cleared'
        ]);
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action'
        ]);
        break;
}
