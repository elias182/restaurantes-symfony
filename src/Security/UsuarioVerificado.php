<?php
namespace App\Security;
use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException; 
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
class UsuarioVerificado implements UserCheckerInterface
{
public function checkPreAuth(UserInterface $user): void
{
if (!$user instanceof AppUser) {
 return;
}
if (!$user->isVerified()) {
// the message passed to this exception is meant to be displayed to the user
throw new CustomUserMessageAccountStatusException('Todavía no se ha verificado tu cuenta. Mira en tu correo.');
}
}
public function checkPostAuth(UserInterface $user): void
{


}



}