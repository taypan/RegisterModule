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

/**
 * @author Josef Kříž
 * @Entity(repositoryClass="\Venne\Developer\Doctrine\BaseRepository")
 * @Table(name="role")
 * 
 * @property string $name
 * @property \Doctrine\Common\Collections\ArrayCollection $childrens
 * @property RoleEntity $parent
 */
class RoleEntity extends \Venne\Developer\Doctrine\BaseEntity {

	/**
	 * @Column(type="string")
	 */
	protected $name;
	/**
	 * @OneToMany(targetEntity="roleEntity", mappedBy="parent")
	 */
	protected $childrens;
	/**
	 * @ManyToOne(targetEntity="roleEntity", inversedBy="id")
	 * @JoinColumn(name="role_id", referencedColumnName="id", onDelete="CASCADE")
	 * @OrderBy({"order" = "ASC"})
	 */
	protected $parent;
	/**
	 * @OneToMany(targetEntity="permissionEntity", mappedBy="role")
	 */
	protected $permissions;
	
	public function __construct() {
        $this->permissions = new \Doctrine\Common\Collections\ArrayCollection();
    }

	
}
