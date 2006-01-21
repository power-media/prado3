<?php
/**
 * Prado core interfaces and classes.
 *
 * This file contains and includes the definitions of Prado core interfaces and classes.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Revision: $  $Date: $
 * @package System
 */

/**
 * The framework installation path.
 */
define('PRADO_DIR',dirname(__FILE__));

/**
 * Includes TComponent definition
 */
require_once(PRADO_DIR.'/TComponent.php');
/**
 * Includes exception definitions
 */
require_once(PRADO_DIR.'/Exceptions/TException.php');
/**
 * Includes TList definition
 */
require_once(PRADO_DIR.'/Collections/TList.php');
/**
 * Includes TMap definition
 */
require_once(PRADO_DIR.'/Collections/TMap.php');
/**
 * Includes TXmlDocument, TXmlElement definition
 */
require_once(PRADO_DIR.'/Data/TXmlDocument.php');
/**
 * Includes THttpUtility definition
 */
require_once(PRADO_DIR.'/Web/THttpUtility.php');
/**
 * Includes TCache definition
 */
require_once(PRADO_DIR.'/Data/TCache.php');
/**
 * Includes TLogger definition
 */
require_once(PRADO_DIR.'/Log/TLogger.php');

/**
 * IModule interface.
 *
 * This interface must be implemented by application modules.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System
 * @since 3.0
 */
interface IModule
{
	/**
	 * Initializes the module.
	 * @param TXmlElement the configuration for the module
	 */
	public function init($config);
	/**
	 * @return string ID of the module
	 */
	public function getID();
	/**
	 * @param string ID of the module
	 */
	public function setID($id);
}

/**
 * IService interface.
 *
 * This interface must be implemented by services.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System
 * @since 3.0
 */
interface IService
{
	/**
	 * Initializes the service.
	 * @param TXmlElement the configuration for the service
	 */
	public function init($config);
	/**
	 * @return string ID of the service
	 */
	public function getID();
	/**
	 * @param string ID of the service
	 */
	public function setID($id);
	/**
	 * Runs the service.
	 */
	public function run();
}

/**
 * ITextWriter interface.
 *
 * This interface must be implemented by writers.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System
 * @since 3.0
 */
interface ITextWriter
{
	/**
	 * Writes a string.
	 * @param string string to be written
	 */
	public function write($str);
	/**
	 * Flushes the content that has been written.
	 */
	public function flush();
}

/**
 * ITheme interface.
 *
 * This interface must be implemented by theme.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System
 * @since 3.0
 */
interface ITheme
{
	/**
	 * Applies this theme to the specified control.
	 * @param TControl the control to be applied with this theme
	 */
	public function apply($control);
}

/**
 * ITemplate interface
 *
 * ITemplate specifies the interface for classes encapsulating
 * parsed template structures.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System
 * @since 3.0
 */
interface ITemplate
{
	/**
	 * Instantiates the template.
	 * Content in the template will be instantiated as components and text strings
	 * and passed to the specified parent control.
	 * @param TControl the parent control
	 */
	public function instantiateIn($parent);
}

/**
 * IUser interface.
 *
 * This interface must be implemented by user objects.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System
 * @since 3.0
 */
interface IUser
{
	/**
	 * @return string username
	 */
	public function getName();
	/**
	 * @param string username
	 */
	public function setName($value);
	/**
	 * @return boolean if the user is a guest
	 */
	public function getIsGuest();
	/**
	 * @param boolean if the user is a guest
	 */
	public function setIsGuest($value);
	/**
	 * @return array list of roles that the user is of
	 */
	public function getRoles();
	/**
	 * @return array|string list of roles that the user is of. If it is a string, roles are assumed by separated by comma
	 */
	public function setRoles($value);
	/**
	 * @param string role to be tested
	 * @return boolean whether the user is of this role
	 */
	public function isInRole($role);
	/**
	 * @return string user data that is serialized and will be stored in session
	 */
	public function saveToString();
	/**
	 * @param string user data that is serialized and restored from session
	 * @return IUser the user object
	 */
	public function loadFromString($string);
}

/**
 * IStatePersister class.
 *
 * This interface must be implemented by all state persister classes (such as
 * {@link TPageStatePersister}, {@link TApplicationStatePersister}.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System
 * @since 3.0
 */
interface IStatePersister
{
	/**
	 * Loads state from a persistent storage.
	 * @return mixed the state
	 */
	public function load();
	/**
	 * Saves state into a persistent storage.
	 * @param mixed the state to be saved
	 */
	public function save($state);
}

/**
 * TModule class.
 *
 * TModule implements the basic methods required by IModule and may be
 * used as the basic class for application modules.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System
 * @since 3.0
 */
abstract class TModule extends TComponent implements IModule
{
	/**
	 * @var string module id
	 */
	private $_id;

