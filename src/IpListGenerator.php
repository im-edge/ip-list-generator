<?php

namespace IMEdge\IpListGenerator;

use Generator;
use IMEdge\Config\Settings;

interface IpListGenerator
{
    public function __construct(Settings $settings);
    public function generate(): Generator;
}
