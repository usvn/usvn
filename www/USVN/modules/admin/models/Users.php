<?php

class USVN_modules_admin_models_Users extends USVN_modules_default_models_Users {
	/**
	 * The primary key column (underscore format).
	 *
	 * Without our prefixe...
	 *
	 * @var string
	 */
	protected $_primary = "id";

	/**
	 * The field's prefix for this table.
	 *
	 * @var string
	 */
	protected $_fieldPrefix = "users_";

	/**
	 * The table name derived from the class name (underscore format).
	 *
	 * @var array
	 */
    protected $_name = "users";

    /**
     * Add user in database
     * @param string $login user's login
     * @param string $passwd user's password
     * @param string $name user's name
     * @param string $firstname user's firstname
     * @param string $email user's email
     * @return bool action performed or not
     */
    public function add($login, $password, $lastname = '', $firstname = '', $email = '')
    {
    	//voir pour la notification par mail de la creation de user
    	//Penser a la verification de l'adresse, pas vu de fonction dans Zend_mail
    	//require_once 'Zend/Mail.php';
    	//$mail = new Zend_Mail();
    	//voir pour les prefixes mais _setup pas appele ? PB du controller adminIndex
		$row = array(
					'users_login' 		=> $login,
					'users_password' 	=> $password,
					'users_lastname' 	=> $lastname,
					'users_firstname' 	=> $firstname,
					'users_email' 		=> $email
					);

		$id = $this->insert($row);
    }

    /**
     * Edit a user
     *
     */
    public function edit()
    {

    }

    /**
     * Delete a user
     *
     * @param integer $id
     */
    public function deleteUser($id)
    {
		$db = $this->getAdapter();
		$where = $db->quoteInto('users_id = ?', $id);
		$rows_affected = $this->delete($where);
    }

    /**
     * List all users
     *
     * @see keep it or not
     * @return array
     */
    public function listAll()
    {
    	$table = new USVN_modules_default_models_Users();
		$users = $table->fetchAll();
		return $users;
    }
}
