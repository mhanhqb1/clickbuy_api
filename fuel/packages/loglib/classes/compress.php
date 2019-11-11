<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    NextLog
 * @version    0.5
 * @author     ThaiLH <thailh@evolableasia.vn>
 * @license    MIT License
 * @copyright  Yahoo! JAPAN
 * @link       http://fuelphp.com
 */

namespace LogLib;

/**
 * Compress class for compress file/folder
 */
class Compress {
    
	public function __construct() {}
    
    /**
    * Compress a file  
    * @access	public  
    * @return	bool
    */
	public static function zipFile($srcName, $dstName) {	
        try{
            logger(\Fuel::L_INFO, "START BACUP FILE {$srcName} - {$dstName}", __METHOD__);  
            $z = new \ZipArchive(); 
            if($z->open($dstName, \ZIPARCHIVE::CREATE)){	
                $z->addFile($srcName);
            }
            $z->close();
            logger(\Fuel::L_INFO, "FINISH BACUP FILE {$srcName} - {$dstName}", __METHOD__);
        }catch (Exception $e){
            logger(\Fuel::L_ERROR, $e->getMessage(), __METHOD__);  
            return false;
        }
        return true;
	}
  
    /**
    * Compress a folder  
    * @access	public  
    * @return	bool
    */
	public static function zipFolder($srcPath, $dstName) {
        try{            
            logger(\Fuel::L_INFO, "START BACUP FOLDER {$srcPath} - {$dstName}", __METHOD__);          
            $z = new \ZipArchive(); 
            if($z->open($dstName, \ZIPARCHIVE::CREATE)){	
                self::folderToZip($srcPath, $z, strlen("{$srcPath}/"));
            }
            $z->close();
            logger(\Fuel::L_INFO, "FINISH BACUP FOLDER {$srcPath} - {$dstName}", __METHOD__);          
        }catch (Exception $e){           
            logger(\Fuel::L_ERROR, $e->getMessage(), __METHOD__);  
            return false;
        }
        return true;
	}
	
    /**
    * Read and add all files/sub-folder in ZipArchive 
    * @access	private  
    * @return	bool
    */
	private static function folderToZip($folder, &$zipFile, $exclusiveLength) { 
        try{
            $handle = opendir($folder); 
            while (false !== $f = readdir($handle)){ 
                if ($f != '.' && $f != '..'){ 
                    $filePath = "{$folder}/{$f}"; 
                    // Remove prefix from file path before add to zip. 				
                    $localPath = substr($filePath, $exclusiveLength); 
                    if(is_file($filePath)) { 
                        $zipFile->addFile($filePath, $localPath); 
                    }elseif(is_dir($filePath)){ 
                        // Add sub-directory. 
                        $zipFile->addEmptyDir($localPath);
                        self::folderToZip($filePath, $zipFile, $exclusiveLength);
                    }
                }
            }
            closedir($handle);
         }catch (Exception $e){
            logger(\Fuel::L_ERROR, $e->getMessage(), __METHOD__);  
            return false;
        }
        return true;
	}
}