<?php
/**
 * Simple test script for the Jurnal API
 * This script can be used to test the API endpoints before integrating with WhatsApp bot
 */

// Base URL of your application
$base_url = 'http://prestasi.test/api/';

// API Key for authentication
$api_key = 'whatsapp_bot_key_2024';

/**
 * Function to make API requests
 */
function make_request($url, $method = 'GET', $data = null) {
    global $api_key;
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-API-Key: ' . $api_key
    ]);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status_code' => $http_code,
        'response' => json_decode($response, true)
    ];
}

// Test 1: Get list of guru
echo "Test 1: Get list of guru\n";
$result = make_request($base_url . 'guru/list');
echo "Status Code: " . $result['status_code'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Get list of kelas
echo "Test 2: Get list of kelas\n";
$result = make_request($base_url . 'kelas/list');
echo "Status Code: " . $result['status_code'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n\n";

// Test 3: Get list of mapel
echo "Test 3: Get list of mapel\n";
$result = make_request($base_url . 'mapel/list');
echo "Status Code: " . $result['status_code'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n\n";

// Test 4: Create a new jurnal
echo "Test 4: Create a new jurnal\n";
$jurnal_data = [
    'tanggal' => date('Y-m-d'),
    'id_guru' => 1, // Assuming ID 1 exists
    'id_kelas' => 1, // Assuming ID 1 exists
    'id_mapel' => 1, // Assuming ID 1 exists
    'materi' => 'Test API - Pembelajaran Matematika',
    'jumlah_siswa' => 20,
    'keterangan' => 'Ini adalah test jurnal dari API',
    'created_by' => 1
];

$result = make_request($base_url . 'jurnal/create', 'POST', $jurnal_data);
echo "Status Code: " . $result['status_code'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n\n";

// Test 5: Get all jurnal
echo "Test 5: Get all jurnal\n";
$result = make_request($base_url . 'jurnal/list?limit=5');
echo "Status Code: " . $result['status_code'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n\n";

// Test 6: Search jurnal
echo "Test 6: Search jurnal\n";
$result = make_request($base_url . 'jurnal/search?keyword=test');
echo "Status Code: " . $result['status_code'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n\n";

echo "API testing completed!\n";
?>