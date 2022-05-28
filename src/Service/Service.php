<?php 
namespace App\Service;
class Service{
    public function aleatoire(int $int)
    {
      $string ="";
      $chaine ="abcdefghijklmnopqrstuvwxyz0123456789";
      srand((double)microtime()*1000000);
      for($i=0; $i< $int;$i++)
      {
        $string.=$chaine[rand()%strlen($chaine)];
      }
      return $string;
    }
}