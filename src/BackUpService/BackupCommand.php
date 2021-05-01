<?php


namespace App\BackUpService;


use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
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

    public function __construct(string $name = null, KernelInterface $kernel, DropBoxService $dropboxService, Manager $backupManager)
    {
        $this->folder = $kernel->getProjectDir() . '/backup';
        $this->dropboxService = $dropboxService;
        $this->backupManager = $backupManager;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('easyauto:backup');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->deleteFtpBackup();
        $this->backupManager->makeBackup()->run('development', [new Destination('ftp', 'backup.sql')], 'gzip');

        return Command::SUCCESS;
    }

    private function deleteFtpBackup()
    {
        $file = $_SERVER['FTP_FOLDER'] . '/backup.sql.gz';
        $conn_id = ftp_connect($_SERVER['FTP_HOST']);
        $login_result = ftp_login($conn_id, $_SERVER['FTP_USER'], $_SERVER['FTP_PASSWORD']);

        if (ftp_delete($conn_id, $file)) {
            ftp_close($conn_id);
            return "sauvegarde effacée avec succès";
        } else {
            ftp_close($conn_id);
            return "Impossible d'effacer le fichier";
        }

    }

}
