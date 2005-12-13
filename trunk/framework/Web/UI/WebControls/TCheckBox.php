<?php
/**
 * TCheckBox class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Revision: $  $Date: $
 * @package System.Web.UI.WebControls
 */

/**
 * TCheckBox class
 *
 * TCheckBox displays a check box on the page.
 * You can specify the caption to display beside the check box by setting
 * the {@link setText Text} property.  The caption can appear either on the right
 * or left of the check box, which is determined by the {@link setTextAlign TextAlign}
 * property.
 *
 * To determine whether the TCheckBox component is checked, test the {@link getChecked Checked}
 * property. The {@link onCheckedChanged CheckedChanged} event is raised when
 * the {@link getChecked Checked} state of the TCheckBox component changes
 * between posts to the server. You can provide an event handler for
 * the {@link onCheckedChanged CheckedChanged} event to  to programmatically
 * control the actions performed when the state of the TCheckBox component changes
 * between posts to the server.
 *
 * If {@link setAutoPostBack AutoPostBack} is set true, changing the check box state
 * will cause postback action. And if {@link setCausesValidation CausesValidation}
 * is true, validation will also be processed, which can be further restricted within
 * a {@link setValidationGroup ValidationGroup}.
 *
 * Note, {@link setText Text} is rendered as is. Make sure it does not contain unwanted characters
 * that may bring security vulnerabilities.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System.Web.UI.WebControls
 * @since 3.0
 */
class TCheckBox extends TWebControl implements IPostBackDataHandler, IValidatable
{
	/**
	 * @return string tag name of the button
	 */
	protected function getTagName()
	{
		return 'input';
	}

	/**
	 */
	protected function addAttributesToRender($writer)
	{
		// TODO: add display inline block
	}

	/**
	 * Loads user input data.
	 * This method is primarly used by framework developers.
	 * @param string the key that can be used to retrieve data from the input data collection
	 * @param array the input data collection
	 * @return boolean whether the data of the control has been changed
	 */
	public function loadPostData($key,$values)
	{
		$checked=$this->getChecked();
		if(isset($values[$key])!=$checked)
		{
			$this->setChecked(!$checked);
			return true;
		}
		else
			return false;
	}

	/**
	 * Raises postdata changed event.
	 * This method raises {@link onCheckedChanged CheckedChanged} event.
	 * This method is primarly used by framework developers.
	 */
	public function raisePostDataChangedEvent()
	{
		$page=$this->getPage();
		if($this->getAutoPostBack() && !$page->getPostBackEventTarget())
		{
			$page->setPostBackEventTarget($this);
			if($this->getCausesValidation())
				$page->validate($this->getValidationGroup());
		}
		$this->onCheckedChanged(null);
	}

	/**
	 * Raises <b>CheckedChanged</b> event when {@link getChecked Checked} changes value during postback.
	 * If you override this method, be sure to call the parent implementation
	 * so that the event delegates can be invoked.
	 * @param TEventParameter event parameter to be passed to the event handlers
	 */
	protected function onCheckedChanged($param)
	{
		$this->raiseEvent('CheckedChanged',$this,$param);
	}

	/**
	 * Registers the checkbox to receive postback data during postback.
	 * This is necessary because a checkbox if unchecked, when postback,
	 * does not have direct mapping between post data and the checkbox name.
	 * 
	 * Auto-postback javascript code is also registered here.
	 * 
	 * This method overrides the parent implementation and is invoked before render.
	 * @param mixed event parameter
	 */
	protected function onPreRender($param)
	{
		parent::onPreRender($param);
		if($this->getEnabled(true))
			$this->getPage()->registerRequiresPostData($this);

		if($this->getAutoPostBack() 
			&& $this->getPage()->getClientSupportsJavaScript())
		{	
			$options = $this->getAutoPostBackOptions();
			$scripts = $this->getPage()->getClientScript();			
			$postback = $scripts->getPostBackEventReference($this,'',$options,false);
			$scripts->registerClientEvent($this, "click", $postback);
		}
	}

	/**
	 * Returns the value of the property that needs validation.
	 * @return mixed the property value to be validated
	 */
	public function getValidationPropertyValue()
	{
		return $this->getChecked();
	}

	/**
	 * @return string the text caption of the checkbox
	 */
	public function getText()
	{
		return $this->getViewState('Text','');
	}

	/**
	 * Sets the text caption of the checkbox.
	 * @param string the text caption to be set
	 */
	public function setText($value)
	{
		$this->setViewState('Text',$value,'');
	}

	/**
	 * @return string the alignment (Left or Right) of the text caption, defaults to Right.
	 */
	public function getTextAlign()
	{
		return $this->getViewState('TextAlign','Right');
	}

	/**
	 * @param string the alignment of the text caption. Valid values include Left and Right.
	 */
	public function setTextAlign($value)
	{
		$this->setViewState('TextAlign',TPropertyValue::ensureEnum($value,array('Left','Right')),'Right');
	}

	/**
	 * @return boolean whether the checkbox is checked
	 */
	public function getChecked()
	{
		return $this->getViewState('Checked',false);
	}

	/**
	 * Sets a value indicating whether the checkbox is to be checked or not.
	 * @param boolean whether the checkbox is to be checked or not.
	 */
	public function setChecked($value)
	{
		$this->setViewState('Checked',$value,false);
	}

	/**
	 * @return boolean whether clicking on the checkbox will post the page.
	 */
	public function getAutoPostBack()
	{
		return $this->getViewState('AutoPostBack',false);
	}

