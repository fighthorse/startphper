<?php
/**
* Article Model
*/
class Article extends Illuminate\Database\Eloquent\Model
{
    
   public $timestamps = false;
   
  public static function first()
  {
    $pdo = new PDO("mysql:host=localhost;dbname=mffc","root","root"); 
    if (!$pdo) {
      die('Could not connect: ' . $pdo->error());
    }
    
    $result =  $pdo -> query("SELECT * FROM articles limit 0,1");
    
    while($row = $result -> fetch()){ 
        return $row;
    }

  }
}