	/**
	 * Initializes the module.
	 * This method is required by IModule and is invoked by application.
	 * @param TXmlElement module configuration
	 */
	public function init($config)
	{
	}

	/**
	 * @return string id of this module
	 */
	public function getID()
	{
		return $this->_id;
	}

	/**
	 * @param string id of this module
	 */
	public function setID($value)
	{
		$this->_id=$value;
	}
}

/**
 * TService class.
 *
 * TService implements the basic methods required by IService and may be
 * used as the basic class for application services.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System
 * @since 3.0
 */
abstract class TService extends TComponent implements IService
{
	/**
	 * @var string service id
	 */
	private $_id;

	/**
	 * Initializes the service and attaches {@link run} to the RunService event of application.
	 * This method is required by IService and is invoked by application.
	 * @param TXmlElement module configuration
	 */
	public function init($config)
	{
	}

	/**
	 * @return string id of this service
	 */
	public function getID()
	{
		return $this->_id;
	}

	/**
	 * @param string id of this service
	 */
	public function setID($value)
	{
		$this->_id=$value;
	}

	/**
	 * Runs the service.
	 */
	public function run()
	{
	}
}

/**
 * PradoBase class.
 *
 * PradoBase implements a few fundamental static methods.
 *
 * To use the static methods, Use Prado as the class name rather than PradoBase.
 * PradoBase is meant to serve as the base class of Prado. The latter might be
 * rewritten for customization.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System
 * @since 3.0
 */
class PradoBase
{
	/**
	 * File extension for Prado class files.
	 */
	const CLASS_FILE_EXT='.php';
	/**
	 * @var array list of path aliases
	 */
	private static $_aliases=array('System'=>PRADO_DIR);
	/**
	 * @var array list of namespaces currently in use
	 */
	private static $_usings=array();
	/**
	 * @var TApplication the application instance
	 */
	private static $_application=null;
	/**
	 * @var TLogger logger instance
	 */
	private static $_logger=null;

	/**
	 * @return string the version of Prado framework
	 */
	public static function getVersion()
	{
		return '3.0b';
	}

	/**
	 * @return string a string that can be displayed on your Web page showing powered-by-PRADO information
	 */
	public static function poweredByPrado()
	{
		return '<a title="Powered by PRADO" href="http://www.pradosoft.com/"><img src="http://www.pradosoft.com/images/powered.gif" style="border-width:0px;" alt="Powered by PRADO" /></a>';
	}

	/**
	 * PHP error handler.
	 * This method should be registered as PHP error handler using
	 * {@link set_error_handler}. The method throws an exception that
	 * contains the error information.
	 * @param integer the level of the error raised
	 * @param string the error message
	 * @param string the filename that the error was raised in
	 * @param integer the line number the error was raised at
	 */
	public static function phpErrorHandler($errno,$errstr,$errfile,$errline)
	{
		if(error_reporting()!=0)
			throw new TPhpErrorException($errno,$errstr,$errfile,$errline);
	}

	/**
	 * Default exception handler.
	 * This method should be registered as default exception handler using
	 * {@link set_exception_handler}. The method tries to use the errorhandler
	 * module of the Prado application to handle the exception.
	 * If the application or the module does not exist, it simply echoes the
	 * exception.
	 * @param Exception exception that is not caught
	 */
	public static function exceptionHandler($exception)
	{
		if(self::$_application!==null && ($errorHandler=self::$_application->getErrorHandler())!==null)
		{
			$errorHandler->handleError(null,$exception);
		}
		else
		{
			echo $exception;
		}
		exit(1);
	}

	/**
	 * Stores the application instance in the class static member.
	 * This method helps implement a singleton pattern for TApplication.
	 * Repeated invocation of this method or the application constructor
	 * will cause the throw of an exception.
	 * This method should only be used by framework developers.
	 * @param TApplication the application instance
	 * @throws TInvalidOperationException if this method is invoked twice or more.
	 */
	public static function setApplication($application)
	{
		if(self::$_application!==null)
			throw new TInvalidOperationException('prado_application_singleton_required');
		self::$_application=$application;
	}

	/**
	 * @return TApplication the application singleton, null if the singleton has not be created yet.
	 */
	public static function getApplication()
	{
		return self::$_application;
	}

	/**
	 * @return string the path of the framework
	 */
	public static function getFrameworkPath()
	{
		return PRADO_DIR;
	}

	/**
	 * Serializes a data.
	 * The original PHP serialize function has a bug that may not serialize
	 * properly an object.
	 * @param mixed data to be serialized
	 * @return string the serialized data
	 */
	public static function serialize($data)
	{
		$arr[0]=$data;
		return serialize($arr);
	}

