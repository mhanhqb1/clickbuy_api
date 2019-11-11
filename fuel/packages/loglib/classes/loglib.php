<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Log
 * @version    0.5
 * @author     ThaiLH
 * @license    MIT License
 * @copyright  OCEANIZE, Inc
 * @link       http://fuelphp.com
 */

namespace LogLib;

/**
 * Loglib class facade for the Monolog composer package.
 * Level & When it’s used
 * - DEBUG: Detailed information, typically of interest only when diagnosing problems.
 * - INFO: Confirmation that things are working as expected.
 * - WARNING: An indication that something unexpected happened, or indicative of some problem in the near future (e.g. ‘disk space low’). The software is still working as expected.
 * - ERROR: Due to a more serious problem, the software has not been able to perform some function.
 * - CRITICAL: A serious error, indicating that the program itself may be unable to continue running. 
 * - NOTICE: Things of moderate interest to the user or administrator.
 * - ALERT: A serious failure in a key system.
 * - EMERGENCY: The highest priority, usually reserved for catastrophic failures and reboot notices.
 */
 
class LogLib {
   /**
    * container for the Monolog instance
    */
   protected static $monolog = null;   
   
   /**
    * Copy of the Monolog log levels
    */
   protected static $levels = array(
        100 => 'DEBUG',
        200 => 'INFO',
        250 => 'NOTICE',
        300 => 'WARNING',
        400 => 'ERROR',
        500 => 'CRITICAL',
        550 => 'ALERT',
        600 => 'EMERGENCY',
   );
	
    /**
     * Initialize the class
     *
     * @created	May 05, 2014
     * @updated	May 06, 2014
     * @access	public
     * @author 	thailh <thailh@evolableasia.vn>     
     * @return	void
     */   
	public static function _init()
	{		
        // load the file config
		\Config::load('file', TRUE);   
        
		// make sure the log directories exist
		try
		{
			// determine the name and location of the logfile
			$rootpath = \Config::get('log_path').date('Y').'/';
			$filepath = \Config::get('log_path').date('Y/m').'/';
			$filename = $filepath.date('d').'.php';

			// get the required folder permissions
			$permission = \Config::get('file.chmod.folders', 0777);

			if ( ! is_dir($rootpath))
			{
				mkdir($rootpath, 0777, TRUE);
				chmod($rootpath, $permission);
			}
			if ( ! is_dir($filepath))
			{
				mkdir($filepath, 0777, TRUE);
				chmod($filepath, $permission);
			}

            // Backup log files in to backup log path
            // static::backup_logs();
          
			$handle = fopen($filename, 'a');
		}
		catch (\Exception $e)
		{
			\Config::set('log_threshold', \Fuel::L_NONE);
			throw new \FuelException('Unable to create or write to the log file. Please check the permissions on '.\Config::get('log_path').'. ('.$e->getMessage().')');
		}

		if ( ! filesize($filename))
		{
			fwrite($handle, "<?php defined('COREPATH') or exit('No direct script access allowed'); ?>".PHP_EOL.PHP_EOL);
			chmod($filename, \Config::get('file.chmod.files', 0666));
		}
		fclose($handle);

		// create the monolog instance
		static::$monolog = new \Monolog\Logger('fuelphp');

		// create the streamhandler, and activate the handler
		$stream = new \Monolog\Handler\StreamHandler($filename, \Monolog\Logger::DEBUG);
		$formatter = new \Monolog\Formatter\LineFormatter("%level_name% - %datetime% --> %message%".PHP_EOL, "Y-m-d H:i:s");
		$stream->setFormatter($formatter);
		static::$monolog->pushHandler($stream);
        
        /*
        $filename = $filepath.date('\db_d').'.php';
        $stream = new \MonoLog\Handler\StreamHandler($filename, \Monolog\Logger::NOTICE);
        $stream->setFormatter($formatter);
        static::$monolog->pushHandler($stream);
        * 
        */
	}    
    
    /**
     * Return the monolog instance
     *
     * @created	May 05, 2014
     * @updated	May 06, 2014
     * @access	public
     * @author 	thailh <thailh@evolableasia.vn>     
     * @return	monolog object
     */    
	public static function instance()
	{
		// make sure we have an instance
		static::$monolog or static::_init();

		// return the created instance
		return static::$monolog;
	}   
    
    /**
     * Log info
     *
     * @created	May 05, 2014
     * @updated	May 06, 2014
     * @access	public
     * @author 	thailh <thailh@evolableasia.vn>     
     * @param   string  $msg     Log message
     * @param   string  $method  method that logged
     * @param   array   $data    log more data (input params or response)
     * @param   bool    $console show on console screen
     * @return	boolean TRUE     if write log success ELSE FALSE     
     */   
    public static function info($msg, $method = null, $data = null, $console = FALSE)
	{
		$msg = static::message($msg, $data);
        return static::write(\Fuel::L_INFO, $msg, $method, $console);
	}
    
