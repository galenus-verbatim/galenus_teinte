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
// drop $argv[0], $argv[1…] should be file
array_shift($argv);
// destination directory for tei files
$dst_dir = __DIR__ . '/out/';
Filesys::mkdir($dst_dir);

$source = new Docx();
// local xml template
$source->user_template(__DIR__ . '/galenus_tmpl_lat.xml');
// regex program to insert
$source->user_pcre(__DIR__ . '/galenus_pcre.tsv');

// loop on arguments to get files of globs
foreach ($argv as $glob) {
    foreach (glob($glob) as $docx_file) {
        $src_name = pathinfo($docx_file, PATHINFO_FILENAME);
        $dst_file = $dst_dir. $src_name .'.xml';
        Log::info($docx_file . " > " . $dst_file);
        $source->load($docx_file);
        // for debug
        $source->pkg(); // open the docx
        $source->teilike(); // apply a first tei layer
        // file_put_contents($dst_dir. $src_name .'_teilike.xml', $source->xml());
        $source->pcre(); // apply regex, custom re may break XML
        // for debug write this step
        // file_put_contents($dst_dir. $src_name .'_pcre.xml', $source->xml());
        $source->tmpl();
        // finalize with personal xslt
        $xml = Xt::transformToXml(
            __DIR__ . '/galenusgrc.xsl',
            $source->dom(),
            ['filename' => $src_name]
        );
        file_put_contents($dst_file, $xml);
    }
}
