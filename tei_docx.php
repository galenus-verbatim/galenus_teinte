<?php declare(strict_types=1);

include_once(__DIR__ . '/vendor/autoload.php');

use Psr\Log\LogLevel;
use Oeuvres\Kit\{Filesys, Log, LoggerCli, Xt};
use Oeuvres\Teinte\Format\{Tei};
use Oeuvres\Xsl\{Xpack};

Log::setLogger(new LoggerCli(LogLevel::DEBUG));

if (!isset($argv[2])) {
    die("usage: php tei_docx.php file_list.txt dst_dir");
}
$list_file = $argv[1];
$lines = file($list_file);
// destination directory for docx files
$dst_dir = rtrim($argv[2], '/\\').'/';
Filesys::mkdir($dst_dir);

$tei = new Tei();
foreach($lines as $tei_file) {
    $tei_file = trim($tei_file);
    if (!$tei_file) continue;
    if ($tei_file[0] == '#') continue;
    $dst_file = $dst_dir . pathinfo($tei_file, PATHINFO_FILENAME) . '.docx';
    if (!file_exists($tei_file)) {
        Log::warning($tei_file . " 404 File not found");
        continue;
    }
    if (!is_file($tei_file)) {
        Log::warning($tei_file . " is not a file");
        continue;
    }

    Log::info($tei_file . " -> " . $dst_file);
    $tei->load($tei_file);
    $tei->toUri('docx', $dst_file);
}



