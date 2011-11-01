<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
*
* Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
*
* For the full copyright and license information, please view
* the file license.txt that was distributed with this source code.
*/

namespace App\RegisterModule;

use \Nette;
/**
 * @author Josef Kříž
 * @Entity(repositoryClass="\Venne\Developer\Doctrine\BaseRepository")
 * @Table(name="user")
 *
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $salt
 * @property array $roles
 */
class UserEntity extends \Nette\Security\Identity {

	protected $roleNames;

	protected $data = array();

	public function __construct()
	{
		$this->roles = new \Doctrine\Common\Collections\ArrayCollection();
		$this->key = "";
	}

	/**
	 * @id
	 * @generatedValue
	 * @column(type="integer")
	 */
	public $id;
	/**
	 * @Column(type="boolean")
	 */
	public $enable;
	/**
	 * @Column(type="string")
	 */
	public $name;
	/**
	 * @Column(type="string")
	 */
	public $email;
	/**
	 * @Column(type="string")
	 */
	public $password;
	/**
	 * @Column(type="string", name="`key`")
	 */
	public $key;
	/**
	 * @Column(type="string")
	 */
	public $salt;
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ManyToMany(targetEntity="RoleEntity", indexBy="id")
	 * @JoinTable(name="users_roles",
	 *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@JoinColumn(name="role_id", referencedColumnName="id")}
	 *      )
	 */
	protected $roles;

	/**
	 * @Column(type="string")
	 */
	public $firstname;
	
	/**
	 * @Column(type="string")
	 */
	public $lastname;

	
	/**
	* @Column(type="string")
	*/
	public $street;
	
	/**
	* @Column(type="date")
	*/
	public $birthdate;
	
	/**
	* @Column(type="integer")
	*/
	public $streetnumber;
	
	/**
	* @Column(type="integer")
	*/
	public $zipcode;
	
	
	/**
	* @Column(type="boolean")
	*/
	public $sendmail;
	
	/**
	* @Column(type="string")
	*/
	public $city;
	
	/**
	 * @param type \Venne\Modules\Role
	 */
	public function addRole($role)
	{
		$this->roles[] = $role;
	}

	/**
	 * @param type \Venne\Modules\Role
	 */
	public function removeRole($role)
	{
		$this->roles->removeElement($role);
	}

	public function getRoleEntities()
	{
		return $this->roles;
	}


	/**
	 * Sets a list of roles that the user is a member of.
	 * @param  array
	 * @return Identity  provides a fluent interface
	 */
	public function setRoles(array $roles)
	{
		$this->roleNames = $roles;
		return $this;
	}

	/**
	 * Returns a list of roles that the user is a member of.
	 * @return array
	 */
	public function getRoles()
	{
		if(!$this->roleNames){
			$ret = array();
			foreach ($this->roles as $role){
				$ret[] = $role->name;
			}
			$this->roleNames = $ret;
		}
		return $this->roleNames;
	}



	/**
	 * Returns a user data.
	 * @return array
	 */
	public function getData()
	{
		return array(
			"name"=>$this->name,
			"email"=>$this->email,
			"salt"=>$this->salt,
			"id"=>$this->id
		);
	}

	/**
	 * Returns a user data.
	 * @return array
	 */
	public function setData($array)
	{
		$this->name = $array["name"];
		$this->email = $array["email"];
		$this->salt = $array["salt"];
		$this->id = $array["id"];
	}


	public function getId()
	{
		return $this->id;
	}

}
