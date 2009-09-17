<?php
/**
 * Profile management controller's.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage users
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

class ProfileController extends USVN_Controller
{
	private $user = null;

	protected function getUserData($data)
	{
		if (!isset($data['users_lastname'])
			|| !isset($data['users_firstname'])
			|| !isset($data['users_email'])
			|| !isset($data['users_password'])
			|| !isset($data['users_new_password'])
			|| !isset($data['users_new_password2'])
			) {
			return array();
		}
		$user = $this->getUser();
		if (!USVN_Crypt::checkPassword($data['users_password'], $user->password)) {
			throw new USVN_Exception(T_("Wrong password"));
		}
		if (!empty($data['users_new_password']) && !empty($data['users_new_password2'])) {
			if ($data['users_new_password'] !== $data['users_new_password2']) {
				throw new USVN_Exception(T_('Not the same password.'));
			}
			$data['users_password'] = $data['users_new_password'];
		}
		$user = array('users_lastname'  => $data['users_lastname'],
					  'users_firstname' => $data['users_firstname'],
					  'users_email'     => $data['users_email'],
					  'users_password'  => $data['users_password']
						);
		return $user;
	}

	protected function getUser()
	{
		if ($this->user === null) {
			$identity = Zend_Auth::getInstance()->getIdentity();
			$table = new USVN_Db_Table_Users();
			$this->user = $table->fetchRow(array('users_login = ?' => $identity['username']));
		}
		return $this->user;
	}

	public function indexAction()
	{
		$this->view->user = $this->getUser();
		if ($this->view->user === null) {
			$this->_redirect("/admin/user/");
		}
	}

	public function saveAction()
	{
		$user = $this->getUser();
		try {
			$data = $this->getUserData($_POST);
			if (empty($data)) {
				$this->_redirect("/profile/");
			}
			$user->setFromArray($data);
			$user->save();
			$this->_redirect("/");
		}
		catch (Exception $e) {
			$this->view->user = $user;
			$this->view->message = $e->getMessage();
			$this->render('index');
		}
	}
}
