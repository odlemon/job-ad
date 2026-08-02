# Media Server Diagnostic Script
# This script checks if the media server is accessible

Write-Host "=== Media Server Diagnostic ===" -ForegroundColor Cyan
Write-Host ""

$baseUrl = "207.180.234.151"
$ports = @(80, 443, 8080, 3000, 3050, 8000)

Write-Host "1. Checking server connectivity..." -ForegroundColor Yellow
$ping = Test-Connection -ComputerName $baseUrl -Count 1 -Quiet
if ($ping) {
    Write-Host "   ✓ Server is reachable (ping successful)" -ForegroundColor Green
} else {
    Write-Host "   ✗ Server is NOT reachable (ping failed)" -ForegroundColor Red
}

Write-Host ""
Write-Host "2. Checking ports..." -ForegroundColor Yellow
foreach ($port in $ports) {
    $test = Test-NetConnection -ComputerName $baseUrl -Port $port -WarningAction SilentlyContinue
    if ($test.TcpTestSucceeded) {
        Write-Host "   ✓ Port $port is OPEN" -ForegroundColor Green
    } else {
        Write-Host "   ✗ Port $port is CLOSED" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "3. Testing HTTP endpoints..." -ForegroundColor Yellow

# Test upload service
try {
    $response = Invoke-WebRequest -Uri "http://${baseUrl}:3050/upload" -Method GET -TimeoutSec 5 -ErrorAction Stop
    Write-Host "   ✓ Upload service (port 3050) is responding" -ForegroundColor Green
    Write-Host "     Status: $($response.StatusCode)" -ForegroundColor Gray
} catch {
    Write-Host "   ✗ Upload service (port 3050) is NOT responding" -ForegroundColor Red
    Write-Host "     Error: $($_.Exception.Message)" -ForegroundColor Gray
}

# Test media server on port 80
try {
    $response = Invoke-WebRequest -Uri "http://${baseUrl}/uploads" -Method GET -TimeoutSec 5 -ErrorAction Stop
    Write-Host "   ✓ Media server (port 80) is responding" -ForegroundColor Green
    Write-Host "     Status: $($response.StatusCode)" -ForegroundColor Gray
} catch {
    Write-Host "   ✗ Media server (port 80) is NOT responding" -ForegroundColor Red
    Write-Host "     Error: $($_.Exception.Message)" -ForegroundColor Gray
}

# Test media server on port 3050 (uploads path)
try {
    $response = Invoke-WebRequest -Uri "http://${baseUrl}:3050/uploads" -Method GET -TimeoutSec 5 -ErrorAction Stop
    Write-Host "   ✓ Media server (port 3050/uploads) is responding" -ForegroundColor Green
    Write-Host "     Status: $($response.StatusCode)" -ForegroundColor Gray
} catch {
    Write-Host "   ✗ Media server (port 3050/uploads) is NOT responding" -ForegroundColor Red
    Write-Host "     Error: $($_.Exception.Message)" -ForegroundColor Gray
}

Write-Host ""
Write-Host "4. Testing sample image URL..." -ForegroundColor Yellow
$sampleImage = "http://${baseUrl}/uploads/profile-photos/download__1_-1771200759492-799364779.jpeg"
try {
    $response = Invoke-WebRequest -Uri $sampleImage -Method HEAD -TimeoutSec 5 -ErrorAction Stop
    Write-Host "   ✓ Sample image is accessible" -ForegroundColor Green
    Write-Host "     Status: $($response.StatusCode)" -ForegroundColor Gray
} catch {
    Write-Host "   ✗ Sample image is NOT accessible" -ForegroundColor Red
    Write-Host "     URL: $sampleImage" -ForegroundColor Gray
    Write-Host "     Error: $($_.Exception.Message)" -ForegroundColor Gray
}

Write-Host ""
Write-Host "=== Diagnostic Complete ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "Recommendations:" -ForegroundColor Yellow
Write-Host "1. If port 80 is closed, check if the media server is running on a different port"
Write-Host "2. If port 3050 is open, the media server might be on the same port (3050)"
Write-Host "3. Check your .env file - MEDIA_BASE_URL might need to be updated"
Write-Host "4. Contact your server administrator to verify the media server configuration"