	/**
	 * Sets a value indicating whether clicking on the checkbox will post the page.
	 * @param boolean whether clicking on the checkbox will post the page.
	 */
	public function setAutoPostBack($value)
	{
		$this->setViewState('AutoPostBack',$value,false);
	}

	/**
	 * @return boolean whether postback event triggered by this checkbox will cause input validation, default is true.
	 */
	public function getCausesValidation()
	{
		return $this->getViewState('CausesValidation',true);
	}

	/**
	 * Sets the value indicating whether postback event trigger by this checkbox will cause input validation.
	 * @param boolean whether postback event trigger by this checkbox will cause input validation.
	 */
	public function setCausesValidation($value)
	{
		$this->setViewState('CausesValidation',TPropertyValue::ensureBoolean($value),true);
	}

	/**
	 * @return string the group of validators which the checkbox causes validation upon postback
	 */
	public function getValidationGroup()
	{
		return $this->getViewState('ValidationGroup','');
	}

	/**
	 * @param string the group of validators which the checkbox causes validation upon postback
	 */
	public function setValidationGroup($value)
	{
		$this->setViewState('ValidationGroup',$value,'');
	}

	/**
	 * Renders the checkbox control.
	 * This method overrides the parent implementation by rendering a checkbox input element
	 * and a span element if needed.
	 * @param THtmlWriter the writer used for the rendering purpose
	 */
	protected function render($writer)
	{
		$this->addAttributesToRender($writer);
		$this->getPage()->ensureRenderInForm($this);
		$needSpan=false;
		if($this->getHasStyle())
		{
			$this->getStyle()->addAttributesToRender($writer);
			$needSpan=true;
		}
		if(!$this->getEnabled(true))
		{
			$writer->addAttribute('disabled','disabled');
			$needSpan=true;
		}
		if(($tooltip=$this->getToolTip())!=='')
		{
			$writer->addAttribute('title',$tooltip);
			$needSpan=true;
		}
		$onclick=null;
		if($this->getHasAttributes())
		{
			$attributes=$this->getAttributes();
			$value=$attributes->remove('value');
			$onclick=$attributes->remove('onclick');
			if($attributes->getCount())
			{
				$writer->addAttributes($attributes);
				$needSpan=true;
			}
			if($value!==null)
				$attributes->add('value',$value);
		}
		if($needSpan)
			$writer->renderBeginTag('span');
		$clientID=$this->getClientID();
		if(($text=$this->getText())!=='')
		{
			if($this->getTextAlign()==='Left')
			{
				$this->renderLabel($writer,$clientID,$text);
				$this->renderInputTag($writer,$clientID,$onclick);
			}
			else
			{
				$this->renderInputTag($writer,$clientID,$onclick);
				$this->renderLabel($writer,$clientID,$text);
			}
		}
		else
			$this->renderInputTag($writer,$clientID,$onclick);
		if($needSpan)
			$writer->renderEndTag();
	}

	/**
	 * @return TMap list of attributes to be rendered for label beside the checkbox
	 */
	public function getLabelAttributes()
	{
		if($attributes=$this->getViewState('LabelAttributes',null))
			return $attributes;
		else
		{
			$attributes=new TMap;
			$this->setViewState('LabelAttributes',$attributes,null);
			return $attributes;
		}
	}

	/**
	 * @return TMap list of attributes to be rendered for the checkbox
	 */
	public function getInputAttributes()
	{
		if($attributes=$this->getViewState('InputAttributes',null))
			return $attributes;
		else
		{
			$attributes=new TMap;
			$this->setViewState('InputAttributes',$attributes,null);
			return $attributes;
		}
	}

	/**
	 * Renders a label beside the checkbox.
	 * @param THtmlWriter the writer for the rendering purpose
	 * @param string checkbox id
	 * @param string label text
	 */
	protected function renderLabel($writer,$clientID,$text)
	{
		$writer->addAttribute('for',$clientID);
		if($attributes=$this->getViewState('LabelAttributes',null))
			$writer->addAttributes($attributes);
		$writer->renderBeginTag('label');
		$writer->write($text);
		$writer->renderEndTag();
	}

	/**
	 * Renders a checkbox input element.
	 * @param THtmlWriter the writer for the rendering purpose
	 * @param string checkbox id
	 * @param string onclick attribute value for the checkbox
	 */
	protected function renderInputTag($writer,$clientID,$onclick)
	{
		if($clientID!=='')
			$writer->addAttribute('id',$clientID);
		$writer->addAttribute('type','checkbox');
		if(($uniqueID=$this->getUniqueID())!=='')
			$writer->addAttribute('name',$uniqueID);
		if($this->getChecked())
			$writer->addAttribute('checked','checked');
		if(!$this->getEnabled(true))
			$writer->addAttribute('disabled','disabled');
		$page=$this->getPage();
		if(($accesskey=$this->getAccessKey())!=='')
			$writer->addAttribute('accesskey',$accesskey);
		if(($tabindex=$this->getTabIndex())>0)
			$writer->addAttribute('tabindex',"$tabindex");
		if($attributes=$this->getViewState('InputAttributes',null))
			$writer->addAttributes($attributes);
		$writer->renderBeginTag('input');
		$writer->renderEndTag();
	}

	/**
	 * Sets the post back options for this textbox.
	 * @return TPostBackOptions 
	 */
	protected function getAutoPostBackOptions()
	{
		$option=new TPostBackOptions();
		$group = $this->getValidationGroup();
		$hasValidators = $this->getPage()->getValidators($group)->getCount()>0;
		if($this->getCausesValidation() && $hasValidators)
		{
			$option->setPerformValidation(true);
			$option->setValidationGroup($group);
		}
		$option->setAutoPostBack(true);
	}

}

?>