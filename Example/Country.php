<?php

require_once '../vendor/autoload.php';

final class Country extends Enum\Strict {
    
    public static function USA() {
        return self::Representing([
            'Name' => 'United States of America',
            'Population' => 317500000,
            'Area' => 9826675
        ]);
    }
    
    public static function Australia() {
        return self::Representing([
            'Name' => 'Australia',
            'Population' => 23351119,
            'Area' => 7692024
        ]);
    }
    
    public static function FromName($Name) {
        return self::FirstOrDefault(
                function ($Value) use ($Name) {
                    return $Value['Name'] === $Name;
                });
    }
    
    protected function ToString($Value) {
        return $Value['Name'];
    }
    
    public function PopulationDensity() {
        $Country = $this->GetValue();
        
        return $Country['Population'] / $Country['Area'];
    }
}

var_dump((string)Country::USA());
var_dump(Country::USA()->PopulationDensity());

var_dump((string)Country::Australia());
var_dump(Country::Australia()->PopulationDensity());

?>