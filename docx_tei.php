<?php declare(strict_types=1);

include_once(__DIR__ . '/vendor/autoload.php');

use Psr\Log\LogLevel;
use Oeuvres\Kit\{Filesys, Log, LoggerCli, Xt};
use Oeuvres\Teinte\Format\{Docx};
use Oeuvres\Xsl\{Xpack};

Log::setLogger(new LoggerCli(LogLevel::DEBUG));

if (!isset($argv[1])) {
    die("usage: php docx_tei.php examples/*.docx");
}
$docx_glob = $argv[1];
// destination directory for tei files
$dst_dir = __DIR__ . '/out/';
Filesys::mkdir($dst_dir);

$source = new Docx();
// local xml template
$source->user_template(__DIR__ . '/galenus_tmpl.xml');
// regex program to insert
$source->user_pcre(__DIR__ . '/galenus_pcre.tsv');

foreach (glob($docx_glob) as $docx_file) {
    $src_name = pathinfo($docx_file, PATHINFO_FILENAME);
    $dst_file = $dst_dir. $src_name .'.xml';
    Log::info($docx_file . " > " . $dst_file);
    $source->load($docx_file);
    $source->tei();

    // finalize with personal xslt
    $xml = Xt::transformToXml(
        __DIR__ . '/galenus.xsl',
        $source->dom(),
        ['filename' => $src_name]
    );
    file_put_contents($dst_file, $xml);
}
