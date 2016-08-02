<?php
namespace App;

use app\Models\User;

class PasswordGrantVerifier
{
  
  public function verify($email, $password)
  {

    $user = User::whereEmail($email)->whereStatus(1)->firstOrFail();
    
    if (\Hash::check($password, $user->password))
      return $user->id;
    
    return false;
  }
}
?>
