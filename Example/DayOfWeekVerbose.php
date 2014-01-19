<?php

require_once '../vendor/autoload.php';

final class DayOfWeek extends Enum\Simple {
    
    public static function Monday() { return self::Representing(__FUNCTION__); }
    
    public static function Tuesday() { return self::Representing(__FUNCTION__); }
    
    public static function Wednesday() { return self::Representing(__FUNCTION__); }
    
    public static function Thursday() { return self::Representing(__FUNCTION__); }
    
    public static function Friday() { return self::Representing(__FUNCTION__); }
    
    public static function Saturday() { return self::Representing(__FUNCTION__); }
    
    public static function Sunday() {  return self::Representing(__FUNCTION__); }
    
    public function GetTomorrow() {
        if($this === self::Sunday()) {
            return self::Monday();
        }
        else {
            $All = self::All();
            return $All[array_search($this, $All) + 1];
        }
    }
}
                
?>