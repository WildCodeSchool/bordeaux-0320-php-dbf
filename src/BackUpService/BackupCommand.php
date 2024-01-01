<?php


namespace App\BackUpService;


use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use \DateTime;
use BackupManager\Manager;
use BackupManager\Filesystems\Destination;

class BackupCommand extends Command
{
    private string $folder;
    private $dropboxService;
    /**
     * @var Manager
     */
    private Manager $backupManager;

    public function __construct(KernelInterface $kernel, DropBoxService $dropboxService, Manager $backupManager)
    {
        $this->folder = $kernel->getProjectDir() . '/backup';
        $this->dropboxService = $dropboxService;
        $this->backupManager = $backupManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('easyauto:backup');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if(is_file($this->folder . '/backup.sql.gz')) {
            unlink($this->folder . '/backup.sql.gz');
        }

        $io->writeln($this->deleteFtpBackup());

        $this->backupManager->makeBackup()->run('development', [new Destination('ftp', 'backup.sql')], 'gzip');
        $io->writeln('Sauvegarde FTP OK');
        $this->backupManager->makeBackup()->run('development', [new Destination('local', 'backup.sql')], 'gzip');
        $io->writeln('Sauvegarde locale OK');

        return Command::SUCCESS;
    }

    private function deleteFtpBackup()
    {
        $file = $_SERVER['FTP_FOLDER'] . '/backup.sql.gz';
        $connection = ftp_connect($_SERVER['FTP_HOST']);
        $login_result = ftp_login($connection, $_SERVER['FTP_USER'], $_SERVER['FTP_PASSWORD']);

        if (ftp_delete($connection, $file)) {
            ftp_close($connection);
            return "sauvegarde effacée avec succès";
        } else {
            ftp_close($connection);
            return "Impossible d'effacer le fichier";
        }

    }

}
