<?php
spl_autoload_register(function ($className)
{
    $prefix = "Riverside\Express\\";

    $length = strlen($prefix);
    if (strncmp($prefix, $className, $length) !== 0)
    {
        return;
    }

    $filename = sprintf("%s%s%s.php",
        __DIR__,
        DIRECTORY_SEPARATOR,
        str_replace('\\', DIRECTORY_SEPARATOR, substr($className, $length)));

    if (file_exists($filename))
    {
        require $filename;
    }
});