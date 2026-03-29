param(
    [string]$ContainerName = "wordpress-migration-20260320-222402-wordpress-1",
    [string]$RepoUrl = "https://github.com/Big-Noah/grass.git",
    [string]$RepoBranch = "main",
    [string]$RepoPath = "/opt/grass-repo"
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

function Invoke-DockerSh {
    param([string]$Script)

    docker exec $ContainerName sh -lc $Script | Out-Host
}

Write-Host "Checking container availability..."
docker inspect $ContainerName | Out-Null

Write-Host "Checking git inside container..."
$gitCheck = docker exec $ContainerName sh -lc "command -v git >/dev/null 2>&1"
if ($LASTEXITCODE -ne 0) {
    throw "git is not installed in container $ContainerName. Install git in the container image or container first."
}

Write-Host "Preparing repository checkout..."
Invoke-DockerSh @"
set -eu
mkdir -p '$RepoPath'
if [ ! -d '$RepoPath/.git' ]; then
  rm -rf '$RepoPath'
  git clone --branch '$RepoBranch' --single-branch '$RepoUrl' '$RepoPath'
else
  git -C '$RepoPath' fetch origin '$RepoBranch'
  git -C '$RepoPath' checkout '$RepoBranch'
  git -C '$RepoPath' reset --hard "origin/$RepoBranch"
fi
"@

Write-Host "Publishing code directories into wp-content..."
Invoke-DockerSh @"
set -eu
rm -rf /var/www/html/wp-content/themes
rm -rf /var/www/html/wp-content/plugins
rm -rf /var/www/html/wp-content/mu-plugins
mkdir -p /var/www/html/wp-content/themes
mkdir -p /var/www/html/wp-content/plugins
mkdir -p /var/www/html/wp-content/mu-plugins
cp -a '$RepoPath/themes/.' /var/www/html/wp-content/themes/
cp -a '$RepoPath/plugins/.' /var/www/html/wp-content/plugins/
cp -a '$RepoPath/mu-plugins/.' /var/www/html/wp-content/mu-plugins/
if [ -f '$RepoPath/index.php' ]; then
  cp '$RepoPath/index.php' /var/www/html/wp-content/index.php
fi
if [ -f '$RepoPath/object-cache.php' ]; then
  cp '$RepoPath/object-cache.php' /var/www/html/wp-content/object-cache.php
fi
chown -R www-data:www-data /var/www/html/wp-content
"@

$commit = docker exec $ContainerName sh -lc "git -C '$RepoPath' rev-parse --short HEAD"
if ($LASTEXITCODE -ne 0) {
    throw "Deployment finished, but failed to read the deployed commit hash."
}

Write-Host ""
Write-Host "Deployment complete."
Write-Host "Container: $ContainerName"
Write-Host "Repository: $RepoUrl"
Write-Host "Commit: $($commit.Trim())"
