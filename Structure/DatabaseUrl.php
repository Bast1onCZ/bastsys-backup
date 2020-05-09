<?php

namespace App\BackupBundle\Structure;

use InvalidArgumentException;

/**
 * Class DatabaseUrl
 * @package App\BackupBundle\Structure
 * @author mirkl
 */
class DatabaseUrl
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $user;
    /**
     * @var string|null
     */
    private $password;
    /**
     * @var string
     */
    private $host;
    /**
     * @var string
     */
    private $database;

    /**
     * DatabaseUrl constructor.
     * @param string $databaseUrl
     */
    public function __construct(string $databaseUrl)
    {
        $matches = [];
        if(!preg_match('/(\w+):\/\/(\w+)(?::(\w+))?@([\w.]+)\/(\w+)/', $databaseUrl, $matches)) {
            throw new InvalidArgumentException('Invalid database url');
        }

        $this->type = $matches[1];
        $this->user = $matches[2];
        $this->password = $matches[3] ?? null;
        $this->host = $matches[4];
        $this->database = $matches[5];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $passwordPart = $this->password ? ":$this->password" : '';
        return "$this->type://$this->user$passwordPart@$this->host/$this->database";
    }
}
