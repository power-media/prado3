<?php
/**
 * TThemeManager class
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Revision: $  $Date: $
 * @package System.Web.UI
 */

/**
 * TThemeManager class
 *
 * TThemeManager manages the themes used in a Prado application.
 *
 * Themes are stored under the directory specified by the
 * {@link setBasePath BasePath} property. The themes can be accessed
 * via URL {@link setBaseUrl BaseUrl}. Each theme is represented by a subdirectory
 * and all the files under that directory. The name of a theme is the name
 * of the corresponding subdirectory.
 * By default, the base path of all themes is a directory named "themes"
 * under the directory containing the application entry script.
 * To get a theme (normally you do not need to), call {@link getTheme}.
 *
 * TThemeManager may be configured within page service tag in application
 * configuration file as follows,
 * <module id="themes" type="System.Web.UI.TThemeManager"
 *         BasePath="Application.themes" BaseUrl="/themes" />
 * where {@link getCacheExpire CacheExpire}, {@link getCacheControl CacheControl}
 * and {@link getBufferOutput BufferOutput} are configurable properties of THttpResponse.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System.Web.UI
 * @since 3.0
 */
class TThemeManager extends TModule
{
	/**
	 * default themes base path
	 */
	const DEFAULT_BASEPATH='themes';
	/**
	 * @var boolean whether this module has been initialized
	 */
	private $_initialized=false;
	/**
	 * @var string the directory containing all themes
	 */
	private $_basePath=null;
	/**
	 * @var string the base URL for all themes
	 */
	private $_baseUrl=null;

	/**
	 * Initializes the module.
	 * This method is required by IModule and is invoked by application.
	 * @param TXmlElement module configuration
	 */
	public function init($config)
	{
		$this->_initialized=true;
		$this->getService()->setThemeManager($this);
	}

	/**
	 * @param string name of the theme to be retrieved
	 * @return TTheme the theme retrieved
	 */
	public function getTheme($name)
	{
		$themePath=$this->getBasePath().'/'.$name;
		$themeUrl=$this->getBaseUrl().'/'.$name;
		return new TTheme($themePath,$themeUrl);

	}

	/**
	 * @return string the base path for all themes. It is returned as an absolute path.
	 * @throws TConfigurationException if base path is not set and "themes" directory does not exist.
	 */
	public function getBasePath()
	{
		if($this->_basePath===null)
		{
			$this->_basePath=dirname($this->getRequest()->getPhysicalApplicationPath()).'/'.self::DEFAULT_BASEPATH;
			if(($basePath=realpath($this->_basePath))===false || !is_dir($basePath))
				throw new TConfigurationException('thememanager_basepath_invalid',$this->_basePath);
			$this->_basePath=$basePath;
		}
		return $this->_basePath;
	}

	/**
	 * @param string the base path for all themes. It must be in the format of a namespace.
	 * @throws TInvalidDataValueException if the base path is not a proper namespace.
	 */
	public function setBasePath($value)
	{
		if($this->_initialized)
			throw new TInvalidOperationException('thememanager_basepath_unchangeable');
		else
		{
			$this->_basePath=Prado::getPathOfAlias($value);
			if($this->_basePath===null || !is_dir($this->_basePath))
				throw new TInvalidDataValueException('thememanager_basepath_invalid',$value);
			$this->_basePath=$value;
		}
	}

	/**
	 * @return string the base URL for all themes.
	 * @throws TConfigurationException If base URL is not set and a correct one cannot be determined by Prado.
	 */
	public function getBaseUrl()
	{
		if($this->_baseUrl===null)
		{
			$appPath=dirname($this->getRequest()->getPhysicalApplicationPath());
			$basePath=$this->getBasePath();
			if(strpos($basePath,$appPath)===false)
				throw new TConfigurationException('thememanager_baseurl_required');
			$appUrl=dirname($this->getRequest()->getApplicationPath());
			$this->_baseUrl=$appUrl.strtr(substr($basePath,strlen($appPath)),'\\','/');
		}
		return $this->_baseUrl;
	}

	/**
	 * @param string the base URL for all themes.
	 */
	public function setBaseUrl($value)
	{
		$this->_baseUrl=rtrim($value,'/');
	}
}

/**
 * TTheme class
 *
 * TTheme represents a particular theme. It is merely a collection of skins
 * that are applicable to the corresponding controls.
 *
 * Each theme is stored as a directory and files under that directory.
 * The theme name is the directory name. When TTheme is created, the files
 * whose name has the extension ".skin" are parsed and saved as controls skins.
 *
 * A skin is essentially a list of initial property values that are to be applied
 * to a control when the skin is applied.
 * Each type of control can have multiple skins identified by the SkinID.
 * If a skin does not have SkinID, it is the default skin that will be applied
 * to controls that do not specify particular SkinID.
 *
 * Whenever possible, TTheme will try to make use of available cache to save
 * the parsing time.
 *
 * To apply a theme to a particular control, call {@link applySkin}.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System.Web.UI
 * @since 3.0
 */
class TTheme extends TComponent
{
	/**
	 * prefix for cache variable name used to store parsed themes
	 */
	const THEME_CACHE_PREFIX='prado:theme:';
	/**
	 * Extension name of skin files
	 */
	const SKIN_FILE_EXT='.skin';
	/**
	 * @var string theme path
	 */
	private $_themePath;
	/**
	 * @var string theme url
	 */
	private $_themeUrl;
	/**
	 * @var array list of skins for the theme
	 */
	private $_skins=null;
	/**
	 * @var string theme name
	 */
	private $_name='';
	/**
	 * @var array list of css files
	 */
	private $_cssFiles=array();
	/**
	 * @var array list of js files
	 */
	private $_jsFiles=array();

