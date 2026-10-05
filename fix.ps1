$path = "D:\Amtech\skillnest\resources\panels\src\components\footer\Footer.jsx"
$lines = Get-Content $path
$lines = $lines | ForEach-Object { $_ -replace '"Account"', '"Accounts"' }
Set-Content -Path $path -Value $lines -Encoding UTF8
Write-Host "done"
