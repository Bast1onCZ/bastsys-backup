<?php

namespace BastSys\BackupBundle\Service;

/**
 * Class BackupService
 * @package BastSys\BackupBundle\Service
 * @author mirkl
 */
class BackupService
{
    /**
     * @var string
     */
    private $directory;
    /**
     * @var int
     */
    private $maxBackups;

    /**
     * BackupService constructor.
     * @param string $directory
     * @param int $maxBackups
     */
    public function __construct(string $directory, int $maxBackups)
    {
        $this->directory = $directory;
        $this->maxBackups = $maxBackups;
    }
}
