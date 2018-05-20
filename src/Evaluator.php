<?php
namespace uModEvaluator;

class Evaluator {
    /**
     * 
     * @param string $source
     * @return \uMod\Evaluator\PluginInfo
     * @throws uMod\Evaluator\InvalidSourceException
     * @throws uMod\Evaluator\NoInfoAuthorException
     * @throws uMod\Evaluator\NoInfoVersionException
     * @throws uMod\Evaluator\NoInfoAttributeException
     */
    public function evaluate($source) {
        $pluginInfo = new PluginInfo();
        
        $this->extractClassName($pluginInfo, $source);
        $this->extractInfo($pluginInfo, $source);
        
        return $pluginInfo;
    }
    
    /**
     * Extracts class name from plugin source
     * @param \uMod\Evaluator\PluginInfo $info
     * @param string $source
     * @throws uMod\Evaluator\InvalidSourceException
     */
    private function extractClassName(PluginInfo $info, $source) {
        $searchString = 'class ';
        $pos = strpos($source, $searchString);
        
        if($pos == false) {
            throw new uMod\Evaluator\InvalidSourceException("Class name not found");
        }
        
        $start = $pos += strlen($searchString);
        
        $next = strpos($source, ':', $start);
        if($next == false) {
            throw new uMod\Evaluator\InvalidSourceException("Class is not a plugin");
        }
        
        $classNameRaw = substr($source, $start, $next - $start);
        $info->className = trim($classNameRaw);
    }
    
    /**
     * Extracts Info attribute from plugin source
     * @param \uMod\Evaluator\PluginInfo $info
     * @param string $source
     * @throws uMod\Evaluator\NoInfoAuthorException
     * @throws uMod\Evaluator\NoInfoVersionException
     * @throws uMod\Evaluator\NoInfoAttributeException
     */
    private function extractInfo(PluginInfo $info, $source) {
        $searchString = '[Info(';
        $pos = strpos($source, $searchString);
        if($pos == false) {
            $searchString = '[Info (';
            $pos = strpos($source, $searchString);
        }
        if($pos !== false) {
            $starts = $pos + strlen($searchString);
            $ends = strpos($source, ')]', $starts);

            $infoString = substr($source, $starts, $ends - $starts);
            $infoData = explode(',', $infoString);
            foreach($infoData as $i => $infoItem) {
                $infoData[$i] = trim(str_replace('"','', $infoItem));
            }

            if(isset($infoData[1])) {
                $info->author = $infoData[1];
            } else {
                throw new uMod\Evaluator\NoInfoAuthorException('Info attribute invalid, no author specified');
            }

            if(isset($infoData[2])) {
                $info->version = $infoData[2];
            } else {
                throw new uMod\Evaluator\NoInfoVersionException('Info attribute invalid, no version specified');
            }
        } else {
            throw new uMod\Evaluator\NoInfoAttributeException('Info attribute invalid or not found');
        }
    }
}
