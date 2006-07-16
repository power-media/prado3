<?php
/**
 * Project DAO class file.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2006 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Revision: $  $16/07/2006: $
 * @package Demos
 */

/**
 * Project DAO class.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version $Revision: $  $16/07/2006: $
 * @package Demos
 * @since 3.1
 */
class ProjectDao extends BaseDao
{
/*	public function createNewProject($project)
	{
		$sqlmap = $this->getConnection();
		$creator = $sqlmap->queryForObject('GetUserByName', $project->CreatorUserName);
		$manager = $sqlmap->queryForObject('GetUserByName', $project->ManagerUserName);
		$exists = $sqlmap->queryForObject('GetProjectByName', $project->Name);
		if($exists)
		{
			throw new TimeTrackerException(
				'project_exists', $project->Name);
		}
		else if(!$creator || !$manager)
		{
			throw new TimeTrackerException(
				'invalid_creator_and_manager',
				$project->Name, $project->CreatorUserName, 
				$project->ManagerUserName);	
		}
		else
		{
			$param['project'] = $project;
			$param['creator'] = $creator->ID;
			$param['manager'] = $manager->ID; 
			return $sqlmap->insert('CreateNewProject', $param);			
		}
	} 
	
	public function getProjectByID($projectID)
	{
		$sqlmap = $this->getConnection();
		return $sqlmap->queryForObject('GetProjectByID', $projectID);
	}
	
	public function addUserToProject($project, $user)
	{
		$sqlmap = $this->getConnection();
		$project = $this->getProjectByID($project->ID);
		$user = $sqlmap->queryForObject('GetUserByName', $user->Name);
		$list = $sqlmap->queryForList('GetProjectMembers', $project);
		$userExists = false;
		foreach($list as $k)
		{
			if($k->ID == $user->ID)
				$userExists = true;
		}
		if(!$project)
		{
			throw new TimeTrackerException(
				'invalid_project', $project->Name);			
		}
		else if(!$user)
		{
			throw new TimeTrackerException(
				'invalid_user', $user->Name);	
		}
		else if($userExists)
		{
			throw new TimeTrackerException(
				'project_member_exists', $projet->Name, $user->Name);
		}
		else
		{
			$param['project'] = $project;
			$param['user'] = $user;
			return $sqlmap->insert('AddUserToProject', $param);
		}	
	}
*/
}

?>