    /**
     * Log debug
     *
     * @created	May 05, 2014
     * @updated	May 06, 2014
     * @access	public
     * @author 	thailh <thailh@evolableasia.vn>     
     * @param   string  $msg     Log message
     * @param   string  $method  method that logged
     * @param   array   $data    log more data (input params or response)
     * @param   bool    $console show on console screen
     * @return	boolean TRUE     if write log success ELSE FALSE     
     */   
	public static function debug($msg, $method = null, $data = null, $console = FALSE)
	{
		$msg = static::message($msg, $data);
        return static::write(\Fuel::L_DEBUG, $msg, $method, $console);
	}

	/**
     * Log warning
     *
     * @created	May 05, 2014
     * @updated	May 06, 2014
     * @access	public
     * @author 	thailh <thailh@evolableasia.vn>     
     * @param   string  $msg     Log message
     * @param   string  $method  method that logged
     * @param   array   $data    log more data (input params or response)
     * @param   bool    $console show on console screen
     * @return	boolean TRUE     if write log success ELSE FALSE     
     */   
	public static function warning($msg, $method = null, $data = null, $console = FALSE)
	{
		$msg = static::message($msg, $data);
        return static::write(\Fuel::L_WARNING, $msg, $method, $console);
	}

	/**
     * Log error
     *
     * @created	May 05, 2014
     * @updated	May 06, 2014
     * @access	public
     * @author 	thailh <thailh@evolableasia.vn>     
     * @param   string  $msg     Log message
     * @param   string  $method  method that logged
     * @param   array   $data    log more data (input params or response)
     * @param   bool    $console show on console screen
     * @return	boolean TRUE if write log success else FALSE
     */  
	public static function error($msg, $method = null, $data = null, $console = FALSE)
	{
		$msg = static::message($msg, $data);
        return static::write(\Fuel::L_ERROR, $msg, $method, $console);
	}

