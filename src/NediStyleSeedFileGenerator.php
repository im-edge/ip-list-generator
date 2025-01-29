<?php

namespace IMEdge\IpListGenerator;

use Generator;
use IMEdge\Config\Settings;
use InvalidArgumentException;

class NediStyleSeedFileGenerator implements IpListGenerator
{
    protected string $fileContent;

    public function __construct(Settings $settings)
    {
        $seed = $settings->getRequired('seed');
        if (is_string($seed)) {
            $this->fileContent = $seed;
        } else {
            throw new InvalidArgumentException('Seed must be a string');
        }
    }

    public function generate(): Generator
    {
        $lines = preg_split('/\r?\n/', $this->fileContent, -1, PREG_SPLIT_NO_EMPTY);
        if ($lines === false) {
            throw new InvalidArgumentException('Invalid seed file: ' . $this->fileContent);
        }
        $ips = [];
        $i = 0;
        while (array_key_exists($i, $lines)) {
            $line = $lines[$i];
            $i++;
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if ($line[0] === ';') {
                continue;
            }
            if ((strpos($line, '-')) === false) {
                if (self::isIp($line)) {
                    yield $line;
                } else {
                    throw new InvalidArgumentException("Invalid IP: $line");
                }
            } else {
                array_splice($lines, $i, 0, self::explodeIps($line));
            }
        }

        return $ips;
    }

    protected static function isIp(string $string): bool
    {
        return @inet_pton($string) !== false;
    }

    /**
     * @param string $ip
     * @return string[]
     */
    protected static function explodeIps(string $ip): array
    {
        $ips = [];
        if (!preg_match('/^(.*?)(\d+)-(\d+)(.*?)$/', $ip, $match)) {
            throw new InvalidArgumentException("Invalid IP range: $ip");
        }

        foreach (range((int) $match[2], (int) $match[3]) as $part) {
            $ips[] = $match[1] . $part . $match[4];
        }

        return $ips;
    }
}
