<?php
/**
 * TScaffoldListView class file.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2007 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 * @package System.Data.ActiveRecord.Scaffold
 */

/**
 * Load the scaffold base class.
 */
Prado::using('System.Data.ActiveRecord.Scaffold.TScaffoldBase');

/**
 * TScaffoldListView displays a list of Active Records.
 *
 * The {@link getHeader Header} property is a TRepeater displaying the
 * Active Record property/field names. The {@link getSort Sort} property
 * is a drop down list displaying the combination of properties and its possible
 * ordering. The {@link getPager Pager} property is a TPager control that
 * determines the number of records display in one page (e.g. Page..
 *
 *
 *
 * @author Wei Zhuo <weizho[at]gmail[dot]com>
 * @version $Id$
 * @package System.Data.ActiveRecord.Scaffold
 * @since 3.1
 */
class TScaffoldListView extends TScaffoldBase
{
	/**
	 * Initialize the sort drop down list in non post back mode (i.e. GET requests).
	 */
	public function onLoad($param)
	{
		parent::onLoad($param);
		if(!$this->getPage()->getIsPostBack())
			$this->initializeSort();
	}

	/**
	 * Initialize the sort drop down list and the column names repeater.
	 */
	protected function initializeSort()
	{
		$table = $this->getTableMetaData();
		$sorts = array('Sort By', str_repeat('-',15));
		$headers = array();
		foreach($table->getColumns() as $name=>$colum)
		{
			$fname = ucwords(str_replace('_', ' ', $name));
			$sorts[$name.' ASC'] = $fname .' Ascending';
			$sorts[$name.' DESC'] = $fname .' Descending';
			$headers[] = $fname ;
		}
		$this->_sort->setDataSource($sorts);
		$this->_sort->dataBind();
		$this->_header->setDataSource($headers);
		$this->_header->dataBind();
	}

	/**
	 * Loads and display the data.
	 */
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		$this->loadRecordData();
	}

	/**
	 * Fetch the records and data bind it to the list.
	 */
	protected function loadRecordData()
	{
		$this->_list->setVirtualItemCount($this->getRecordFinder()->count());
		$finder = $this->getRecordFinder();
		$criteria = $this->getRecordCriteria();
		$this->_list->setDataSource($finder->findAll($criteria));
		$this->_list->dataBind();
	}

	/**
	 * @return TActiveRecordCriteria sort/search/paging criteria
	 */
	protected function getRecordCriteria()
	{
		$total = $this->_list->getVirtualItemCount();
		$limit = $this->_list->getPageSize();
		$offset = $this->_list->getCurrentPageIndex()*$limit;
		if($offset + $limit > $total)
			$limit = $total - $offset;
		$criteria = new TActiveRecordCriteria($this->getSearchCondition(), $this->getSearchParameters());
		$criteria->setLimit($limit);
		$criteria->setOffset($offset);
		$order = explode(' ',$this->_sort->getSelectedValue(), 2);
		if(is_array($order) && count($order) === 2)
			$criteria->OrdersBy[$order[0]] = $order[1];
		return $criteria;
	}

	/**
	 * @param string search condition, the SQL string after the WHERE clause.
	 */
	public function setSearchCondition($value)
	{
		$this->setViewState('SearchCondition', $value);
	}

	/**
	 * @param string SQL search condition for list display.
	 */
	public function getSearchCondition()
	{
		return $this->getViewState('SearchCondition');
	}

	/**
	 * @param array search parameters
	 */
	public function setSearchParameters($value)
	{
		$this->setViewState('SearchParameters', TPropertyValue::ensureArray($value),array());
	}

	/**
	 * @return array search parameters
	 */
	public function getSearchParameters()
	{
		return $this->getViewState('SearchParameters', array());
	}

	/**
	 * Continue bubbling the "edit" command, "delete" command is handled in this class.
	 */
	public function bubbleEvent($sender, $param)
	{
		switch(strtolower($param->getCommandName()))
		{
			case 'delete':
				return $this->deleteRecord($sender, $param);
			case 'edit':
				$this->initializeEdit($sender, $param);
		}
		$this->raiseBubbleEvent($this, $param);
		return true;
	}

	/**
	 * Initialize the edit view control form when EditViewID is set.
	 */
	protected function initializeEdit($sender, $param)
	{
		if(($ctrl=$this->getEditViewControl())!==null)
		{
			if($param instanceof TRepeaterCommandEventParameter)
			{
				$pk = $param->getItem()->getCustomData();
				$ctrl->setRecordPk($pk);
				$ctrl->initializeEditForm();
			}
		}
	}

	/**
	 * Deletes an Active Record.
	 */
	protected function deleteRecord($sender, $param)
	{
		if($param instanceof TRepeaterCommandEventParameter)
		{
			$pk = $param->getItem()->getCustomData();
			$this->getRecordFinder()->deleteByPk($pk);
		}
	}

	/**
	 * Initialize the default display for each Active Record item.
	 */
	protected function listItemCreated($sender, $param)
	{
		$item = $param->getItem();
		if($item instanceof IItemDataRenderer)
		{
			$type = $item->getItemType();
			if($type==TListItemType::Item || $type==TListItemType::AlternatingItem)
				$this->populateField($sender, $param);
		}
	}

	/**
	 * Sets the Record primary key to the current repeater item's CustomData.
	 * Binds the inner repeater with properties of the current Active Record.
	 */
	protected function populateField($sender, $param)
	{
		$item = $param->getItem();
		if(($data = $item->getData()) !== null)
		{
			$item->setCustomData($this->getRecordPkValues($data));
			if(($prop = $item->findControl('_properties'))!==null)
			{
				$item->_properties->setDataSource($this->getRecordPropertyValues($data));
				$item->_properties->dataBind();
			}
		}
	}

	/**
	 * Updates repeater page index with the pager new index value.
	 */
	protected function pageChanged($sender, $param)
	{
		$this->_list->setCurrentPageIndex($param->getNewPageIndex());
	}

	/**
	 * @return TRepeater Repeater control for Active Record instances.
	 */
	public function getList()
	{
		$this->ensureChildControls();
		return $this->getRegisteredObject('_list');
	}

	/**
	 * @return TPager List pager control.
	 */
	public function getPager()
	{
		$this->ensureChildControls();
		return $this->getRegisteredObject('_pager');
	}

	/**
	 * @return TDropDownList Control that displays and controls the record ordering.
	 */
	public function getSort()
	{
		$this->ensureChildControls();
		return $this->getRegisteredObject('_sort');
	}

	/**
	 * @return TRepeater Repeater control for record property names.
	 */
	public function getHeader()
	{
		$this->ensureChildControls();
		return $this->getRegisteredObject('_header');
	}

	/**
	 * @return string TScaffoldEditView control ID for editing selected Active Record.
	 */
	public function getEditViewID()
	{
		return $this->getViewState('EditViewID');
	}

	/**
	 * @param string TScaffoldEditView control ID for editing selected Active Record.
	 */
	public function setEditViewID($value)
	{
		$this->setViewState('EditViewID', $value);
	}

	/**
	 * @return TScaffoldEditView control for editing selected Active Record, null if EditViewID is not set.
	 */
	protected function getEditViewControl()
	{
		if(($id=$this->getEditViewID())!==null)
		{
			$ctrl = $this->getParent()->findControl($id);
			if($ctrl===null)
				throw new TConfigurationException('scaffold_unable_to_find_edit_view', $id);
			return $ctrl;
		}
	}
}

?>