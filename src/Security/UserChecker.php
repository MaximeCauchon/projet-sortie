<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

	/**
	 * @inheritDoc
	 */
	public function checkPreAuth(UserInterface $user)
	{
		if (!$user->isIsActif()) {
			throw new CustomUserMessageAccountStatusException('Vous avez été éjecté de la plateforme.');
		}
	}

	/**
	 * @inheritDoc
	 */
	public function checkPostAuth(UserInterface $user)
	{
		// TODO: Implement checkPostAuth() method.
	}
}