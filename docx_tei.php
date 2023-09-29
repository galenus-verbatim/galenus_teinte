<?php declare(strict_types=1);

include_once(__DIR__ . '/vendor/autoload.php');

use Psr\Log\LogLevel;
use Oeuvres\Kit\{Filesys, Log, Xt};
use Oeuvres\Kit\Logger\{LoggerCli};
use Oeuvres\Teinte\Format\{Docx};
use Oeuvres\Xsl\{Xpack};

Log::setLogger(new LoggerCli(LogLevel::DEBUG));

if (!isset($argv[1])) {
    die("usage: php docx_tei.php examples/*.docx");
}
// drop $argv[0], $argv[1…] should be file
array_shift($argv);
// destination directory for tei files
$tmp_dir = __DIR__ . '/out/';
Filesys::mkdir($tmp_dir);

$source = new Docx();
// local xml template
$source->user_template(__DIR__ . '/galenus_tmpl_lat.xml');
// regex program to insert
$source->user_pcre(__DIR__ . '/galenus_pcre.tsv');
$force = true;
// loop on arguments to get files of globs
foreach ($argv as $glob) {
    Log::info($glob);
    foreach (glob($glob) as $docx_file) {
        $src_name = pathinfo($docx_file, PATHINFO_FILENAME);
        $split = explode('.', $src_name);
        $dst_file = dirname(__DIR__) . '/galenus_cts/data/' . $split[0] . '/' . $split[1] . '/' . $src_name .'.xml';
        if (!$force && file_exists($dst_file) && filemtime($docx_file) < filemtime($dst_file)) {
            continue;
        }
        Filesys::mkdir(dirname($dst_file));
        Log::info($docx_file . " > " . $dst_file);
        $source->load($docx_file);
        // for debug
        $source->pkg(); // open the docx
        $source->teilike(); // apply a first tei layer
        file_put_contents($tmp_dir . $src_name .'_teilike.xml', $source->tei());
        $source->pcre(); // apply regex, custom re may break XML
        // for debug write this step
        file_put_contents($tmp_dir . $src_name .'_pcre.xml', $source->tei());
        $source->tmpl();
        $grc_file = dirname(__DIR__) . '/galenus_cts/data/' . $split[0] . '/' . $split[1] . '/' . str_replace('verbatim-lat', '1st1K-grc', $src_name) .'.xml';
        if (!file_exists($grc_file)) {
            $grc_file = dirname(__DIR__) . '/galenus_cts/data/' . $split[0] . '/' . $split[1] . '/' . str_replace('verbatim-lat', 'verbatim-grc', $src_name)  .'.xml';
        }
        if (!file_exists($grc_file)) {
            echo "[404] $grc_file\n";
            unlink($dst_file);
            continue;
        }
        // finalize with personal xslt
        $xml = Xt::transformToXml(
            __DIR__ . '/galenus_lat.xsl',
            $source->teiDoc(),
            [
                'filename' => $src_name,
                'grc_file' => 'file:///' . $grc_file,
            ]
        );
        file_put_contents($dst_file, $xml);
    }
}
