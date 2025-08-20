<?php

namespace Pavliq\LaravelBackupPackage\Interfaces;

interface DumpInterface
{
    public function dump(string $fullPath): string;
}