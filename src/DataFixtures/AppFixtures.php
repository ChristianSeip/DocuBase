<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Permission;
use App\Entity\User;
use App\Entity\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
	private UserPasswordHasherInterface $passwordHasher;

	public function __construct(UserPasswordHasherInterface $passwordHasher)
	{
		$this->passwordHasher = $passwordHasher;
	}

	public function load(ObjectManager $manager): void
	{
		$adminRole = new UserRole();
		$adminRole->setName('Admin')->setDescription('Default role for admins.')->setProtected(true);
		$userRole = new UserRole();
		$userRole->setName('User')->setDescription('Default role for users.')->setProtected(true);
		$guestRole = new UserRole();
		$guestRole->setName('Guest')->setDescription('Default role for guests.')->setProtected(true);
		$manager->persist($adminRole);
		$manager->persist($userRole);
		$manager->persist($guestRole);
		$permissions = [
			'can_create_article', 'can_edit_article', 'can_delete_article','can_use_acp', 'can_edit_users', 'can_delete_users',
			'can_edit_user_role_assignments', 'can_edit_categories', 'can_delete_categories', 'can_create_categories',
			'can_create_roles', 'can_edit_roles', 'can_delete_roles'
		];
		$roles = [$adminRole, $userRole, $guestRole];
		foreach ($roles as $role) {
			$value = $role->getName() === 'Admin';
			foreach ($permissions as $permission) {
				$p = new Permission();
				$p->setRole($role)->setPermissionName($permission)->setPermissionValue($value);
				$manager->persist($p);
			}
		}
		$adminUser = new User();
		$adminUser->setUsername('admin')->setEmail('admin@example.com')->setIsVerified(true)->setIsLocked(false)->addRole($adminRole)->addRole($userRole);
		$adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'admin'));
		$manager->persist($adminUser);
		$guestUser = new User();
		$guestUser->setUsername('guest')->setEmail('guest@example.com')->setIsVerified(true)->setIsLocked(false)->addRole($guestRole);
		$guestUser->setPassword($this->passwordHasher->hashPassword($guestUser, random_bytes(10)));
		$manager->persist($guestUser);
		$category = new Category();
		$category->setName('Default');
		$manager->persist($category);
		$article = new Article();
		$article->setCategory($category)
			->setTitle('Welcome')
			->setText('<h2>DocuBase</h2><p>Welcome to your DocuBase</p><h3>User Roles</h3><p>Before you use the system, I would like to advise you to make some basic settings; Both guests and users have no rights whatsoever. You can adjust these via the admin control panel (/acp/roles). Users who are not logged in automatically receive the “Guest” role, while users who register automatically receive the “User” role. You can create additional roles to refine rights management.</p><h3>Categories</h3><p>After you have set the rights, you should create the first categories and subcategories for your documentation. You can also do this via the ACP (/acp/categories). You can then fill the categories with your first articles.</p>')
			->setUser($adminUser);
		$manager->persist($article);
		$manager->flush();
	}
}
