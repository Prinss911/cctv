# Start PHP development server
# Port 8081 konsisten dengan docker-compose.yml dan dokumentasi
$port = if ($args[0]) { $args[0] } else { "8081" }
$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path

Write-Host "Starting PHP dev server on http://localhost:$port" -ForegroundColor Green
php -S "0.0.0.0:$port" -t "$projectRoot\public"
