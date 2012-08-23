<?php
/**
* @version 0.0.1
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JGWallHelper {

    var $_db = null;
    var $articleid = null;
    var $jguser = null;


    function JGWallHelper() {

        $this->_db =& JFactory::getDBO();
        $this->articleid = JRequest::getInt('id');
        $this->jguser =& JFactory::getUser();
    }

    function isOwner() {


        $this->_db->setQuery('SELECT created_by FROM #__content WHERE id=' . $this->articleid);
        $ownerid = $this->_db->loadResult();

        return ($this->jguser->get('id') == $ownerid) ? true : false;
    }

    function isServiceProvider() {

        $query = 'SELECT COUNT(user_id) AS ttl FROM #__probid_friends WHERE user_id = ' . $this->jguser->get('id');
        $query .= ' AND article_id=' . $this->articleid;

        $this->_db->setQuery($query);
        $ttl = $this->_db->loadResult();
        
        return ($ttl > 0) ? true : false;
    }

    function isProjectManager() {

        $query = 'SELECT position FROM #__probid_friends WHERE user_id = ' . $this->jguser->get('id');
		$query .= ' AND article_id=' . $this->articleid;

        $this->_db->setQuery($query);
        $position = $this->_db->loadResult();

        return ($position == 1) ? true : false;

    }
	
	function hasAccepted() {
		$query = 'SELECT accepted FROM #__probid_friends WHERE user_id = ' . $this->jguser->get('id');
		$query .= ' AND article_id=' . $this->articleid;
		
		$this->_db->setQuery($query);
        $accepted = $this->_db->loadResult();
		
		return ($accepted == 1) ? true : false;
	}

    function addToWall($userid, $articleid) {
        $query = 'INSERT INTO #__probid_friends(user_id, article_id) VALUES ('. $userid . ', '. $articleid .')';
        $this->_db->setQuery($query);
        $this->_db->query();

    }

    function removeFromWall($userid, $articleid) {
        $query = 'DELETE FROM #__probid_friends WHERE user_id = ' . $userid . ' AND article_id = ' . $articleid;
        $this->_db->setQuery($query);
        $this->_db->query();
    }

    function getWallServiceProviders($articleid) {
        $query = 'SELECT * FROM #__probid_friends WHERE article_id = ' . $articleid;
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }

    function getServiceProviders() {
        //TODO: ADD QUERY WITH POSSIBLE FILTER PASSED IN TO FIND SERVICE PROVIDERS TO ADD TO WALL
        return true;
    }
	
	function isAwarded() {
		$query = 'SELECT TRIM(jr_projectstatus) FROM #__jreviews_content WHERE contentid = ' . $this->articleid;
		
		$this->_db->setQuery($query);
        $projectStatus = $this->_db->loadResult();
		
		return ($projectStatus == '*awarded*') ? true : false;
		
	}


}