    /**
     * Compile message and data to string 
     *
     * @created	May 05, 2014
     * @updated	May 06, 2014
     * @access	public
     * @author 	thailh <thailh@evolableasia.vn>     
     * @param   string  $msg     message     
     * @param   array   $data    data array (input params or response)     
     * @return	string  $message     
     */ 
    private static function message($msg, $data = NULL)
    {       
       if (empty($data)) {
			return $msg;
		}
        return "{$msg} - " . json_encode($data);
        
		if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $msg .= "- $key: " .
                        html_entity_decode(self::mojibake($value, TRUE), ENT_NOQUOTES, 'UTF-8');
                } else {
                    $msg .= " $key: " . self::mojibake($value, FALSE);
                }
            }
        } elseif (is_object($data)) {
			$msg = "{$msg} - " . self::mojibake($data, TRUE);
		} else {
			$msg = "{$msg} - " . self::mojibake($data, FALSE);
		}
        return $msg;
    }
    
    /**
     * Write log
     *
     * @created	May 05, 2014
     * @updated	May 06, 2014
     * @access	public
     * @author 	thailh <thailh@evolableasia.vn>     
     * @param   string  $level   level log 
     * @param   string  $msg     Log message
     * @param   string  $method  method that logged     
     * @param   bool    $console show on console screen
     * @return	boolean TRUE if write log success else FALSE
     */  
    public static function write($level, $msg, $method = null, $console = FALSE)
	{
		// defined default error labels
		static $oldlabels = array(
			1  => 'Error',
			2  => 'Warning',
			3  => 'Debug',
			4  => 'Info',
		);

		// get the levels defined to be logged
		$loglabels = \Config::get('log_threshold');

		// bail out if we don't need logging at all
		if ($loglabels == \Fuel::L_NONE)
		{
			return FALSE;
		}

		// if it's not an array, assume it's an "up to" level
		if ( ! is_array($loglabels))
		{
			$a = array();
			foreach (static::$levels as $l => $label)
			{
				$l >= $loglabels and $a[] = $l;
			}
			$loglabels = $a;
		}

		// if profiling is active log the message to the profile
		if (\Config::get('profiling'))
		{
			\Console::log($method.' - '.$msg);
		}

		// convert the level to monolog standards if needed
		if (is_int($level) and isset($oldlabels[$level]))
		{
			$level = strtoupper($oldlabels[$level]);
		}
		if (is_string($level))
		{
			if ( ! $level = array_search($level, static::$levels))
			{
				$level = 250;	// can't map it, convert it to a NOTICE
			}
		}

		// make sure $level has the correct value
		if ((is_int($level) and ! isset(static::$levels[$level])) or (is_string($level) and ! array_search(strtoupper($level), static::$levels)))
		{
			throw new \FuelException('Invalid level "'.$level.'" passed to logger()');
		}

		// do we need to log the message with this level?
		if ( ! in_array($level, $loglabels))
		{
			return FALSE;
		}

        if($console){
            switch($level){
                case \Fuel::L_ERROR:
                    echo \Fuel\Core\Cli::color($msg."\n","red");
                    break;
               case \Fuel::L_INFO:
                    echo \Fuel\Core\Cli::color($msg."\n","green");
                    break;
               case \Fuel::L_WARNING:
                    echo \Fuel\Core\Cli::color($msg."\n","yellow");
                    break;
            }
        }
        
		// log the message
		static::instance()->log($level, (empty($method) ? '' : $method.' - ').$msg);

		return TRUE;
	}   
    
    /**
    * Backup logs   
    * @created	May 05, 2014
    * @updated	May 06, 2014
    * @access	public
    * @author 	thailh <thailh@evolableasia.vn>          
    * @return	boolean TRUE if backup success else FALSE
    */  
    public static function backup_logs(  ){
        // load the file config
        \Config::load('file', TRUE);
        
        $log_path_bk = \Config::get('log_path_bk');
        
        if(!is_dir($log_path_bk)){
            // get the required folder permissions
            $permission = \Config::get('file.chmod.folders', 0777);
            mkdir($log_path_bk, 0777, TRUE);
            chmod($log_path_bk, $permission);
        }
        switch (\Config::get('log_bk_after', 'y')){
            case 'y':
                return static::backup_log_year();
            case 'm':
                return static::backup_log_month();
            default:
                return static::backup_log_day();
        }
    }    
    
    /**
    * Auto backup logs yearly   
    * @created	May 05, 2014
    * @updated	May 06, 2014
    * @access	protected
    * @author 	thailh <thailh@evolableasia.vn>          
    * @return	boolean TRUE if backup success else FALSE
    */    
    protected static function backup_log_year($forceBackup = FALSE){              
        $prevYear = date("Y", strtotime("-1year")); // backup log folder of previous year
        $srcPath = \Config::get('log_path').$prevYear; // log folder of previous year        
        $dstName = \Config::get('log_path_bk').$prevYear.".zip"; // backup YYYY.zip (previous year)
        if(!is_dir($srcPath)){
            return FALSE;
        } 
        // Abort backup if backup file is existing
        if(file_exists($dstName) && $forceBackup === FALSE){
            return TRUE;
        }  
        return \Compress::zipFolder($srcPath, $dstName);      
    }
    
   /**
    * Auto backup logs monthly   
    * @created	May 05, 2014
    * @updated	May 06, 2014
    * @access	protected
    * @author 	thailh <thailh@evolableasia.vn>          
    * @return	boolean TRUE if backup success else FALSE
    */  
    protected static function backup_log_month($forceBackup = FALSE){   
        $srcPath = \Config::get('log_path').date("Y/m", strtotime("-1month")); // log folder of previous month        
        $dstName = \Config::get('log_path_bk').date("Y-m", strtotime("-1month")).".zip"; // backup YYYY-MM.zip (previous month)
        if(!is_dir($srcPath)){
            return FALSE;
        }
        // Abort backup if backup file is existing
        if(file_exists($dstName) && $forceBackup === FALSE){       
            return TRUE;
        }   
        return \Compress::zipFolder($srcPath, $dstName);
    }
    
   /**
    * Auto backup logs daily   
    * @created	May 05, 2014
    * @updated	May 06, 2014
    * @access	protected
    * @author 	thailh <thailh@evolableasia.vn>          
    * @return	boolean TRUE if backup success else FALSE
    */ 
    protected static function backup_log_day($forceBackup = FALSE){          
        $srcName = \Config::get('log_path').date("Y/m/d", strtotime("-1 day")); // log file of yesterday
        $dstName = \Config::get('log_path_bk').date("Y-m-d", strtotime("-1 day")).".zip"; // backup YYYY-MM-DD.zip (yesterday)
        if(!is_file($srcName)){
            return FALSE;
        }
        // Abort backup if backup file is existing
        if(file_exists($dstName) && $forceBackup === FALSE){
            return TRUE;
        }  
        return \Compress::zipFile($srcName, $dstName);
    }
    
    /**
     * Encode data log
     * @created     June 23, 2014
     * @param       array|object|string.. $data
     * @param       json $isjson
     * @return      String
     */
    private static function mojibake($data, $isjson = false) {
        if ($isjson) {
            return preg_replace("/\\\\u([0-9A-Fa-f]{4})/u", "&#x\\1;", @json_encode($data));
        } else {
            return preg_replace("/\\\\u([0-9A-Fa-f]{4})/u", "&#x\\1;", $data);
        }
    }
}