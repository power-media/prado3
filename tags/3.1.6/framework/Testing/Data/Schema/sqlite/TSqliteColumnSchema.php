<?php
/**
 * TSqliteColumnSchema class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2009 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

prado::using('System.Testing.Data.Schema.TDbColumnSchema');

/**
 * TSqliteColumnSchema class describes the column meta data of a SQLite table.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: TSqliteColumnSchema.php 2679 2009-06-15 07:49:42Z Christophe.Boulain $
 * @package System.Testing.Data.Schema.sqlite
 * @since 1.0
 */
class TSqliteColumnSchema extends TDbColumnSchema
{
	/**
	 * Extracts the default value for the column.
	 * The value is typecasted to correct PHP type.
	 * @param mixed the default value obtained from metadata
	 */
	protected function extractDefault($defaultValue)
	{
		if($this->type==='string') // PHP 5.2.6 adds single quotes while 5.2.0 doesn't
			$this->defaultValue=trim($defaultValue,"'\"");
		else
			$this->defaultValue=$this->typecast($defaultValue);
	}
}
