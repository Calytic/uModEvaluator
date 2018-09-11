<?php
namespace uMod\Evaluator;

class Evaluator {
    /**
     * 
     * @param string $source
     * @return PluginInfo
     * @throws Exceptions\InvalidSourceException
     * @throws Exceptions\NoInfoAuthorException
     * @throws Exceptions\NoInfoVersionException
     * @throws Exceptions\NoInfoAttributeException
     */
    public function evaluate($source) {
        $pluginInfo = new PluginInfo();
        
        $this->extractNamespace($pluginInfo, $source);
        $this->extractClassName($pluginInfo, $source);
        $this->extractInfo($pluginInfo, $source);
        
        return $pluginInfo;
    }
    
    /**
     * Extracts class name from plugin source
     * @param PluginInfo $info
     * @param string $source
     * @throws Exceptions\InvalidSourceException
     */
    private function extractClassName(PluginInfo $info, $source) {
        $searchString = 'class ';
        $pos = strpos($source, $searchString);
        
        if($pos === false) {
            throw new Exceptions\InvalidSourceException("Class name not found");
        }
        
        $start = $pos += strlen($searchString);
        
        $next = strpos($source, ':', $start);
        if($next === false) {
            throw new Exceptions\InvalidSourceException("Class is not a plugin");
        }
        
        $classname = substr($source, $start, $next - $start);
        $info->className = trim($classname);
    }
    
    /**
     * Extracts class name from plugin source
     * @param PluginInfo $info
     * @param string $source
     * @throws Exceptions\InvalidSourceException
     */
    private function extractNamespace(PluginInfo $info, $source) {
        $searchString = 'namespace ';
        $pos = strpos($source, $searchString);
        
        if($pos === false) {
            throw new Exceptions\InvalidSourceException("Namespace not found");
        }
        
        $start = $pos += strlen($searchString);
        
        $next = strpos($source, '{', $start);
        if($next === false) {
            throw new Exceptions\InvalidSourceException("Namespace definition invalid");
        }
        
        $namespace = substr($source, $start, $next - $start);
        $info->namespace = trim($namespace);
    }
    
    /**
     * Extracts Info attribute from plugin source
     * @param PluginInfo $info
     * @param string $source
     * @throws Exceptions\NoInfoAuthorException
     * @throws Exceptions\NoInfoVersionException
     * @throws Exceptions\NoInfoAttributeException
     */
    private function extractInfo(PluginInfo $info, $source) {
        $startSearch = [
            '[Info(',
            '[Info ('
        ];
        
        foreach($startSearch as $searchString) {
            $pos = strpos($source, $searchString);
            if($pos !== false) {
                break;
            }
        }
        
        $endSearch = [
            ')]',
            ') ]',
            ')'
        ];
        
        if($pos !== false) {
            $starts = $pos + strlen($searchString);
            
            foreach($endSearch as $endSearchString) {
                $ends = strpos($source, $endSearchString, $starts);
                if($ends !== false) {
                    break;
                }
            }

            $infoString = substr($source, $starts, $ends - $starts);
            $infoData = explode(',', $infoString);
            foreach($infoData as $i => $infoItem) {
                $infoData[$i] = trim(str_replace('"','', $infoItem));
            }

            if(isset($infoData[0])) {
                $info->title = trim($infoData[0]);
            } else {
                throw new Exceptions\NoInfoTitleException('Info attribute invalid, no title specified');
            }
            
            if(isset($infoData[1])) {
                $info->author = trim($infoData[1]);
            } else {
                throw new Exceptions\NoInfoAuthorException('Info attribute invalid, no author specified');
            }

            if(isset($infoData[2])) {
                $info->version = trim($infoData[2]);
                $this->evaluateVersion($info->version);
            } else {
                throw new Exceptions\NoInfoVersionException('Info attribute invalid, no version specified');
            }
        } else {
            throw new Exceptions\NoInfoAttributeException('Info attribute invalid or not found');
        }
    }
    
    private function evaluateVersion($version) {
        if(strpos($version,'.') === false) {
            throw new Exceptions\InvalidInfoVersionException('Info version invalid, must specify at least two version parts #.#');
        }
        
        $parts = explode('.', $version);
        
        if(count($parts) >= 6) {
            throw new Exceptions\InvalidInfoVersionException('Info version invalid, too many version parts ('.count($parts).'/5)');
        }
        
        foreach($parts as $part) {
            if(!is_numeric($part)) {
                throw new Exceptions\InvalidInfoVersionException('Info version invalid, version part is not numeric ('.$part.')');
            }
        }
    }
}
