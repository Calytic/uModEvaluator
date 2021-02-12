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

    function testGetVersion2() {
        $source = $this->getMockSource('TestPlugin2');

        $evaluator = new \uMod\Evaluator\Evaluator();

        $pluginInfo = $evaluator->evaluate($source);

        $this->assertEquals("0.1.1", $pluginInfo->version);
    }

    function testGetVersion3() {
        $source = $this->getMockSource('TestPlugin3');

        $evaluator = new \uMod\Evaluator\Evaluator();

        $pluginInfo = $evaluator->evaluate($source);

        $this->assertEquals("0.1.2", $pluginInfo->version);
    }

    function testGetVersion4() {
        $source = $this->getMockSource('TestPlugin4');

        $evaluator = new \uMod\Evaluator\Evaluator();

        $pluginInfo = $evaluator->evaluate($source);

        $this->assertEquals("0.1.2", $pluginInfo->version);
    }

    function testGetVersion5() {
        $source = $this->getMockSource('TestPlugin5');

        $evaluator = new \uMod\Evaluator\Evaluator();

        $pluginInfo = $evaluator->evaluate($source);

        $this->assertEquals("0.1.1", $pluginInfo->version);
    }

    function testGetVersion6() {
        $source = $this->getMockSource('TestPlugin6');

        $evaluator = new \uMod\Evaluator\Evaluator();

        $pluginInfo = $evaluator->evaluate($source);

        $this->assertEquals("0.1.1", $pluginInfo->version);
    }

    function testGetVersion7() {
        $source = $this->getMockSource('TestPlugin11');

        $evaluator = new \uMod\Evaluator\Evaluator();

        $pluginInfo = $evaluator->evaluate($source);

        $this->assertEquals("0.0.50", $pluginInfo->version);
    }

    function testShouldRemoveLeadingZeroesWhenVersionHasLeadingZeroes() {
        $source = $this->getMockSource('TestPlugin10');

        $evaluator = new \uMod\Evaluator\Evaluator();

        $pluginInfo = $evaluator->evaluate($source);

        $this->assertEquals("0.1.7", $pluginInfo->version);
    }

    function testGetAll7() {
        $source = $this->getMockSource('TestPlugin7');

        $evaluator = new \uMod\Evaluator\Evaluator();

        $pluginInfo = $evaluator->evaluate($source);

        $this->assertEquals("1.2.0", $pluginInfo->version);
        $this->assertEquals("Calytic", $pluginInfo->author);
        $this->assertEquals("TestPlugin7", $pluginInfo->className);
        $this->assertEquals("TestPlugin7", $pluginInfo->title);
        $this->assertEquals("description", $pluginInfo->description);
    }

    function testGetAll8() {
        $source = $this->getMockSource('TestPlugin8');

        $evaluator = new \uMod\Evaluator\Evaluator();

        $pluginInfo = $evaluator->evaluate($source);

        $this->assertEquals("1.2.1", $pluginInfo->version);
        $this->assertEquals("Calytic", $pluginInfo->author);
        $this->assertEquals("TestPlugin8", $pluginInfo->className);
        $this->assertEquals("TestPlugin8", $pluginInfo->title);
        $this->assertEquals("hello' world", $pluginInfo->description);
    }

    function testGetAll9() {
        $source = $this->getMockSource('TestPlugin9');

        $evaluator = new \uMod\Evaluator\Evaluator();

        $pluginInfo = $evaluator->evaluate($source);

        $this->assertEquals("0.1.7", $pluginInfo->version);
        $this->assertEquals("Calytic", $pluginInfo->author);
        $this->assertEquals("Bank", $pluginInfo->className);
        $this->assertEquals("Bank's For All", $pluginInfo->title);
        $this->assertEquals("Safe player storage", $pluginInfo->description);
    }

    function testGetDescription6() {
        $source = $this->getMockSource('TestPlugin6');

        $evaluator = new \uMod\Evaluator\Evaluator();

        $pluginInfo = $evaluator->evaluate($source);

        $this->assertEquals("Some description]1", $pluginInfo->description);
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

        $this->assertEquals("TestPlugin1", $pluginInfo->className);
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

        $this->assertEquals("TestPlugin1", $pluginInfo->title);
    }

    function testInvalidSource() {
        $source = $this->getMockSource('InvalidSource');

        $this->expectException(\uMod\Evaluator\Exceptions\InvalidSourceException::class);

        $evaluator = new \uMod\Evaluator\Evaluator();

        $evaluator->evaluate($source);
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
