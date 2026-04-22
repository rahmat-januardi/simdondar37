<?php
/**
 * Script untuk mendekode hex obfuscation dari file PHP
 * Mengubah karakter hex seperti \x47 menjadi karakter asli
 * Cara pakai: php decode_hex_obfuscation.php file.php
 * Contoh: php decode_hex_obfuscation.php barcodeproses.php 
 */

function decodeHexObfuscation($content) {
    // Decode hexadecimal sequences like \x47\x4c\x4f\x42\x41\x4c\x53
    $pattern = '/\\\\x([0-9a-fA-F]{2})/';
    $decoded = preg_replace_callback($pattern, function($matches) {
        return chr(hexdec($matches[1]));
    }, $content);
    
    return $decoded;
}

function deobfuscateVariables($content) {
    // Pattern untuk mendeteksi variable obfuscation seperti ${"\x47\x4c\x4f\x42\x41\x4c\x53"}["key"]
    $pattern = '/\$\{"([^"]+)"\}\["([^"]+)"\]\s*=\s*"([^"]+)";/';
    
    $variables = [];
    
    // Extract semua variable assignments
    preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $globalKey = decodeHexObfuscation($match[1]);
        $arrayKey = decodeHexObfuscation($match[2]);
        $value = decodeHexObfuscation($match[3]);
        
        if ($globalKey === 'GLOBALS') {
            $variables[$arrayKey] = $value;
        }
    }
    
    return $variables;
}

function reconstructCode($content) {
    // Decode hex sequences
    $decoded = decodeHexObfuscation($content);
    
    // Extract variables
    $variables = deobfuscateVariables($content);
    
    // Replace obfuscated variable references with actual variable names
    foreach ($variables as $key => $varName) {
        // Replace ${GLOBALS["key"]} with $varName
        $pattern = '/\$\{\$\{"GLOBALS"\}\["' . preg_quote($key, '/') . '"\]\}/';
        $decoded = preg_replace($pattern, '$' . $varName, $decoded);
        
        // Also replace direct GLOBALS references
        $pattern = '/\$\{"GLOBALS"\}\["' . preg_quote($key, '/') . '"\]/';
        $decoded = preg_replace($pattern, '$' . $varName, $decoded);
    }
    
    // Clean up remaining hex sequences
    $decoded = decodeHexObfuscation($decoded);
    
    return $decoded;
}

// Main execution
if ($argc < 2) {
    echo "Usage: php decode_hex_obfuscation.php <input_file> [output_file]\n";
    exit(1);
}

$inputFile = $argv[1];
$outputFile = isset($argv[2]) ? $argv[2] : str_replace('.php', '_decoded.php', $inputFile);

if (!file_exists($inputFile)) {
    echo "Error: File '$inputFile' not found.\n";
    exit(1);
}

$content = file_get_contents($inputFile);

echo "Decoding hex obfuscation from: $inputFile\n";
echo "Original file size: " . strlen($content) . " bytes\n";

// Decode the content
$decodedContent = reconstructCode($content);

echo "Decoded file size: " . strlen($decodedContent) . " bytes\n";

// Save decoded content
file_put_contents($outputFile, $decodedContent);

echo "Decoded content saved to: $outputFile\n";
echo "\nFirst 500 characters of decoded content:\n";
echo str_repeat('-', 50) . "\n";
echo substr($decodedContent, 0, 500) . "\n";
echo str_repeat('-', 50) . "\n";

?>
