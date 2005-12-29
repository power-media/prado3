<?php

require_once(dirname(__FILE__).'/Highlighter/geshi.php');

/**
 * ${classname}
 *
 * ${description}
 *
 * @author Wei Zhuo<weizhuo[at]gmail[dot]com>
 * @version $Revision: 1.66 $  $Date: ${DATE} ${TIME} $
 * @package ${package}
 */
class TTextHighlighter extends TWebControl
{
	/**
	 * @return string tag name of the panel
	 */
	protected function getTagName()
	{
		return 'div';
	}

	public function getLanguage()
	{
		return $this->getViewState('Language', 'php');
	}

	public function setLanguage($value)
	{
		$this->setViewState('Language', $value, 'php');
	}

	public function setEnableLineNumbers($value)
	{
		$this->setViewState('LineNumbers', TPropertyValue::ensureBoolean($value), false);
	}

	public function getEnableLineNumbers()
	{
		return $this->getViewState('LineNumbers', false);
	}

	public function getEnableEntities()
	{
		return $this->getViewState('Entities', false);
	}

	public function setEnableEntities($value)
	{
		$this->setViewState('Entities', TPropertyValue::ensureBoolean($value), false);
	}

	/**
	 * Parse the body string using GeSHi to highlight the contents.
	 */
	public function addParsedObject($object)
	{
		if(is_string($object))
		{
			$this->registerTextHighlightStyleSheet();
			$this->getControls()->add($this->getTextHighlight($object));
		}
		else
			$this->getControls()->add($object);
	}

	/**
	 * Register CSS style sheet file.
	 */
	protected function registerTextHighlightStyleSheet()
	{
		$cs = $this->getPage()->getClientScript();
		if(!$cs->isStyleSheetFileRegistered(get_class($this)))
		{
			$styleSheet = $this->getAsset('Highlighter/code_highlight.css');
			$cs->registerStyleSheetFile(get_class($this), $styleSheet);
		}
	}

	/**
	 * Returns the highlighted text.
	 * @param string text to highlight.
	 * @return string highlighted text.
	 */
	protected function getTextHighlight($text)
	{
		if(!$this->getEnableEntities())
			$text = html_entity_decode($text);
		$geshi = new GeSHi(trim($text), $this->getLanguage());
		if($this->getEnableLineNumbers())
			$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
		$geshi->enable_classes();
		return $geshi->parse_code();
	}
}
?>