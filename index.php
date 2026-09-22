<?php
/**
 * Enterprise Amharic NLP Pipeline with Bearer Token Auth & MySQL Logging
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'amharic_nlp_db');

// Valid API Secret Tokens (In production, store these securely in a database table)
$validTokens = [
    'amharic_secret_token_abc123',
    'client_app_token_xyz987'
];

class AmharicNLPParser {
    private static $fidelMap = [
        'ሀ' => 'ha', 'ሁ' => 'hu', 'ሂ' => 'hi', 'ሃ' => 'ha', 'ሄ' => 'he', 'ህ' => 'h', 'ሆ' => 'ho',
        'ለ' => 'la', 'ሉ' => 'lu', 'ሊ' => 'li', 'ላ' => 'la', 'ሌ' => 'le', 'ል' => 'l', 'ሎ' => 'lo',
        'መ' => 'ma', 'ሙ' => 'mu', 'ሚ' => 'mi', 'ማ' => 'ma', 'ሜ' => 'me', 'ም' => 'm', 'ሞ' => 'mo',
        'ሠ' => 'sa', 'ሡ' => 'su', 'ሢ' => 'si', 'ሣ' => 'sa', 'ሤ' => 'se', 'ሥ' => 's', 'ሦ' => 'so',
        'ረ' => 'ra', 'ሩ' => 'ru', 'ሪ' => 'ri', 'ራ' => 'ra', 'ሬ' => 're', 'ር' => 'r', 'ሮ' => 'ro',
        'ሰ' => 'sa', 'ሱ' => 'su', 'ሲ' => 'si', 'ሳ' => 'sa', 'ሴ' => 'se', 'ስ' => 's', 'ሶ' => 'so',
        'ሸ' => 'sha', 'ሹ' => 'shu', 'ሺ' => 'shi', 'ሻ' => 'sha', 'ሼ' => 'she', 'ሽ' => 'sh', 'ሾ' => 'sho',
        'ቀ' => 'qa', 'ቁ' => 'qu', 'ቂ' => 'qi', 'ቃ' => 'qa', 'ቄ' => 'qe', 'ቅ' => 'q', 'ቆ' => 'qo',
        'በ' => 'ba', 'ቡ' => 'bu', 'ቢ' => 'bi', 'ባ' => 'ba', 'ቤ' => 'be', 'ብ' => 'b', 'ቦ' => 'bo',
        'ተ' => 'ta', 'ቱ' => 'tu', 'ቲ' => 'ti', 'ታ' => 'ta', 'ቴ' => 'te', 'ት' => 't', 'ቶ' => 'to',
        'ቸ' => 'cha', 'ቹ' => 'chu', 'ቺ' => 'chi', 'ቻ' => 'cha', 'ቼ' => 'che', 'ች' => 'ch', 'ቾ' => 'cho',
        'የ' => 'ya', 'ዩ' => 'yu', 'ዪ' => 'yi', 'ያ' => 'ya', 'ዬ' => 'ye', 'ይ' => 'y', 'ዮ' => 'yo',
        'ነ' => 'na', 'ኑ' => 'nu', 'ኒ' => 'ni', 'ና' => 'na', 'ኔ' => 'ne', 'ን' => 'n', 'ኖ' => 'no',
        'አ' => 'a', 'ኡ' => 'u', 'ኢ' => 'i', 'ኣ' => 'a', 'ኤ' => 'e', 'እ' => 'e', 'ኦ' => 'o',
        'ከ' => 'ka', 'ኩ' => 'ku', 'ኪ' => 'ki', 'ካ' => 'ka', 'ኬ' => 'ke', 'ክ' => 'k', 'ኮ' => 'ko',
        'ወ' => 'wa', 'ዉ' => 'wu', 'ዊ' => 'wi', 'ዋ' => 'wa', 'ዌ' => 'we', 'ው' => 'w', 'ዎ' => 'wo',
        'ዘ' => 'za', 'ዙ' => 'zu', 'ዚ' => 'zi', 'ዛ' => 'za', 'ዜ' => 'ze', 'ዝ' => 'z', 'ዞ' => 'zo',
        'ደ' => 'da', 'ዱ' => 'du', 'ዲ' => 'di', 'ዳ' => 'da', 'ዴ' => 'de', 'ድ' => 'd', 'ዶ' => 'do',
        'ጀ' => 'ja', 'ጁ' => 'ju', 'ጂ' => 'ji', 'ጃ' => 'ja', 'ጄ' => 'je', 'ጅ' => 'j', 'ጆ' => 'jo',
        'ገ' => 'ga', 'ጉ' => 'gu', 'ጊ' => 'gi', 'ጋ' => 'ga', 'ጌ' => 'ge', 'ግ' => 'g', 'ጎ' => 'go',
        'ጠ' => 'tta', 'ጡ' => 'ttu', 'ጢ' => 'tti', 'ጣ' => 'tta', 'ጤ' => 'tte', 'ጥ' => 'tt', 'ጦ' => 'tto',
        'ፈ' => 'fa', 'ፊ' => 'fi', 'ፋ' => 'fa', 'ፌ' => 'fe', 'ፍ' => 'f', 'ፎ' => 'fo',
        '፡' => ' ', '።' => '.', '፣' => ',', '፤' => ';'
    ];

    public function cleanText(string $rawText): string {
        $cleaned = preg_replace('/[\x00-\x1F\x7F]/u', '', $rawText);
        return trim(preg_replace('/\s+/', ' ', $cleaned));
    }

    public function transliterate(string $text): string {
        $normalized = $this->cleanText($text);
        $result = '';
        $len = mb_strlen($normalized, 'UTF-8');
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($normalized, $i, 1, 'UTF-8');
            $result .= self::$fidelMap[$char] ?? $char;
        }
        return $result;
    }

    public function process(array $lines): array {
        $output = [];
        foreach ($lines as $line) {
            $clean = $this->cleanText($line);
            if (!empty($clean)) {
                $output[] = [
                    'original' => $line,
                    'normalized' => $clean,
                    'transliteration' => $this->transliterate($clean),
                    'length' => mb_strlen($clean, 'UTF-8')
                ];
            }
        }
        return $output;
    }
}

// Handle JSON REST API requests
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
    header('Content-Type: application/json; charset=utf-8');

    // 1. Authenticate Bearer Token
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    $token = '';
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
    }

    if (!in_array($token, $validTokens)) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized: Invalid or missing Bearer token.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 2. Parse JSON Input
    $inputData = json_decode(file_get_contents('php://input'), true);
    $corpus = [];
    if (isset($inputData['text'])) {
        $corpus = [$inputData['text']];
    } elseif (isset($inputData['corpus']) && is_array($inputData['corpus'])) {
        $corpus = $inputData['corpus'];
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Bad Request: Send {"text": "..."} or {"corpus": ["...", "..."]}'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $parser = new AmharicNLPParser();
    $results = $parser->process($corpus);

    // 3. Log to MySQL Database
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("INSERT INTO api_logs (token_used, endpoint_hit, request_count, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([substr($token, 0, 10) . '...', $_SERVER['REQUEST_URI'], count(results)]);
    } catch (Exception $e) {
        // Fail silently on log error so API response still succeeds
    }

    echo json_encode(['status' => 'success', 'count' => count($results), 'data' => $results], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Amharic NLP API Dashboard</title>
    <style>
        body { font-family: sans-serif; background: #0f172a; color: #f8fafc; padding: 40px; }
        .box { max-width: 600px; margin: auto; background: #1e293b; padding: 30px; border-radius: 10px; }
        code { background: #0f172a; color: #38bdf8; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
<div class="box">
    <h2>Amharic NLP API Gateway</h2>
    <p>Send secure <code>POST</code> requests with header <code>Authorization: Bearer amharic_secret_token_abc123</code> and a JSON body.</p>
</div>
</body>
</html>
