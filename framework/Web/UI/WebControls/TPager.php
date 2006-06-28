<?php
/**
 * TPager class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Revision: $  $Date: $
 * @package System.Web.UI.WebControls
 */

/**
 * TPager class.
 *
 * TPager creates a pager that provides UI for end-users to interactively
 * specify which page of data to be rendered in a {@link TDataBoundControl}-derived control,
 * such as {@link TDataList}, {@link TRepeater}, {@link TCheckBoxList}, etc.
 * The target data-bound control is specified by {@link setControlToPaginate ControlToPaginate},
 * which must be the ID path of the target control reaching from the pager's
 * naming container. Note, the target control must have its {@link TDataBoundControl::setAllowPaging AllowPaging}
 * set to true.
 *
 * TPager can display three different UIs, specified via {@link setMode Mode}:
 * - NextPrev: a next page and a previous page button are rendered.
 * - Numeric: a list of page index buttons are rendered.
 * - List: a dropdown list of page indices are rendered.
 *
 * TPager raises an {@link onPageIndexChanged OnPageIndexChanged} event when
 * the end-user interacts with it and specifies a new page (e.g. clicking
 * on a page button that leads to a new page.) The new page index may be obtained
 * from the event parameter's property {@link TPagerPageChangedEventParameter::getNewPageIndex NewPageIndex}.
 * Normally, in the event handler, one can set the {@link TDataBoundControl::getCurrentPageIndex CurrentPageIndex}
 * to this new page index so that the new page of data is rendered.
 *
 * Multiple pagers can be associated with the same data-bound control.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System.Web.UI.WebControls
 * @since 3.0.2
 */
class TPager extends TWebControl implements INamingContainer
{
	/**
	 * Command name that TPager understands.
	 */
	const CMD_PAGE='Page';
	const CMD_PAGE_NEXT='Next';
	const CMD_PAGE_PREV='Previous';
	const CMD_PAGE_FIRST='First';
	const CMD_PAGE_LAST='Last';

	/**
	 * Restores the pager state.
	 * This method overrides the parent implementation and is invoked when
	 * the control is loading persistent state.
	 */
	public function loadState()
	{
		parent::loadState();
		if($this->getEnableViewState(true))
		{
			$this->getControls()->clear();
			$this->buildPager();
		}
	}

	/**
	 * @return string the ID path of the control whose content would be paginated.
	 */
	public function getControlToPaginate()
	{
		return $this->getViewState('ControlToPaginate','');
	}

	/**
	 * Sets the ID path of the control whose content would be paginated.
	 * The ID path is the dot-connected IDs of the controls reaching from
	 * the pager's naming container to the target control.
	 * @param string the ID path
	 */
	public function setControlToPaginate($value)
	{
		$this->setViewState('ControlToPaginate',$value,'');
	}

	/**
	 * @return string pager mode. Defaults to 'NextPrev'.
	 */
	public function getMode()
	{
		return $this->getViewState('Mode','NextPrev');
	}

	/**
	 * @param string pager mode. Valid values include 'NextPrev', 'Numeric' and 'List'.
	 */
	public function setMode($value)
	{
		$this->setViewState('Mode',TPropertyValue::ensureEnum($value,'NextPrev','Numeric','List'),'NextPrev');
	}

	/**
	 * @return string the type of command button for paging. Defaults to 'LinkButton'.
	 */
	public function getButtonType()
	{
		return $this->getViewState('ButtonType','LinkButton');
	}

	/**
	 * @param string the type of command button for paging. Valid values include 'LinkButton' and 'PushButton'.
	 */
	public function setButtonType($value)
	{
		$this->setViewState('ButtonType',TPropertyValue::ensureEnum($value,'LinkButton','PushButton'));
	}

	/**
	 * @return string text for the next page button. Defaults to '>'.
	 */
	public function getNextPageText()
	{
		return $this->getViewState('NextPageText','>');
	}

	/**
	 * @param string text for the next page button.
	 */
	public function setNextPageText($value)
	{
		$this->setViewState('NextPageText',$value,'>');
	}

	/**
	 * @return string text for the previous page button. Defaults to '<'.
	 */
	public function getPrevPageText()
	{
		return $this->getViewState('PrevPageText','<');
	}

	/**
	 * @param string text for the next page button.
	 */
	public function setPrevPageText($value)
	{
		$this->setViewState('PrevPageText',$value,'<');
	}

	/**
	 * @return string text for the first page button. Defaults to '<<'.
	 */
	public function getFirstPageText()
	{
		return $this->getViewState('FirstPageText','<<');
	}

