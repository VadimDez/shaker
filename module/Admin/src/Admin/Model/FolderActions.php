<?php
/**
 * Created by PhpStorm.
 * User: vadimdez
 * Date: 02/11/13
 * Time: 00:32
 */

namespace Admin\Model;

class FolderActions
{
    public function createFolderAndReturnFolderName($name,$path)
    {
        $name = str_replace(' ','_',$name);
        // check if folder exists, and if doesn't - create folder
        $myFolder = $path . $name;
        If(!file_exists($myFolder))
        {
            mkdir($myFolder);
        }
        return $myFolder;
    }

    public function deleteFolder($dirPath)
    {
        // delete folder with FILES in it
        if (! is_dir($dirPath)) {
            throw new \Exception("$dirPath must be a directory");
        }

        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}