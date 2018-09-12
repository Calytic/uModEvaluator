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
        $this->extractClassAttributes($pluginInfo, $source);
        
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
    
    private function extractClassAttributes(PluginInfo $info, $source) {
        $attrRegex = '/\[([^]]+)\]/m';
        
        preg_match_all($attrRegex, $source, $attrLines, PREG_SET_ORDER, 0);
        
        if(count($attrLines) == 0) {
            throw new Exceptions\NoInfoAttributeException('Info attribute invalid or not found');
        }
        
        $paramsRegex = '/(\w+)(\([^\)]*?(?:(?:(\'|")[^\'"]*?\3)[^\)]*?)*\))/m';
        
        foreach($attrLines as $attrLine) {
            if(is_array($attrLine)) {
                $attrLine = array_shift($attrLine);
            }
            preg_match_all($paramsRegex, $attrLine, $parts, PREG_SET_ORDER, 0);
            if(count($parts) !== 4) {
                throw new Exceptions\InvalidSourceException("Attributes invalid");
            }
            
            $type = $parts[1];
            $params = $parts[2];
            $infoFound = false;
            
            switch(strtolower(trim($type))) {
                case 'info':
                    $infoFound = true;
                    $items = $this->extractInfoParams($info, $params);
                    
                    if(empty($items)) {
                        throw new Exceptions\NoInfoTitleException('Info attribute invalid, no title specified');
                    }
                    
                    if(count($items) == 1) {
                        throw new Exceptions\NoInfoAuthorException('Info attribute invalid, no author specified');
                    }
                    
                    if(count($items) == 2) {
                        throw new Exceptions\NoInfoVersionException('Info attribute invalid, no version specified');
                    }
                    
                    $info->title = $items[0];
                    $info->author = $items[1];
                    $info->version = $items[2];
                    break;
                case 'description':
                    $info->description = $this->extractDescription($info, $params);
                    break;
            }
            
            if(!$infoFound) {
                throw new Exceptions\NoInfoAttributeException('Info attribute invalid or not found');
            }
        }
    }
    
    private function extractInfoParams(PluginInfo $info, $params) {
        $inQuote = false;
        
        $arr = str_split($params);
        
        $items = [];
        
        $buffer = '';
        foreach($arr as $char) {
            if($char == '"') {
                if($inQuote) {
                    $inQuote = false;
                } else {
                    $inQuote = false;
                }
            } elseif($char == ',' && !$inQuote) {
                $items[] = $buffer;
            } else {
                $buffer.=$char;
            }
        }
        
        return $items;
    }
    
    private function extractDescription(PluginInfo $info, $params) {
        $start = strpos($params,'"');
        
        $end = strpos($params,'"', $start + 1);
        
        return substr($params, $start, $start - $end);
    }
    
    /**
     * Extracts Info attribute from plugin source
     * @param PluginInfo $info
     * @param string $source
     * @throws Exceptions\NoInfoAuthorException
     * @throws Exceptions\NoInfoVersionException
     * @throws Exceptions\NoInfoAttributeException
     */
//    private function extractInfo(PluginInfo $info, $source) {
//        $startSearch = [
//            '[Info(',
//            '[Info ('
//        ];
//        
//        foreach($startSearch as $searchString) {
//            $pos = strpos($source, $searchString);
//            if($pos !== false) {
//                break;
//            }
//        }
//        
//        $endSearch = [
//            ')]',
//            ') ]',
//            ')'
//        ];
//        
//        if($pos !== false) {
//            $starts = $pos + strlen($searchString);
//            
//            foreach($endSearch as $endSearchString) {
//                $ends = strpos($source, $endSearchString, $starts);
//                if($ends !== false) {
//                    break;
//                }
//            }
//
//            $infoString = substr($source, $starts, $ends - $starts);
//            $infoData = explode(',', $infoString);
//            foreach($infoData as $i => $infoItem) {
//                $infoData[$i] = trim(str_replace('"','', $infoItem));
//            }
//
//            if(isset($infoData[0])) {
//                $info->title = trim($infoData[0]);
//            } else {
//                throw new Exceptions\NoInfoTitleException('Info attribute invalid, no title specified');
//            }
//            
//            if(isset($infoData[1])) {
//                $info->author = trim($infoData[1]);
//            } else {
//                throw new Exceptions\NoInfoAuthorException('Info attribute invalid, no author specified');
//            }
//
//            if(isset($infoData[2])) {
//                $info->version = trim($infoData[2]);
//                $this->evaluateVersion($info->version);
//            } else {
//                throw new Exceptions\NoInfoVersionException('Info attribute invalid, no version specified');
//            }
//        } else {
//            throw new Exceptions\NoInfoAttributeException('Info attribute invalid or not found');
//        }
//    }
    
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
