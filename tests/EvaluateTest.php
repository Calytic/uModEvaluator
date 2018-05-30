<?php
namespace uMod\Tests;

class EvaluateTest extends \PHPUnit\Framework\TestCase {
    private function getMockSource($name) {
        $testPluginPath = __DIR__.DIRECTORY_SEPARATOR.'Mock'.DIRECTORY_SEPARATOR.$name.'.cs';
        
        return file_get_contents($testPluginPath);
    }
    
    function testGetVersion() {
        $source = $this->getMockSource('TestPlugin');
        
        $evaluator = new \uMod\Evaluator\Evaluator();
        
        $pluginInfo = $evaluator->evaluate($source);
        
        $this->assertEquals("0.1.1", $pluginInfo->version);
    }
    
    function testGetAuthor() {
        $source = $this->getMockSource('TestPlugin');
        
        $evaluator = new \uMod\Evaluator\Evaluator();
        
        $pluginInfo = $evaluator->evaluate($source);
        
        $this->assertEquals("Calytic", $pluginInfo->author);
    }
    
    function testGetClass() {
        $source = $this->getMockSource('TestPlugin');
        
        $evaluator = new \uMod\Evaluator\Evaluator();
        
        $pluginInfo = $evaluator->evaluate($source);
        
        $this->assertEquals("TestPlugin", $pluginInfo->className);
    }
    
    function testGetNamespace() {
        $source = $this->getMockSource('TestPlugin');
        
        $evaluator = new \uMod\Evaluator\Evaluator();
        
        $pluginInfo = $evaluator->evaluate($source);
        
        $this->assertEquals("Oxide.Plugins", $pluginInfo->namespace);
    }
    
    function testGetTitle() {
        $source = $this->getMockSource('TestPlugin');
        
        $evaluator = new \uMod\Evaluator\Evaluator();
        
        $pluginInfo = $evaluator->evaluate($source);
        
        $this->assertEquals("TestPlugin", $pluginInfo->title);
    }
    
    function testInvalidAuthor() {
        $source = $this->getMockSource('InvalidAuthor');
        
        $this->expectException(\uMod\Evaluator\Exceptions\NoInfoAuthorException::class);
        
        $evaluator = new \uMod\Evaluator\Evaluator();
        
        $evaluator->evaluate($source);
    }
    
    function testInvalidClass() {
        $source = $this->getMockSource('InvalidClass');
        
        $this->expectException(\uMod\Evaluator\Exceptions\InvalidSourceException::class);
        
        $evaluator = new \uMod\Evaluator\Evaluator();
        
        $evaluator->evaluate($source);
    }
    
    function testInvalidInfo() {
        $source = $this->getMockSource('InvalidInfo');
        
        $this->expectException(\uMod\Evaluator\Exceptions\NoInfoAttributeException::class);
        
        $evaluator = new \uMod\Evaluator\Evaluator();
        
        $evaluator->evaluate($source);
    }
    
    function testInvalidVersion() {
        $source = $this->getMockSource('InvalidVersion');
        
        $this->expectException(\uMod\Evaluator\Exceptions\NoInfoVersionException::class);
        
        $evaluator = new \uMod\Evaluator\Evaluator();
        
        $evaluator->evaluate($source);
    }
    
    function testInvalidVersionNotEnoughParts() {
        $source = $this->getMockSource('InvalidVersion2');
        
        $this->expectException(\uMod\Evaluator\Exceptions\InvalidInfoVersionException::class);
        
        $evaluator = new \uMod\Evaluator\Evaluator();
        
        $evaluator->evaluate($source);
    }
    
    function testInvalidVersionTooManyParts() {
        $source = $this->getMockSource('InvalidVersion3');
        
        $this->expectException(\uMod\Evaluator\Exceptions\InvalidInfoVersionException::class);
        
        $evaluator = new \uMod\Evaluator\Evaluator();
        
        $evaluator->evaluate($source);
    }
    
    function testInvalidVersionNonNumericParts() {
        $source = $this->getMockSource('InvalidVersion4');
        
        $this->expectException(\uMod\Evaluator\Exceptions\InvalidInfoVersionException::class);
        
        $evaluator = new \uMod\Evaluator\Evaluator();
        
        $evaluator->evaluate($source);
    }
}
