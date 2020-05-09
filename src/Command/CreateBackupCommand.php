<?php

namespace BastSys\BackupBundle\Command;

use BastSys\BackupBundle\Exception\InvalidBackupsException;
use BastSys\BackupBundle\Structure\DatabaseUrl;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @CronJob("0 0 * * *")
 *
 * Class CreateBackupCommand
 * @package BastSys\BackupBundle\Command
 * @author mirkl
 */
class CreateBackupCommand extends Command
{
    const BACKUP_DIR_FORMAT = 'Y-m-d H-i-s';

    /**
     * @var string
     */
    protected static $defaultName = 'backup:create';

    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var string
     */
    private $directory;
    /**
     * @var int
     */
    private $maxBackups;
    /**
     * @var DatabaseUrl|null
     */
    private $database;
    /**
     * @var string|null
     */
    private $filesDirectory;

    /**
     * CreateBackupCommand constructor.
     * @param Filesystem $filesystem
     * @param string $directory
     * @param int $maxBackups
     * @param string|null $database
     * @param string|null $filesDirectory
     */
    public function __construct(Filesystem $filesystem, string $directory, int $maxBackups, ?string $database, ?string $filesDirectory)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
        $this->directory = $directory;
        $this->maxBackups = $maxBackups;
        $this->database = $database ? new DatabaseUrl($database) : null;
        if($this->database && $this->database->getType() !== 'mysql') {
            throw new \InvalidArgumentException('Unsupported database type');
        }

        $this->filesDirectory = $filesDirectory;
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setDescription('Creates a backup');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws InvalidBackupsException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Beginning backup create ...');
        if(!file_exists($this->directory)) {
            mkdir($this->directory);
            $output->writeln("Created backup directory '$this->directory'");
        }

        $currentBackupDirs = scandir($this->directory, SCANDIR_SORT_ASCENDING); // older backups first
        $backups = [];
        foreach($currentBackupDirs as $backupDir) {
            if($backupDir === '.' || $backupDir === '..') {
                continue;
            }

            if(!\DateTime::createFromFormat(self::BACKUP_DIR_FORMAT, $backupDir)) {
                throw new InvalidBackupsException("Backup directory contains an invalid backup folder '$backupDir'");
            }

            $backups[] = $backupDir;
        }

        $currentBackupName = (new \DateTime())->format(self::BACKUP_DIR_FORMAT);
        $backupDirectory = "$this->directory/$currentBackupName";

        mkdir($backupDirectory);
        $output->writeln("Created backup '$currentBackupName'");

        if($this->database) {
            $backupFile = "$backupDirectory/database.sql";
            $user = $this->database->getUser();
            $password = $this->database->getPassword();
            $passwordPart = $password ? "--password=$password" : '';
            $database = $this->database->getDatabase();
            $host = $this->database->getHost();

            exec("mysqldump $database > \"$backupFile\" --user=$user --host=$host $passwordPart --single-transaction");
            $output->writeln("Created database backup");
        }

        if($this->filesDirectory) {
            $filesDirectoryParts = explode('/', $this->filesDirectory);
            $filesDirectoryName = $filesDirectoryParts[count($filesDirectoryParts) - 1];
            $this->filesystem->mirror($this->filesDirectory, "$backupDirectory/$filesDirectoryName");
            $output->writeln('Created files directory backup');
        }

        $backups[] = $currentBackupName;
        $backupsToRemove = count($backups) - $this->maxBackups;

        for($i = 0; $i < $backupsToRemove; $i++) {
            $backupName = $backups[$i];
            $this->filesystem->remove("$this->directory/$backupName");
            $output->writeln("Removed old backup '$backupName'");
        }

        $output->writeln('Backup create completed.');

        return 0;
    }

}