	/**
	 * Unserializes a data.
	 * The original PHP unserialize function has a bug that may not unserialize
	 * properly an object.
	 * @param string data to be unserialized
	 * @return mixed unserialized data, null if unserialize failed
	 */
	public static function unserialize($str)
	{
		$arr=unserialize($str);
		return isset($arr[0])?$arr[0]:null;
	}

	/**
	 * Creates a component with the specified type.
	 * A component type can be either the component class name
	 * or a namespace referring to the path of the component class file.
	 * For example, 'TButton', 'System.Web.UI.WebControls.TButton' are both
	 * valid component type.
	 * This method can also pass parameters to component constructors.
	 * All paramters passed to this method except the first one (the component type)
	 * will be supplied as component constructor paramters.
	 * @param string component type
	 * @return TComponent component instance of the specified type
	 * @throws TInvalidDataValueException if the component type is unknown
	 */
	public static function createComponent($type)
	{
		self::using($type);
		if(($pos=strrpos($type,'.'))!==false)
			$type=substr($type,$pos+1);
		if(($n=func_num_args())>1)
		{
			$args=func_get_args();
			$s='$args[1]';
			for($i=2;$i<$n;++$i)
				$s.=",\$args[$i]";
			eval("\$component=new $type($s);");
			return $component;
		}
		else
			return new $type;
	}

	/**
	 * Uses a namespace.
	 * A namespace ending with an asterisk '*' refers to a directory, otherwise it represents a PHP file.
	 * If the namespace corresponds to a directory, the directory will be appended
	 * to the include path. If the namespace corresponds to a file, it will be included (require_once).
	 * @param string namespace to be used
	 * @throws TInvalidDataValueException if the namespace is invalid
	 */
	public static function using($namespace)
	{
		if(isset(self::$_usings[$namespace]) || class_exists($namespace,false))
			return;
		if(($pos=strrpos($namespace,'.'))===false)  // a class name
		{
			require_once($namespace.self::CLASS_FILE_EXT);
			if(!class_exists($namespace,false))
				throw new TInvalidOperationException('prado_component_unknown',$namespace);
		}
		else if(($path=self::getPathOfNamespace($namespace,self::CLASS_FILE_EXT))!==null)
		{
			$className=substr($namespace,$pos+1);
			if($className==='*')  // a directory
			{
				if(is_dir($path))
				{
					self::$_usings[$namespace]=$path;
					set_include_path(get_include_path().PATH_SEPARATOR.$path);
				}
				else
					throw new TInvalidDataValueException('prado_using_invalid',$namespace);
			}
			else  // a file
			{
				if(is_file($path))
				{
					self::$_usings[$namespace]=$path;
					if(!class_exists($className,false))
						require_once($path);
				}
				else
					throw new TInvalidDataValueException('prado_using_invalid',$namespace);
			}
		}
		else
			throw new TInvalidDataValueException('prado_using_invalid',$namespace);
	}

	/**
	 * Translates a namespace into a file path.
	 * The first segment of the namespace is considered as a path alias
	 * which is replaced with the actual path. The rest segments are
	 * subdirectory names appended to the aliased path.
	 * If the namespace ends with an asterisk '*', it represents a directory;
	 * Otherwise it represents a file whose extension name is specified by the second parameter (defaults to empty).
	 * Note, this method does not ensure the existence of the resulting file path.
	 * @param string namespace
	 * @param string extension to be appended if the namespace refers to a file
	 * @return string file path corresponding to the namespace, null if namespace is invalid
	 */
	public static function getPathOfNamespace($namespace,$ext='')
	{
		if(isset(self::$_usings[$namespace]))
			return self::$_usings[$namespace];
		else if(isset(self::$_aliases[$namespace]))
			return self::$_aliases[$namespace];
		else
		{
			$segs=explode('.',$namespace);
			$alias=array_shift($segs);
			if(($file=array_pop($segs))!==null && ($root=self::getPathOfAlias($alias))!==null)
				return rtrim($root.'/'.implode('/',$segs),'/').(($file==='*')?'':'/'.$file.$ext);
			else
				return null;
		}
	}

	/**
	 * @param string alias to the path
	 * @return string the path corresponding to the alias, null if alias not defined.
	 */
	public static function getPathOfAlias($alias)
	{
		return isset(self::$_aliases[$alias])?self::$_aliases[$alias]:null;
	}

	/**
	 * @param string alias to the path
	 * @param string the path corresponding to the alias
	 * @throws TInvalidOperationException if the alias is already defined
	 * @throws TInvalidDataValueException if the path is not a valid file path
	 */
	public static function setPathOfAlias($alias,$path)
	{
		if(isset(self::$_aliases[$alias]))
			throw new TInvalidOperationException('prado_alias_redefined',$alias);
		else if(($rp=realpath($path))!==false && is_dir($rp))
		{
			if(strpos($alias,'.')===false)
				self::$_aliases[$alias]=$rp;
			else
				throw new TInvalidDataValueException('prado_aliasname_invalid',$alias);
		}
		else
			throw new TInvalidDataValueException('prado_alias_invalid',$alias,$path);
	}

