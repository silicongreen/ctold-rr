<?php

namespace Entity;

/**
 * User Model
 *
 * @Entity
 * @Table(name="tds_admin")
 * @author  Joseph Wynn <joseph@wildlyinaccurate.com>
 */
class User
{

	/**
	 * @Id
	 * @Column(type="integer", nullable=false)
	 * @GeneratedValue(strategy="AUTO")
	 */
	protected $admin_id;

	/**
	 * @Column(type="string", length=32, unique=true, nullable=false)
	 */
	protected $admin_name;

	/**
	 * @Column(type="string", length=64, nullable=false)
	 */
	protected $admin_password;

	/**
	 * @Column(type="string", length=255, nullable=false)
	 */
	protected $admin_email;
        
        /**
	 * @Column(type="string", length=255, nullable=false)
	 */
	protected $salt;
        /**
	 * @Column(type="integer", nullable=false)
	 */
	
        protected $group_id;
	

	/**
	 * Encrypt the password before we store it
	 *
	 * @param	string	$password
	 * @return	void
	 */
	public function setPassword($password)
	{
		$this->admin_password = $this->hashPassword($password);
	}

	/**
	 * Encrypt a Password
	 *
	 * @param	string	$password
	 * @return	string
	 */
	public function hashPassword($password)
	{
		if ( ! $this->admin_name)
		{
			throw new \Exception('The username must be set before the password can be hashed.');
		}

		return hash('sha256', $password . $this->admin_name);
	}

	public function setUsername($admin_name)
	{
		$this->admin_name = $admin_name;
		return $this;
	}

	public function setEmail($admin_email)
	{
		$this->admin_email = $admin_email;
		return $this;
	}
        public function setGroupId($group_id)
	{
		$this->group_id = $group_id;
		return $this;
	}
        public function setSalt($salt)
	{
		$this->salt = $salt;
		return $this;
	}

	public function getId()
	{
		return $this->admin_id;
	}

	public function getUsername()
	{
		return $this->admin_name;
	}

	public function getEmail()
	{
		return $this->admin_email;
	}
        public function getSalt()
	{
		return $this->Salt;
	}
        public function getGroupId()
	{
		return $this->group_id;
	}

	public function getPassword()
	{
		return $this->admin_password;
	}

	

}
