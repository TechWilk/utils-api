<?php

declare(strict_types=1);

namespace TechWilk\PhpTools;

interface SettingsInterface
{
    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key = '');
}