	/**
	 * Fatal error handler.
	 * This method displays an error message together with the current call stack.
	 * The application will exit after calling this method.
	 * @param string error message
	 */
	public static function fatalError($msg)
	{
		echo '<h1>Fatal Error</h1>';
		echo '<p>'.$msg.'</p>';
		if(!function_exists('debug_backtrace'))
			return;
		echo '<h2>Debug Backtrace</h2>';
		echo '<pre>';
		$index=-1;
		foreach(debug_backtrace() as $t)
		{
			$index++;
			if($index==0)  // hide the backtrace of this function
				continue;
			echo '#'.$index.' ';
			if(isset($t['file']))
				echo basename($t['file']) . ':' . $t['line'];
			else
			   echo '<PHP inner-code>';
			echo ' -- ';
			if(isset($t['class']))
				echo $t['class'] . $t['type'];
			echo $t['function'];
			if(isset($t['args']) && sizeof($t['args']) > 0)
				echo '(...)';
			else
				echo '()';
			echo "\n";
		}
		echo '</pre>';
		exit(1);
	}

	/**
	 * Returns a list of user preferred languages.
	 * The languages are returned as an array. Each array element
	 * represents a single language preference. The languages are ordered
	 * according to user preferences. The first language is the most preferred.
	 * @return array list of user preferred languages.
	 */
	public static function getUserLanguages()
	{
		static $languages=null;
		if($languages===null)
		{
			$languages=array();
			foreach(explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']) as $language)
			{
				$array=split(';q=',trim($language));
				$languages[trim($array[0])]=isset($array[1])?(float)$array[1]:1.0;
			}
			arsort($languages);
			$languages=array_keys($languages);
			if(empty($languages))
				$languages[0]='en';
		}
		return $languages;
	}

	/**
	 * Returns the most preferred language by the client user.
	 * @return string the most preferred language by the client user, defaults to English.
	 */
	public static function getPreferredLanguage()
	{
		static $language=null;
		if($language===null)
		{
			$langs=Prado::getUserLanguages();
			$lang=explode('-',$langs[0]);
			if(empty($lang[0]) || !ctype_alpha($lang[0]))
				$language='en';
			else
				$language=$lang[0];
		}
		return $language;
	}

	/**
	 * Writes a log message.
	 * This method wraps {@link log()} by checking the application mode.
	 * When the application is in Debug mode, debug backtrace information is appended
	 * to the message and the message is logged at DEBUG level.
	 * When the application is in Performance mode, this method does nothing.
	 * Otherwise, the message is logged at INFO level.
	 * @param string message to be logged
	 * @param string category of the message
	 * @see log, getLogger
	 */
	public static function trace($msg,$category='Uncategorized')
	{
		if(self::$_application && self::$_application->getMode()==='Performance')
			return;
		if(!self::$_application || self::$_application->getMode()==='Debug')
		{
			$trace=debug_backtrace();
			if(isset($trace[0]['file']) && isset($trace[0]['line']))
				$msg.=" (line {$trace[0]['line']}, {$trace[0]['file']})";
			$level=TLogger::DEBUG;
		}
		else
			$level=TLogger::INFO;
		self::log($msg,$level,$category);
	}

	/**
	 * Logs a message.
	 * Messages logged by this method may be retrieved via {@link TLogger::getLogs}
	 * and may be recorded in different media, such as file, email, database, using
	 * {@link TLogRouter}.
	 * @param string message to be logged
	 * @param integer level of the message. Valid values include
	 * TLogger::DEBUG, TLogger::INFO, TLogger::NOTICE, TLogger::WARNING,
	 * TLogger::ERROR, TLogger::ALERT, TLogger::FATAL.
	 * @param string category of the message
	 */
	public static function log($msg,$level=TLogger::INFO,$category='Uncategorized')
	{
		if(self::$_logger===null)
			self::$_logger=new TLogger;
		self::$_logger->log($msg,$level,$category);
	}

	/**
	 * @return TLogger message logger
	 */
	public static function getLogger()
	{
		if(self::$_logger===null)
			self::$_logger=new TLogger;
		return self::$_logger;
	}
}

class TTextWriter extends TComponent implements ITextWriter
{
	private $_str='';

	public function flush()
	{
		$str=$this->_str;
		$this->_str='';
		return $str;
	}

	public function write($str)
	{
		$this->_str.=$str;
	}

	public function writeLine($str='')
	{
		$this->write($str."\n");
	}
}

class TDate extends TComponent
{
}
?>