<?php

namespace App\Security;

use App\Entity\Pengingat;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PengingatVoter extends Voter
{
    private const SHOW = 'show';
    private const EDIT = 'edit';
    private const DELETE = 'delete';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Pengingat && in_array($attribute, [self::SHOW, self::EDIT, self::DELETE], true);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $pengingat, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $user->getId() === $pengingat->getUser()->getId();
    }
}
