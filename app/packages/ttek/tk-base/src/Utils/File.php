<?php

namespace Tk\Utils;

class File
{

    /**
     * get the maximum upload size for a form in bytes
     */
    public static function getMaxUploadBytes(): int
    {
        return min(
            self::string2Bytes((string)ini_get('upload_max_filesize')),
            self::string2Bytes((string)ini_get('post_max_size'))
        );
    }

    /**
     * Get the bytes from a string like 40M, 10T, 100K
     */
    public static function string2Bytes(string $str): int
    {
        $sUnit = substr($str, -1);
        $iSize = (int)substr($str, 0, -1);
        switch (strtoupper($sUnit)) {
        case 'Y' :
            $iSize *= 1024; // Yotta
        case 'Z' :
            $iSize *= 1024; // Zetta
        case 'E' :
            $iSize *= 1024; // Exa
        case 'P' :
            $iSize *= 1024; // Peta
        case 'T' :
            $iSize *= 1024; // Tera
        case 'G' :
            $iSize *= 1024; // Giga
        case 'M' :
            $iSize *= 1024; // Mega
        case 'K' :
            $iSize *= 1024; // kilo
        }
        return $iSize;
    }

    /**
     * Convert a value from bytes to a human-readable value
     *
     * @author http://php-pdb.sourceforge.net/samples/viewSource.php?file=twister.php
     */
    public static function bytes2String(int $bytes, int $round = 2): string
    {
        $tags = ['b', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $index = 0;
        while ($bytes > 999 && isset($tags[$index + 1])) {
            $bytes /= 1024;
            $index++;
        }
        $rounder = 1;
        if ($bytes < 10) {
            $rounder *= 10;
        }
        if ($bytes < 100) {
            $rounder *= 10;
        }
        $bytes *= $rounder;
        settype($bytes, 'integer');
        $bytes /= $rounder;
        if ($round > 0) {
            $bytes = round($bytes, $round);
            return  sprintf('%.'.$round.'f %s', $bytes, $tags[$index]);
        } else {
            return  sprintf('%s %s', $bytes, $tags[$index]);
        }
    }
}
