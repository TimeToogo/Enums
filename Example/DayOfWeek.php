<?php

require_once '../vendor/autoload.php';

final class DayOfWeek extends Enum\Simple {
    use Enum\Values {
        _ as Monday;
        _ as Tuesday;
        _ as Wednesday;
        _ as Thursday;
        _ as Friday;
        _ as Saturday;
        _ as Sunday;
    }
    
    protected function ToString($Value) {
        return 'Today could be ' . strtolower($Value) . '.';
    }
    
    public function GetTomorrow() {
        if($this === self::Sunday()) {
            return self::Monday();
        }
        else {
            $All = self::All();
            return $All[array_search($this, $All) + 1];
        }
    }
    
    public static function IsWeekEnd(self $DayOfWeek) {
        return $DayOfWeek === self::Saturday() || $DayOfWeek === self::Sunday();
    }
    
    public static function GetWeekDays() {
        return self::Filter(function (self $DayOfWeek) {
            return !self::IsWeekEnd($DayOfWeek);
        });
    }
    
    public static function GetWeekEndDays() {
        return self::Filter([__CLASS__, 'IsWeekEnd']);
    }
}


?>