<?php

require_once 'CRM/Core/Page.php';

class CRM_Personalcampaign_Page_PersonalCampaign extends CRM_Core_Page {
	public function run() {	    
	    CRM_Utils_System::setTitle(ts('PersonalCampaigns'));

	 	$contactID = $id = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE, 0);

	    $query = "
				SELECT id,title, is_active,page_type,goal_amount,pcp_block_id
				FROM civicrm_pcp pcp
				WHERE pcp.contact_id = %1
				ORDER BY page_type, page_id";   
		$params = array(1 => array($contactID, 'Integer'));

	  	$dao = CRM_Core_DAO::executeQuery($query, $params);
	  	$results = array();

	  	while($dao->fetch()) {
		    $results[$dao->id]['title'] = $dao->title;
		    $results[$dao->id]['is_active'] = $dao->is_active ? 'Active' : 'Inactive';
		    $results[$dao->id]['page_type']  = $this->getContributionPageTitle($dao->id,$dao->page_type);		    
		    $results[$dao->id]['goal_amount'] = CRM_Utils_Money::format($dao->goal_amount, $dao->currency); 
		    $contributionDetails = $this->getContributionDetails($dao->id);		    
		    $results[$dao->id]['amout_raised'] = CRM_Utils_Money::format(0, $dao->currency);
		    $results[$dao->id]['no_of_contributions'] = 0;

		    if($contributionDetails){
		    	$results[$dao->id]['amout_raised'] = CRM_Utils_Money::format($contributionDetails[0], $dao->currency);
		    	$results[$dao->id]['no_of_contributions'] = $contributionDetails[1];
		    }

		    $results[$dao->id]['view_page_link']  = CRM_Utils_System::url('civicrm/pcp/info',
		    																'reset=1&id='.$dao->id.'&component='.$dao->page_type);
		    $results[$dao->id]['edit_page_link']  = CRM_Utils_System::url( 'civicrm/pcp/info',
		    																"action=update&reset=1&id=$dao->id&context=dashboard");
		}

		$this->assign('campignResults',$results) ;
	    parent::run();
	}

	/**
	* Function to get the contribution details
	*
	* @param $pcpId INT
	*
	* @return array()
	**/
	public function getContributionDetails($pcpId) {
	    $query = "
	              SELECT SUM(cc.total_amount) as total,count(*) as total_contributions
	              FROM civicrm_pcp pcp
	              LEFT JOIN civicrm_contribution_soft cs ON ( pcp.id = cs.pcp_id )
	              LEFT JOIN civicrm_contribution cc ON ( cs.contribution_id = cc.id)
	              WHERE pcp.id = %1 AND cc.contribution_status_id =1 AND cc.is_test = 0";
	    $params = array(1 => array($pcpId, 'Integer'));
	    $dao = CRM_Core_DAO::executeQuery($query, $params);
    	if ($dao->fetch()) {
      		return array($dao->total, $dao->total_contributions);
    	}

    	return array();
	    
	}


	/**
	 * Obtain the title of page associated with a pcp.
	 *
	 * @param int $pcpId
	 * @param $component
	 *
	 * @return int
	*/
	public static function getContributionPageTitle($pcpId, $component) {
		if ($component == 'contribute') {
		  $query = "
			SELECT cp.title
			FROM civicrm_pcp pcp
			LEFT JOIN civicrm_contribution_page as cp ON ( cp.id =  pcp.page_id )
			WHERE pcp.id = %1";
		}
		elseif ($component == 'event') {
		  $query = "
			SELECT ce.title
			FROM civicrm_pcp pcp
			LEFT JOIN civicrm_event as ce ON ( ce.id =  pcp.page_id )
			WHERE pcp.id = %1";
		}

		$params = array(1 => array($pcpId, 'Integer'));
		return CRM_Core_DAO::singleValueQuery($query, $params);
	}
}
