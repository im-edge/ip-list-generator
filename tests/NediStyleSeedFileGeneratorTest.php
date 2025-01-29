<?php

namespace IMEdge\Tests\IpListGenerator;

use IMEdge\Config\Settings;
use IMEdge\IpListGenerator\NediStyleSeedFileGenerator;
use PHPUnit\Framework\TestCase;

class NediStyleSeedFileGeneratorTest extends TestCase
{
    public function testSimpleSeedFile(): void
    {
        $file = new NediStyleSeedFileGenerator(Settings::fromSerialization([
            'seed' => "192.0.2.1"
        ]));
        self::assertEquals(['192.0.2.1'], iterator_to_array($file->generate()));
    }

    public function testSeedFileWithRange(): void
    {
        $file = new NediStyleSeedFileGenerator(Settings::fromSerialization([
            'seed' => "192.0.2-4.1"
        ]));
        self::assertEquals(['192.0.2.1', '192.0.3.1', '192.0.4.1'], iterator_to_array($file->generate()));
    }

    public function testSeedFileWithCombinedRanges(): void
    {
        $file = new NediStyleSeedFileGenerator(Settings::fromSerialization([
            'seed' => "192.0.2-4.3-5"
        ]));
        self::assertEquals([
            '192.0.2.3',
            '192.0.2.4',
            '192.0.2.5',
            '192.0.3.3',
            '192.0.3.4',
            '192.0.3.5',
            '192.0.4.3',
            '192.0.4.4',
            '192.0.4.5',
        ], iterator_to_array($file->generate()));
    }

    public function testSeedFileWithMultipleRanges(): void
    {
        $file = new NediStyleSeedFileGenerator(Settings::fromSerialization([
            'seed' => "192.0.2.1-3\n192.0.2-4.3-5\n\n192.0.255.1"
        ]));
        self::assertEquals([
            '192.0.2.1',
            '192.0.2.2',
            '192.0.2.3',
            '192.0.2.3',
            '192.0.2.4',
            '192.0.2.5',
            '192.0.3.3',
            '192.0.3.4',
            '192.0.3.5',
            '192.0.4.3',
            '192.0.4.4',
            '192.0.4.5',
            '192.0.255.1',
        ], iterator_to_array($file->generate()));
    }
}
