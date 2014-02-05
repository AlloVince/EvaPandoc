<?php
require __DIR__ . '/vendor/autoload.php';
use Symfony\Component\Process\Process;

//$symbol = isset($argv[1]) ? $argv[1] : 'xauusd'; //000001
//$target = isset($argv[2]) ? $argv[2] : 'mn'; // 1w  mn 

$dir = getcwd();
define('DS', DIRECTORY_SEPARATOR);

function getFileName($filePath)
{
    $fileExt = explode(".", $filePath);
    array_pop($fileExt);

    return implode('.', $fileExt);
}

function getFileExtension($filePath)
{
    $fileExt = explode(".", $filePath);

    return strtolower(array_pop($fileExt));
}

$times = array();
$round = 0;


while(true) {
    clearstatcache();
    $files = new \GlobIterator($dir . DS . '*.md');
    $i = 0;
    $diff = false;
    if($files) {
        foreach ($files as $file) {
            $i++;
            $srcFile = $file->getPathName();
            $fKey = md5($srcFile);
            $targetFile = getFileName($srcFile) . '.html';
            $fTime = $file->getMTime();
            //echo "time $times[$fKey]\n";
            //echo "ftime $fTime\n";
            //echo "$srcFile\n";
            //echo "$targetFile\n";
            //exit;
            if(isset($times[$fKey]) && $times[$fKey] >= $fTime){
                continue;
            } else {
                $diff = true;
                //echo "Process\n";
                echo "pandoc $srcFile -o $targetFile --webtex\n";
                $process = new Process("pandoc $srcFile -o $targetFile --webtex");
                $process->run(function ($type, $buffer) {
                    if (Process::ERR === $type) {
                        echo 'ERR > ' . $buffer;
                    } else {
                        echo 'OUT > ' . $buffer;
                    }
                });
                file_put_contents($targetFile, '<html><head><link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.css" rel="stylesheet"></head><body class="container">' . file_get_contents($targetFile) . '</body></html>');
                $times[$fKey] = $fTime;
            }
        }

        if(false == $diff) {
            echo "Rount $round processed, No file changes \n";
            sleep(2);
        }

        if($i < 1) {
            exit('No markdown file found');
        }
    } else {
        exit('No markdown file found');
    }
    $round++;
    //if($round == 2)exit;
}


