<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
$allowedOrigin = getenv('SHOP_ORIGIN') ?: ($_SERVER['HTTP_ORIGIN'] ?? '');
if ($allowedOrigin) { header('Access-Control-Allow-Origin: ' . $allowedOrigin); }
header('Vary: Origin');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Accept, Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$apiKey = getenv('LZPEDIA_API_KEY');
$baseUrl = rtrim(getenv('LZPEDIA_BASE_URL') ?: 'https://app.lzpedia.my.id/api', '/');
if (!$apiKey) { http_response_code(500); echo json_encode(['success'=>false,'error'=>'LZPEDIA_API_KEY belum dikonfigurasi di server']); exit; }

$action = (string)($_GET['action'] ?? '');
$amount = filter_var($_GET['amount'] ?? null, FILTER_VALIDATE_INT);
$invoiceId = trim((string)($_GET['invoice_id'] ?? ''));
if ($action === 'create') {
    if (!$amount || $amount <= 0) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'Amount harus berupa bilangan bulat lebih dari 0']); exit; }
    $url = $baseUrl . '/invoice?' . http_build_query(['apikey'=>$apiKey,'amount'=>$amount]);
} elseif ($action === 'status') {
    if ($invoiceId === '') { http_response_code(400); echo json_encode(['success'=>false,'error'=>'invoice_id wajib diisi']); exit; }
    $url = $baseUrl . '/invoice/status?' . http_build_query(['apikey'=>$apiKey,'invoice_id'=>$invoiceId]);
} else {
    http_response_code(400); echo json_encode(['success'=>false,'error'=>'Action tidak valid']); exit;
}

$ch = curl_init($url);
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_SSL_VERIFYPEER=>true, CURLOPT_SSL_VERIFYHOST=>2, CURLOPT_TIMEOUT=>30, CURLOPT_FOLLOWLOCATION=>false, CURLOPT_HTTPHEADER=>['Accept: application/json'], CURLOPT_USERAGENT=>'JOELL-SHOP/3.0']);
$body = curl_exec($ch); $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); $curlError = curl_error($ch); curl_close($ch);
if ($body === false || $curlError) { http_response_code(502); echo json_encode(['success'=>false,'error'=>'Gateway pembayaran tidak dapat dihubungi']); exit; }
$data = json_decode($body, true);
if (!is_array($data)) { http_response_code(502); echo json_encode(['success'=>false,'error'=>'Respons gateway bukan JSON yang valid']); exit; }
if ($httpCode < 200 || $httpCode >= 300) { http_response_code($httpCode ?: 502); echo json_encode(['success'=>false,'error'=>'Gateway mengembalikan HTTP ' . $httpCode, 'details'=>$data]); exit; }
echo json_encode($data, JSON_UNESCAPED_SLASHES);