	/**
	 * @param string text for the first page button. If empty, the first page button will not be rendered.
	 */
	public function setFirstPageText($value)
	{
		$this->setViewState('FirstPageText',$value,'<<');
	}

	/**
	 * @return string text for the last page button. Defaults to '>>'.
	 */
	public function getLastPageText()
	{
		return $this->getViewState('LastPageText','>>');
	}

	/**
	 * @param string text for the last page button. If empty, the last page button will not be rendered.
	 */
	public function setLastPageText($value)
	{
		$this->setViewState('LastPageText',$value,'>>');
	}

	/**
	 * @return integer maximum number of pager buttons to be displayed. Defaults to 10.
	 */
	public function getPageButtonCount()
	{
		return $this->getViewState('PageButtonCount',10);
	}

	/**
	 * @param integer maximum number of pager buttons to be displayed
	 * @throws TInvalidDataValueException if the value is less than 1.
	 */
	public function setPageButtonCount($value)
	{
		if(($value=TPropertyValue::ensureInteger($value))<1)
			throw new TInvalidDataValueException('pager_pagebuttoncount_invalid');
		$this->setViewState('PageButtonCount',$value,10);
	}

	/**
	 * @return integer the zero-based index of the current page. Defaults to 0.
	 */
	public function getCurrentPageIndex()
	{
		return $this->getViewState('CurrentPageIndex',0);
	}

	/**
	 * @param integer the zero-based index of the current page
	 * @throws TInvalidDataValueException if the value is less than 0
	 */
	protected function setCurrentPageIndex($value)
	{
		if(($value=TPropertyValue::ensureInteger($value))<0)
			throw new TInvalidDataValueException('pager_currentpageindex_invalid');
		$this->setViewState('CurrentPageIndex',$value,0);
	}

	/**
	 * @return integer number of pages of data items available
	 */
	public function getPageCount()
	{
		return $this->getViewState('PageCount',0);
	}

	/**
	 * @param integer number of pages of data items available
	 * @throws TInvalidDataValueException if the value is less than 0
	 */
	protected function setPageCount($value)
	{
		if(($value=TPropertyValue::ensureInteger($value))<0)
			throw new TInvalidDataValueException('pager_pagecount_invalid');
		$this->setViewState('PageCount',$value,0);
	}

	/**
	 * @return boolean whether the current page is the first page Defaults to false.
	 */
	public function getIsFirstPage()
	{
		return $this->getCurrentPageIndex()===0;
	}

	/**
	 * @return boolean whether the current page is the last page
	 */
	public function getIsLastPage()
	{
		return $this->getCurrentPageIndex()===$this->getPageCount()-1;
	}

	/**
	 * Performs databinding to populate data items from data source.
	 * This method is invoked by {@link dataBind()}.
	 * You may override this function to provide your own way of data population.
	 * @param Traversable the bound data
	 */
	public function onPreRender($param)
	{
		parent::onPreRender($param);

		$controlID=$this->getControlToPaginate();
		if(($targetControl=$this->getNamingContainer()->findControl($controlID))===null || !($targetControl instanceof TDataBoundControl))
			throw new TConfigurationException('pager_controltopaginate_invalid',$controlID);

		if($targetControl->getAllowPaging() && $targetControl->getPageCount()>1)
		{
			$this->setVisible(true);
			$this->getControls()->clear();
			$this->setPageCount($targetControl->getPageCount());
			$this->setCurrentPageIndex($targetControl->getCurrentPageIndex());
			$this->buildPager();
		}
		else
			$this->setVisible(false);
	}

	/**
	 * Builds the pager content based on the pager mode.
	 * Current implementation includes building 'NextPrev', 'Numeric' and 'List' pagers.
	 * Derived classes may override this method to provide additional pagers.
	 */
	protected function buildPager()
	{
		switch($this->getMode())
		{
			case 'NextPrev':
				$this->buildNextPrevPager();
				break;
			case 'Numeric':
				$this->buildNumericPager();
				break;
			case 'List':
				$this->buildListPager();
				break;
		}
	}

