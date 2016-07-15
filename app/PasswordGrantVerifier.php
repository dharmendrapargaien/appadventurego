<?php
namespace App;

use app\Models\User;

class PasswordGrantVerifier
{
  
  public function verify($email, $password)
  {
        
    $user = User::whereEmail($email)->whereStatus(1)->firstOrFail();
    
    if ((\Hash::check($password, $user->password)) || (\Hash::check($password, $user->temporary_password)))
      return $user->id;
    
    return false;
  }
}
?>
