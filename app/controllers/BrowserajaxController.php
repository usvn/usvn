<?php
/**
 * Controller of browser module
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package browser
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class BrowserajaxController extends USVN_Controller
{
    protected $_mimetype = 'text/xml';

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_helper->layout()->disableLayout();
        $this->_project = str_replace(USVN_URL_SEP, USVN_DIRECTORY_SEPARATOR, $this->getRequest()->getParam('project'));
        $this->_path = $this->getRequest()->getParam('path');
    }

    public function postDispatch()
    {
        parent::postDispatch();
    }

    public function dumprightsAction()
    {
        $table_project = new USVN_Db_Table_Projects();
        $table_groupstoproject = new USVN_Db_Table_GroupsToProjects();
        $table_groups = new USVN_Db_Table_Groups();
        $res_project = $table_project->findByName($this->_project);
        $access_rights = new USVN_FilesAccessRights($res_project->projects_id);
        $res_groupstoproject = $table_groupstoproject->findByProjectId($res_project->projects_id);

        $d = '0';
        $identity = Zend_Auth::getInstance()->getIdentity();
        if (!$res_project->userIsAdmin($identity['username']))
            $d = '1';

        $dom = new DOMDocument();
        $root = $dom->createElement('root');
        $dom->appendChild($root);
        foreach ($res_groupstoproject as $group)
        {
            $access = $access_rights->findByPath($group->groups_id, $this->_path);
            $res_group = $table_groups->findByGroupsId($group->groups_id);
            $grp_name = $res_group->groups_name;

            # group
            $group = $dom->createElement('group');
            $root->appendChild($group);
            # disabled
            $disabled = $dom->createElement('disabled');
            $text = $dom->createTextNode($d);
            $disabled->appendChild($text);
            $group->appendChild($disabled);
            # name
            $name = $dom->createElement('name');
            $text = $dom->createTextNode($grp_name);
            $name->appendChild($text);
            $group->appendChild($name);
            # read
            if ($access['read'] == 1)
                $r = '1';
            else
                $r = '0';
            $read = $dom->createElement('read');
            $text = $dom->createTextNode($r);
            $read->appendChild($text);
            $group->appendChild($read);
            # write
            if ($access['write'] == 1)
                $w = '1';
            else
                $w = '0';
            $write = $dom->createElement('write');
            $text = $dom->createTextNode($w);
            $write->appendChild($text);
            $group->appendChild($write);
        }
        echo $dom->saveXML();
    }

    public function updaterightsAction()
    {
        $this->newRights = $this->getRequest()->getParam('newRights');

        $table_project = new USVN_Db_Table_Projects();
        $res_project = $table_project->findByName($this->_project);
        $access_rights = new USVN_FilesAccessRights($res_project->projects_id);
        $table_group = new USVN_Db_Table_Groups();

        $identity = Zend_Auth::getInstance()->getIdentity();
        if (!$res_project->userIsAdmin($identity['username']))
            $this->_redirect("/");

        $tabNewRights = explode(',', $this->newRights);
        $group = $tabNewRights[0];
        $read = $tabNewRights[1];
        $write = $tabNewRights[2];

        $res_groups = $table_group->findByGroupsName($group);
        $access_rights->setRightByPath(
            $res_groups->groups_id,
            $this->_path,
            ($read == '1' ? True : False),
            ($write == '1' ? True : False),
            (false)
        );
    }
}