	/**
	 * Creates a pager button.
	 * Depending on the button type, a TLinkButton or a TButton may be created.
	 * If it is enabled (clickable), its command name and parameter will also be set.
	 * Derived classes may override this method to create additional types of buttons, such as TImageButton.
	 * @param string button type, either LinkButton or PushButton
	 * @param boolean whether the button should be enabled
	 * @param string caption of the button
	 * @param string CommandName corresponding to the OnCommand event of the button
	 * @param string CommandParameter corresponding to the OnCommand event of the button
	 * @return mixed the button instance
	 */
	protected function createPagerButton($buttonType,$enabled,$text,$commandName,$commandParameter)
	{
		if($buttonType==='LinkButton')
		{
			if($enabled)
				$button=new TLinkButton;
			else
			{
				$button=new TLabel;
				$button->setText($text);
				return $button;
			}
		}
		else
		{
			$button=new TButton;
			if(!$enabled)
				$button->setEnabled(false);
		}
		$button->setText($text);
		$button->setCommandName($commandName);
		$button->setCommandParameter($commandParameter);
		$button->setCausesValidation(false);
		return $button;
	}

	/**
	 * Builds a next-prev pager
	 */
	protected function buildNextPrevPager()
	{
		$buttonType=$this->getButtonType();
		$controls=$this->getControls();
		if($this->getIsFirstPage())
		{
			if(($text=$this->getFirstPageText())!=='')
			{
				$label=$this->createPagerButton($buttonType,false,$text,'','');
				$controls->add($label);
				$controls->add("\n");
			}
			$label=$this->createPagerButton($buttonType,false,$this->getPrevPageText(),'','');
			$controls->add($label);
		}
		else
		{
			if(($text=$this->getFirstPageText())!=='')
			{
				$button=$this->createPagerButton($buttonType,true,$text,self::CMD_PAGE_FIRST,'');
				$controls->add($button);
				$controls->add("\n");
			}
			$button=$this->createPagerButton($buttonType,true,$this->getPrevPageText(),self::CMD_PAGE_PREV,'');
			$controls->add($button);
		}
		$controls->add("\n");
		if($this->getIsLastPage())
		{
			$label=$this->createPagerButton($buttonType,false,$this->getNextPageText(),'','');
			$controls->add($label);
			if(($text=$this->getLastPageText())!=='')
			{
				$controls->add("\n");
				$label=$this->createPagerButton($buttonType,false,$text,'','');
				$controls->add($label);
			}
		}
		else
		{
			$button=$this->createPagerButton($buttonType,true,$this->getNextPageText(),self::CMD_PAGE_NEXT,'');
			$controls->add($button);
			if(($text=$this->getLastPageText())!=='')
			{
				$controls->add("\n");
				$button=$this->createPagerButton($buttonType,true,$text,self::CMD_PAGE_LAST,'');
				$controls->add($button);
			}
		}
	}

	/**
	 * Builds a numeric pager
	 */
	protected function buildNumericPager()
	{
		$buttonType=$this->getButtonType();
		$controls=$this->getControls();
		$pageCount=$this->getPageCount();
		$pageIndex=$this->getCurrentPageIndex()+1;
		$maxButtonCount=$this->getPageButtonCount();
		$buttonCount=$maxButtonCount>$pageCount?$pageCount:$maxButtonCount;
		$startPageIndex=1;
		$endPageIndex=$buttonCount;
		if($pageIndex>$endPageIndex)
		{
			$startPageIndex=((int)(($pageIndex-1)/$maxButtonCount))*$maxButtonCount+1;
			if(($endPageIndex=$startPageIndex+$maxButtonCount-1)>$pageCount)
				$endPageIndex=$pageCount;
			if($endPageIndex-$startPageIndex+1<$maxButtonCount)
			{
				if(($startPageIndex=$endPageIndex-$maxButtonCount+1)<1)
					$startPageIndex=1;
			}
		}

		if($startPageIndex>1)
		{
			if(($text=$this->getFirstPageText())!=='')
			{
				$button=$this->createPagerButton($buttonType,true,$text,self::CMD_PAGE_FIRST,'');
				$controls->add($button);
				$controls->add("\n");
			}
			$prevPageIndex=$startPageIndex-1;
			$button=$this->createPagerButton($buttonType,true,$this->getPrevPageText(),self::CMD_PAGE,"$prevPageIndex");
			$controls->add($button);
			$controls->add("\n");
		}

		for($i=$startPageIndex;$i<=$endPageIndex;++$i)
		{
			if($i===$pageIndex)
			{
				$label=$this->createPagerButton($buttonType,false,"$i",'','');
				$controls->add($label);
			}
			else
			{
				$button=$this->createPagerButton($buttonType,true,"$i",self::CMD_PAGE,"$i");
				$controls->add($button);
			}
			if($i<$endPageIndex)
				$controls->add("\n");
		}

		if($pageCount>$endPageIndex)
		{
			$controls->add("\n");
			$nextPageIndex=$endPageIndex+1;
			$button=$this->createPagerButton($buttonType,true,$this->getNextPageText(),self::CMD_PAGE,"$nextPageIndex");
			$controls->add($button);
			if(($text=$this->getLastPageText())!=='')
			{
				$controls->add("\n");
				$button=$this->createPagerButton($buttonType,true,$text,self::CMD_PAGE_LAST,'');
				$controls->add($button);
			}
		}
	}

