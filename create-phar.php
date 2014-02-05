<?php
use Symfony\Component\Finder\Finder;

include 'vendor/autoload.php';
$srcRoot = __DIR__;
$buildRoot = __DIR__;
  
$pharFile = 'evapandoc.phar';

function addFile($phar, $file)
{
    echo "$file \n";
    $path = strtr(str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $file->getRealPath()), '\\', '/');
    echo "$path\n";
    $content = file_get_contents($file);
    $phar->addFromString($path, $content);
}


$phar = new \Phar($pharFile, 0, 'composer.phar');
//$phar->setSignatureAlgorithm(\Phar::SHA1);
$phar->startBuffering();

$finder = new Finder();
$finder->files()->ignoreVCS(true)->name('*.php')->in(__DIR__);

foreach ($finder as $file) {
    addFile($phar, $file);
}

$phar->setDefaultStub('watcher.php', 'watcher.php');
$phar->stopBuffering();
