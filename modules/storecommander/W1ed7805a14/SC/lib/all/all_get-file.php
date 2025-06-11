<?php

if (!defined('STORE_COMMANDER')
    || STORE_COMMANDER !== 1
    || !Tools::isSubmit('type')
    || !Tools::isSubmit('file'))
{
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$type = Tools::getValue('type', 'import');
switch ($type)
{
    case 'export':
        $initPath = SC_CSV_EXPORT_DIR;
        break;
    case 'import':
    default:
        $initPath = SC_CSV_IMPORT_DIR;
}

$folderPath = Tools::getValue('path');
if ($folderPath)
{
    $folderPath = trim($folderPath).'/';
}
$folderPath = $initPath.$folderPath;
if (!file_exists($folderPath))
{
    exit('Folder not found');
}

$filename = trim(Tools::getValue('file'));
$csvFilesFound = glob($folderPath.'*.csv');
$pathFound = false;

if ($csvFilesFound)
{
    foreach ($csvFilesFound as $path)
    {
        if (basename($path) == $filename)
        {
            $pathFound = $path;
            break;
        }
    }
}

if (!$pathFound)
{
    exit('File not found');
}

/* Detect mime content type */
$mime_type = false;
if (function_exists('finfo_open'))
{
    $finfo = @finfo_open(FILEINFO_MIME);
    $mime_type = @finfo_file($finfo, $pathFound);
    @finfo_close($finfo);
}
elseif (function_exists('mime_content_type'))
{
    $mime_type = @mime_content_type($pathFound);
}
elseif (function_exists('exec'))
{
    $mime_type = trim(@exec('file -bi '.escapeshellarg($pathFound)));
}

/* Set headers for download */
header('Content-Transfer-Encoding: binary');
if ($mime_type)
{
    header('Content-Type: '.$mime_type);
}
header('Content-Length: '.filesize($pathFound));
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($pathFound);
exit;