	/**
	 * Builds a dropdown list pager
	 */
	protected function buildListPager()
	{
		$list=new TDropDownList;
		$this->getControls()->add($list);
		$list->setDataSource(range(1,$this->getPageCount()));
		$list->dataBind();
		$list->setSelectedIndex($this->getCurrentPageIndex());
		$list->setAutoPostBack(true);
		$list->attachEventHandler('OnSelectedIndexChanged',array($this,'listIndexChanged'));
	}

	/**
	 * Event handler to the OnSelectedIndexChanged event of the dropdown list.
	 * This handler will raise {@link onPageIndexChanged OnPageIndexChanged} event.
	 * @param TDropDownList the dropdown list control raising the event
	 * @param TEventParameter event parameter
	 */
	public function listIndexChanged($sender,$param)
	{
		$pageIndex=$sender->getSelectedIndex();
		$this->onPageIndexChanged(new TPagerPageChangedEventParameter($sender,$pageIndex));
	}

	/**
	 * This event is raised when page index is changed due to a page button click.
	 * @param TPagerPageChangedEventParameter event parameter
	 */
	public function onPageIndexChanged($param)
	{
		$this->raiseEvent('OnPageIndexChanged',$this,$param);
	}

	/**
	 * Processes a bubbled event.
	 * This method overrides parent's implementation by wrapping event parameter
	 * for <b>OnCommand</b> event with item information.
	 * @param TControl the sender of the event
	 * @param TEventParameter event parameter
	 * @return boolean whether the event bubbling should stop here.
	 */
	public function bubbleEvent($sender,$param)
	{
		if($param instanceof TCommandEventParameter)
		{
			$command=$param->getCommandName();
			if(strcasecmp($command,self::CMD_PAGE)===0)
			{
				$pageIndex=TPropertyValue::ensureInteger($param->getCommandParameter())-1;
				$this->onPageIndexChanged(new TPagerPageChangedEventParameter($sender,$pageIndex));
				return true;
			}
			else if(strcasecmp($command,self::CMD_PAGE_NEXT)===0)
			{
				$pageIndex=$this->getCurrentPageIndex()+1;
				$this->onPageIndexChanged(new TPagerPageChangedEventParameter($sender,$pageIndex));
				return true;
			}
			else if(strcasecmp($command,self::CMD_PAGE_PREV)===0)
			{
				$pageIndex=$this->getCurrentPageIndex()-1;
				$this->onPageIndexChanged(new TPagerPageChangedEventParameter($sender,$pageIndex));
				return true;
			}
			else if(strcasecmp($command,self::CMD_PAGE_FIRST)===0)
			{
				$this->onPageIndexChanged(new TPagerPageChangedEventParameter($sender,0));
				return true;
			}
			else if(strcasecmp($command,self::CMD_PAGE_LAST)===0)
			{
				$this->onPageIndexChanged(new TPagerPageChangedEventParameter($sender,$this->getPageCount()-1));
				return true;
			}
			return false;
		}
		else
			return false;
	}
}

/**
 * TPagerPageChangedEventParameter class
 *
 * TPagerPageChangedEventParameter encapsulates the parameter data for
 * {@link TPager::onPageIndexChanged PageIndexChanged} event of {@link TPager} controls.
 *
 * The {@link getCommandSource CommandSource} property refers to the control
 * that originally raises the OnCommand event, while {@link getNewPageIndex NewPageIndex}
 * returns the new page index carried with the page command.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System.Web.UI.WebControls
 * @since 3.0.2
 */
class TPagerPageChangedEventParameter extends TEventParameter
{
	/**
	 * @var integer new page index
	 */
	private $_newIndex;
	/**
	 * @var TControl original event sender
	 */
	private $_source=null;

	/**
	 * Constructor.
	 * @param TControl the control originally raises the <b>OnCommand</b> event.
	 * @param integer new page index
	 */
	public function __construct($source,$newPageIndex)
	{
		$this->_source=$source;
		$this->_newIndex=$newPageIndex;
	}

	/**
	 * @return TControl the control originally raises the <b>OnCommand</b> event.
	 */
	public function getCommandSource()
	{
		return $this->_source;
	}

	/**
	 * @return integer new page index
	 */
	public function getNewPageIndex()
	{
		return $this->_newIndex;
	}
}

?>