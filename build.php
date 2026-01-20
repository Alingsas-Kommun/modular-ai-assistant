#!/usr/bin/env php
<?php
/**
 * Build script for Modular AI Assistant WordPress Plugin
 * 
 * Builds assets and optionally cleans up development files.
 * 
 * Usage:
 *   php build.php              - Build assets (no cleanup in dev environment)
 *   php build.php --cleanup    - Build assets and remove development files (explicit)
 * 
 * Note: Cleanup runs automatically when installed via Composer
 */

// Only allow run from CLI
if (php_sapi_name() !== 'cli') {
    exit(0);
}

$args = $argv ?? [];
$explicitCleanup = in_array('--cleanup', $args, true);

// Detect if running from Composer (installed in vendor directory)
$isComposerInstall = isComposerInstall();

// Only cleanup when installed via Composer OR explicitly requested
$cleanup = $isComposerInstall || $explicitCleanup;

$pluginName = basename(dirname(__FILE__));
$packageJson = __DIR__ . '/package.json';
$packageLockJson = __DIR__ . '/package-lock.json';

// Check if package.json exists
if (!file_exists($packageJson)) {
    echo "Skipping asset build - package.json not found\n";
    exit(0);
}

// Check if Node.js is available
$nodePath = trim(shell_exec('command -v node 2>/dev/null') ?: '');
if (empty($nodePath)) {
    echo "Warning: Node.js not found. Assets will not be built.\n";
    echo "Please install Node.js and run: npm install && npm run build\n";
    exit(0);
}

// Build commands
$buildCommands = [];

// Install npm dependencies
if (file_exists($packageLockJson)) {
    $buildCommands[] = 'npm ci --no-progress --no-audit';
} else {
    $buildCommands[] = 'npm install --no-progress --no-audit';
}

// Build assets
$buildCommands[] = 'npm run build';

// Execute build commands
foreach ($buildCommands as $command) {
    $commandName = explode(' ', $command)[0];
    echo "---- Running '$commandName' for $pluginName ----\n";
    
    $timeStart = microtime(true);
    $exitCode = executeCommand($command);
    $buildTime = round(microtime(true) - $timeStart, 2);
    
    if ($exitCode > 0) {
        echo "Error: Command '$command' failed with exit code $exitCode\n";
        exit($exitCode);
    }
    
    echo "---- Completed '$commandName' in {$buildTime}s ----\n\n";
}

echo "Assets built successfully!\n";

// Cleanup development files if requested
if ($cleanup) {
    if ($isComposerInstall) {
        echo "\n---- Cleaning up development files (Composer installation detected) ----\n";
    } else {
        echo "\n---- Cleaning up development files (explicit --cleanup flag) ----\n";
    }
    
    $removables = [
        '.git',
        '.gitignore',
        '.gitattributes',
        'build.php',
        '.npmrc',
        'composer.json',
        'composer.lock',
        '.env.example',
        'package-lock.json',
        'package.json',
        'vite.config.js',
        'node_modules',
        'translation-fix.js',
    ];
    
    foreach ($removables as $removable) {
        $path = __DIR__ . '/' . $removable;
        if (file_exists($path) || is_dir($path)) {
            echo "Removing: $removable\n";
            removePath($path);
        }
    }
    
    echo "Cleanup completed!\n";
}

/**
 * Detect if script is running from a Composer installation
 * 
 * Checks for:
 * - vendor/ directory (standard Composer)
 * - plugins/ directory (WordPress plugin installation)
 * - Absence of .git directory (indicates installed package, not dev repo)
 * 
 * @return bool True if installed via Composer
 */
function isComposerInstall(): bool
{
    $scriptPath = __DIR__;
    $realPath = realpath($scriptPath);
    
    // Check if path contains 'vendor/' which indicates standard Composer installation
    if (strpos($scriptPath, '/vendor/') !== false || strpos($scriptPath, '\\vendor\\') !== false) {
        return true;
    }
    
    // Check if path contains 'plugins/' which indicates WordPress plugin installation
    if (strpos($scriptPath, '/plugins/') !== false || strpos($scriptPath, '\\plugins\\') !== false ||
        ($realPath && (strpos($realPath, '/plugins/') !== false || strpos($realPath, '\\plugins\\') !== false))) {
        
        // Additional check: if .git exists, it's likely the dev repo, not an install
        $gitPath = __DIR__ . '/.git';
        if (!is_dir($gitPath) && !file_exists($gitPath)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Execute a shell command with live output
 * 
 * @param string $command Command to execute
 * @return int Exit code
 */
function executeCommand(string $command): int
{
    $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    
    if ($isWindows) {
        $fullCommand = "cmd /v:on /c \"$command 2>&1 & echo Exit status : !ErrorLevel!\"";
    } else {
        $fullCommand = "$command 2>&1 ; echo Exit status : $?";
    }
    
    $proc = popen($fullCommand, 'r');
    if (!$proc) {
        return 1;
    }
    
    $completeOutput = '';
    
    while (!feof($proc)) {
        $output = fread($proc, 4096);
        if ($output !== false) {
            echo $output;
            $completeOutput .= $output;
            @flush();
        }
    }
    
    pclose($proc);
    
    // Extract exit code from output
    preg_match('/Exit status : (\d+)$/', $completeOutput, $matches);
    
    return isset($matches[1]) ? (int)$matches[1] : 0;
}

/**
 * Remove a file or directory recursively
 * 
 * @param string $path Path to remove
 * @return void
 */
function removePath(string $path): void
{
    if (is_dir($path)) {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            shell_exec("rmdir /s /q " . escapeshellarg($path) . " 2>nul");
        } else {
            shell_exec("rm -rf " . escapeshellarg($path) . " 2>/dev/null");
        }
    } else {
        @unlink($path);
    }
}