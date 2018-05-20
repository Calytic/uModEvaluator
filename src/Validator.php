<?php
namespace uModEvaluator;

class Validator {
    private $info;
    
    public function validate($source) {
        $evaluator = new Evaluator();
        
        try {
            $this->info = $evaluator->evaluate($source);
        } catch(Exceptions\InvalidSourceException $ex) {
            return false;
        }
        
        return true;
    }
    
    public function getInfo() {
        return $this->info;
    }
}