<?php
ini_set('display_errors', 0);
error_reporting(0);
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);
$text = $input['text'] ?? '';

// Comprehensive Amharic Fidel to Latin Phonetic Mapping Table
$fidelMap = [
    // ሀ family
    'ሀ' => 'ha', 'ሁ' => 'hu', 'ሂ' => 'hi', 'ሃ' => 'ha', 'ሄ' => 'he', 'ህ' => 'h', 'ሆ' => 'ho', 'ኋ' => 'hwa',
    // ለ family
    'ለ' => 'le', 'ሉ' => 'lu', 'ሊ' => 'li', 'ላ' => 'la', 'ሌ' => 'le', 'ል' => 'l', 'ሎ' => 'lo', 'ሏ' => 'lwa',
    // ሐ family
    'ሐ' => 'ha', 'ሑ' => 'hu', 'ሒ' => 'hi', 'ሓ' => 'ha', 'ሔ' => 'he', 'ሕ' => 'h', 'ሖ' => 'ho', 'ሗ' => 'hwa',
    // መ family
    'መ' => 'me', 'ሙ' => 'mu', 'ሚ' => 'mi', 'ማ' => 'ma', 'ሜ' => 'me', 'ም' => 'm', 'ሞ' => 'mo', 'ሟ' => 'mwa',
    // ሠ family
    'ሠ' => 'se', 'ሡ' => 'su', 'ሢ' => 'si', 'ሣ' => 'sa', 'ሤ' => 'se', 'ሥ' => 's', 'ሦ' => 'so', 'ሧ' => 'swa',
    // ረ family
    'ረ' => 're', 'ሩ' => 'ru', 'ሪ' => 'ri', 'ራ' => 'ra', 'ሬ' => 're', 'ር' => 'r', 'ሮ' => 'ro', 'ሯ' => 'rwa',
    // ሰ family
    'ሰ' => 'se', 'ሱ' => 'su', 'ሲ' => 'si', 'ሳ' => 'sa', 'ሴ' => 'se', 'ስ' => 's', 'ሶ' => 'so', 'ሷ' => 'swa',
    // ሸ family
    'ሸ' => 'she', 'ሹ' => 'shu', 'ሺ' => 'shi', 'ሻ' => 'sha', 'ሼ' => 'she', 'ሽ' => 'sh', 'ሾ' => 'sho', 'ሿ' => 'shwa',
    // ቀ family
    'ቀ' => 'qe', 'ቁ' => 'qu', 'ቂ' => 'qi', 'ቃ' => 'qa', 'ቄ' => 'qe', 'ቅ' => 'q', 'ቆ' => 'qo', 'ቋ' => 'qwa',
    // በ family
    'በ' => 'be', 'ቡ' => 'bu', 'ቢ' => 'bi', 'ባ' => 'ba', 'ቤ' => 'be', 'ብ' => 'b', 'ቦ' => 'bo', 'ቧ' => 'bwa',
    // ቨ family
    'ቨ' => 've', 'ቩ' => 'vu', 'ቪ' => 'vi', 'ቫ' => 'va', 'ቬ' => 've', 'ቭ' => 'v', 'ቮ' => 'vo', 'ቯ' => 'vwa',
    // ተ family
    'ተ' => 'te', 'ቱ' => 'tu', 'ቲ' => 'ti', 'ታ' => 'ta', 'ቴ' => 'te', 'ት' => 't', 'ቶ' => 'to', 'ቷ' => 'twa',
    // ቸ family
    'ቸ' => 'che', 'ቹ' => 'chu', 'ቺ' => 'chi', 'ቻ' => 'cha', 'ቼ' => 'che', 'ች' => 'ch', 'ቾ' => 'cho', 'ቿ' => 'chwa',
    // የ family
    'የ' => 'ye', 'ዩ' => 'yu', 'ዪ' => 'yi', 'ያ' => 'ya', 'ዬ' => 'ye', 'ይ' => 'y', 'ዮ' => 'yo',
    // ነ family
    'ነ' => 'ne', 'ኑ' => 'nu', 'ኒ' => 'ni', 'ና' => 'na', 'ኔ' => 'ne', 'ን' => 'n', 'ኖ' => 'no', 'ኗ' => 'nwa',
    // ኘ family
    'ኘ' => 'nye', 'ኙ' => 'nyu', 'ኚ' => 'nyi', 'ኛ' => 'nya', 'ኜ' => 'nye', 'ኝ' => 'ny', 'ኞ' => 'nyo', 'ኟ' => 'nywa',
    // አ family
    'አ' => 'a', 'ኡ' => 'u', 'ኢ' => 'i', 'ኣ' => 'a', 'ኤ' => 'e', 'እ' => 'e', 'ኦ' => 'o', 'ኧ' => 'wa',
    // ከ family
    'ከ' => 'ke', 'ኩ' => 'ku', 'ኪ' => 'ki', 'ካ' => 'ka', 'ኬ' => 'ke', 'ክ' => 'k', 'ኮ' => 'ko', 'ኳ' => 'kwa',
    // ኸ family
    'ኸ' => 'he', 'ኹ' => 'hu', 'ኺ' => 'hi', 'ኻ' => 'ha', 'ኼ' => 'he', 'ኽ' => 'h', 'ኾ' => 'ho', 'ዃ' => 'hwa',
    // ወ family
    'ወ' => 'we', 'ዉ' => 'wu', 'ዊ' => 'wi', 'ዋ' => 'wa', 'ዌ' => 'we', 'ው' => 'w', 'ዎ' => 'wo',
    // ዘ family
    'ዘ' => 'ze', 'ዙ' => 'zu', 'ዚ' => 'zi', 'ዛ' => 'za', 'ዜ' => 'ze', 'ዝ' => 'z', 'ዞ' => 'zo', 'ዟ' => 'zwa',
    // ዠ family
    'ዠ' => 'zhe', 'ዡ' => 'zhu', 'ዢ' => 'zhi', 'ዣ' => 'zha', 'ዤ' => 'zhe', 'ዥ' => 'zh', 'ዦ' => 'zho', 'ዧ' => 'zhwa',
    // ደ family
    'ደ' => 'de', 'ዱ' => 'du', 'ዲ' => 'di', 'ዳ' => 'da', 'ዴ' => 'de', 'ድ' => 'd', 'ዶ' => 'do', 'ዷ' => 'dwa',
    // ጀ family
    'ጀ' => 'je', 'ጁ' => 'ju', 'ጂ' => 'ji', 'ጃ' => 'ja', 'ጄ' => 'je', 'ጅ' => 'j', 'ጆ' => 'jo', 'ጇ' => 'jwa',
    // ገ family
    'ገ' => 'ge', 'ጉ' => 'gu', 'ጊ' => 'gi', 'ጋ' => 'ga', 'ጌ' => 'ge', 'ግ' => 'g', 'ጎ' => 'go', 'ጓ' => 'gwa',
    // ጠ family
    'ጠ' => 'te', 'ጡ' => 'tu', 'ጢ' => 'ti', 'ጣ' => 'ta', 'ጤ' => 'te', 'ጥ' => 't', 'ጦ' => 'to', 'ጧ' => 'twa',
    // ጨ family
    'ጨ' => 'ce', 'ጩ' => 'cu', 'ጪ' => 'ci', 'ጫ' => 'ca', 'ጬ' => 'ce', 'ጭ' => 'c', 'ጮ' => 'co', 'ጯ' => 'cwa',
    // ጰ family
    'ጰ' => 'pe', 'ጱ' => 'pu', 'ጲ' => 'pi', 'ጳ' => 'pa', 'ጴ' => 'pe', 'ጵ' => 'p', 'ጶ' => 'po', 'ጷ' => 'pwa',
    // ጸ family
    'ጸ' => 'se', 'ጹ' => 'su', 'ጺ' => 'si', 'ጻ' => 'sa', 'ጼ' => 'se', 'ጽ' => 's', 'ጾ' => 'so', 'ጿ' => 'swa',
    // ፈ family
    'ፈ' => 'fe', 'ፉ' => 'fu', 'ፊ' => 'fi', 'ፋ' => 'fa', 'ፌ' => 'fe', 'ፍ' => 'f', 'ፎ' => 'fo', 'ፏ' => 'fwa',
    // ፀ family
    'ፀ' => 'se', 'ፁ' => 'su', 'ፂ' => 'si', 'ፃ' => 'sa', 'ፄ' => 'se', 'ፅ' => 's', 'ፆ' => 'so',
    // ፐ family
    'ፐ' => 'pe', 'ፑ' => 'pu', 'ፒ' => 'pi', 'ፓ' => 'pa', 'ፔ' => 'pe', 'ፕ' => 'p', 'ፖ' => 'po', 'ፗ' => 'pwa',
    // ዐ family
    'ዐ' => 'a', 'ዑ' => 'u', 'ዒ' => 'i', 'ዓ' => 'a', 'ዔ' => 'e', 'ዕ' => 'e', 'ዖ' => 'o',
    // ኀ family
    'ኀ' => 'he', 'ኁ' => 'hu', 'ኂ' => 'hi', 'ኃ' => 'ha', 'ኄ' => 'he', 'ኅ' => 'h', 'ኆ' => 'ho', 'ኋ' => 'hwa',
    // Punctuation marks
    '፡' => ' ', '።' => '.', '፣' => ',', '፤' => ';', '፥' => ':'
];

$transliterated = '';
$mb_len = mb_strlen($text);
for ($i = 0; $i < $mb_len; $i++) {
    $char = mb_substr($text, $i, 1);
    $transliterated .= $fidelMap[$char] ?? $char;
}

echo json_encode([
    "status" => "success",
    "original_text" => $text,
    "transliterated_text" => $transliterated,
    "timestamp" => date('Y-m-d H:i:s')
], JSON_UNESCAPED_UNICODE);
exit();
?>