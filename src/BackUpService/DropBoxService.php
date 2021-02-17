<?php


namespace App\BackUpService;

use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxFile;
use Kunnu\Dropbox\Models\File;
use Kunnu\Dropbox\Models\MetadataCollection;

class DropBoxService
{
    private $app;
    private $dropbox;

    public function __construct()
    {
        $this->app = new DropboxApp
        (
            $_ENV['DROPBOX_CLIENT'],
            $_ENV['DROPBOX_SECRET'],
            $_ENV['DROPBOX_TOKEN']
        );
        $this->dropbox = new Dropbox($this->app);
    }

    public function getDropbox(): Dropbox
    {
        return $this->dropbox;
    }

    public function sendFile(string $fileUrl, string $folderName = ''): void
    {
        $file = new DropboxFile($fileUrl);
        $this->dropbox->upload($file, '/' . $folderName . $file->getFileName());
    }

    public function uploadFile(string $fileUrl, string $folderName, string $newFileName): void
    {
        $file = new DropboxFile($fileUrl);
        $this->dropbox->upload($file, '/' . $folderName . '/' . $newFileName);
    }

    public function createFolder(string $folderName): void
    {
        $this->dropbox->createFolder('/' . $folderName);
    }

    public function listFiles(string $folderName)
    {
        return $this->dropbox->listFolder('/' . $folderName)->getItems();
    }


    public function folderExist(string $folderName)
    {
        $searchFolder = $this->dropbox->search("/", $folderName, ['start' => 0, 'max_results' => 1]);
        return (count($searchFolder->getItems()) > 0);
    }

    public function removeFile($folderName, $fileName)
    {
        $path = ($folderName != '') ? '/' . $folderName . '/' . $fileName : '/' . $fileName;
        $this->dropbox->delete($path);
    }

    public function getFileUrl(string $file)
    {
        return $this->dropbox->getTemporaryLink($file)->getLink();
    }
}