	/**
	 * Constructor.
	 * @param string theme path
	 * @param string theme URL
	 * @throws TConfigurationException if theme path does not exist or any parsing error of the skin files
	 */
	public function __construct($themePath,$themeUrl)
	{
		$this->_themeUrl=$themeUrl;
		$this->_name=basename($themePath);
		if(($cache=$this->getApplication()->getCache())!==null)
		{
			$array=$cache->get(self::THEME_CACHE_PREFIX.$themePath);
			if(is_array($array))
			{
				list($skins,$cssFiles,$jsFiles,$timestamp)=$array;
				$cacheValid=true;
				if($this->getApplication()->getMode()!==TApplication::STATE_PERFORMANCE)
				{
					if(($dir=opendir($themePath))===false)
						throw new TIOException('theme_path_inexistent',$themePath);
					while(($file=readdir($dir))!==false)
					{
						if($file==='.' || $file==='..')
							continue;
						else if(basename($file,'.css')!==$file)
							$this->_cssFiles[]=$themeUrl.'/'.$file;
						else if(basename($file,'.js')!==$file)
							$this->_jsFiles[]=$themeUrl.'/'.$file;
						else if(basename($file,self::SKIN_FILE_EXT)!==$file && filemtime($themePath.'/'.$file)>$timestamp)
						{
							$cacheValid=false;
							break;
						}
					}
					closedir($dir);
					if($cacheValid)
						$this->_skins=$skins;
				}
				else
				{
					$this->_cssFiles=$cssFiles;
					$this->_jsFiles=$jsFiles;
					$this->_skins=$skins;
				}
			}
		}
		if($this->_skins===null)
		{
			if(($dir=opendir($themePath))===false)
				throw new TIOException('theme_path_inexistent',$themePath);
			while(($file=readdir($dir))!==false)
			{
				if($file==='.' || $file==='..')
					continue;
				else if(basename($file,'.css')!==$file)
					$this->_cssFiles[]=$themeUrl.'/'.$file;
				else if(basename($file,'.js')!==$file)
					$this->_jsFiles[]=$themeUrl.'/'.$file;
				else if(basename($file,self::SKIN_FILE_EXT)!==$file)
				{
					$template=new TTemplate(file_get_contents($themePath.'/'.$file),$themePath,$themePath.'/'.$file);
					foreach($template->getItems() as $skin)
					{
						if($skin[0]!==-1)
							throw new TConfigurationException('theme_control_nested',$skin[1],dirname($themePath));
						else if(!isset($skin[2]))  // a text string, ignored
							continue;
						$type=$skin[1];
						$id=isset($skin[2]['skinid'])?$skin[2]['skinid']:0;
						unset($skin[2]['skinid']);
						if(isset($this->_skins[$type][$id]))
							throw new TConfigurationException('theme_skinid_duplicated',$type,$id,dirname($themePath));
						foreach($skin[2] as $name=>$value)
						{
							if(is_array($value) && ($value[0]===TTemplate::CONFIG_DATABIND || $value[0]===TTemplate::CONFIG_PARAMETER))
								throw new TConfigurationException('theme_databind_forbidden',dirname($themePath),$type,$id);
						}
						$this->_skins[$type][$id]=$skin[2];
					}
				}
			}
			closedir($dir);
			if($cache!==null)
				$cache->set(self::THEME_CACHE_PREFIX.$themePath,array($this->_skins,$this->_cssFiles,$this->_jsFiles,time()));
		}
	}

	/**
	 * @return string theme name
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Applies the theme to a particular control.
	 * The control's class name and SkinID value will be used to
	 * identify which skin to be applied. If the control's SkinID is empty,
	 * the default skin will be applied.
	 * @param TControl the control to be applied with a skin
	 * @return boolean if a skin is successfully applied
	 * @throws TConfigurationException if any error happened during the skin application
	 */
	public function applySkin($control)
	{
		$type=get_class($control);
		if(($id=$control->getSkinID())==='')
			$id=0;
		if(isset($this->_skins[$type][$id]))
		{
			foreach($this->_skins[$type][$id] as $name=>$value)
			{
				Prado::trace("Applying skin $name to $type",'System.Web.UI.TThemeManager');
				if(is_array($value))
				{
					if($value[0]===TTemplate::CONFIG_EXPRESSION)
						$value=$this->evaluateExpression($value[1]);
					else if($value[0]===TTemplate::CONFIG_ASSET)
						$value=$this->_themeUrl.'/'.ltrim($value[1],'/');
				}
				if(strpos($name,'.')===false)	// is simple property or custom attribute
				{
					if($control->hasProperty($name))
					{
						if($control->canSetProperty($name))
						{
							$setter='set'.$name;
							$control->$setter($value);
						}
						else
							throw new TConfigurationException('theme_property_readonly',$type,$name);
					}
					else if($control->getAllowCustomAttributes())
						$control->getAttributes()->add($name,$value);
					else
						throw new TConfigurationException('theme_property_undefined',$type,$name);
				}
				else	// complex property
					$control->setSubProperty($name,$value);
			}
			return true;
		}
		else
			return false;
	}

	/**
	 * @return array list of CSS files (URL) in the theme
	 */
	public function getStyleSheetFiles()
	{
		return $this->_cssFiles;
	}

	/**
	 * @return array list of Javascript files (URL) in the theme
	 */
	public function getJavaScriptFiles()
	{
		return $this->_jsFiles;
	}
}

?>