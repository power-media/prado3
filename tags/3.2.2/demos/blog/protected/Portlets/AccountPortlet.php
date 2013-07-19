<?php
/**
 * AccountPortlet class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2006 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 */

Prado::using('Application.Portlets.Portlet');

/**
 * AccountPortlet class
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2006 PradoSoft
 * @license http://www.pradosoft.com/license/
 */
class AccountPortlet extends Portlet
{
	public function logout($sender,$param)
	{
		$this->Application->getModule('auth')->logout();
		$this->Response->reload();
	}
}

