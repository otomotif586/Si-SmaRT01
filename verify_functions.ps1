# Check ONLY cross-file let/const issues (non-trivial ones)
$jsDir = "public/js"
$jsFiles = @("core","workspace","warga","global-warga","keuangan","agenda","gallery","laporan-iuran","keuangan-global")

# Common names to skip (too generic)
$skip = @("html","i","f","d","a","b","l","w","q","t","m","idx","el","btn","div","opt","row","csv","fd","id","img","now","day","dt","res","container","filtered","data","blob","link","year","line","index","start","end","total","cards","items")

Write-Host "=== CROSS-FILE let/const THAT MATTER ==="
foreach ($f in $jsFiles) {
    $lines = Get-Content "$jsDir/$f.js"
    for ($i = 0; $i -lt $lines.Count; $i++) {
        $line = $lines[$i].Trim()
        # Only top-level (no leading spaces except minimal)
        if ($lines[$i] -match '^(let|const)\s+(\w+)') {
            $varName = $Matches[2]
            $type = $Matches[1]
            if ($skip -contains $varName) { continue }
            foreach ($other in $jsFiles) {
                if ($other -eq $f) { continue }
                $otherContent = Get-Content "$jsDir/$other.js" -Raw
                if ($otherContent -match "\b$varName\b") {
                    Write-Host "  WARNING: $type $varName (defined in $f.js line $($i+1), used in $other.js)"
                }
            }
        }
    }
}
