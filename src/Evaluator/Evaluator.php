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

        $namespaceDefinitionStart = strpos($source, 'namespace');
        if($namespaceDefinitionStart === false) {
            throw new Exceptions\InvalidSourceException("No namespace definition found");
        }

        $source = substr($source, $namespaceDefinitionStart);

        $this->extractNamespace($pluginInfo, $source);
        $end = $this->extractClassName($pluginInfo, $source);
        $this->extractClassAttributes($pluginInfo, $source, $end);

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
        return $start;
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

    private function extractClassAttributes(PluginInfo $info, $source, $end) {
        $source = substr($source, 0, $end);

        $attrRegex = '/\[([^\]]*?(?:(?:(")[^"]*?\2)[^\]]*?)*)\]/';

        preg_match_all($attrRegex, $source, $attrLines, PREG_SET_ORDER, 0);

        if(count($attrLines) == 0) {
            throw new Exceptions\NoInfoAttributeException('Info attribute not found');
        }

        $paramsRegex = '/(\w+)\s*(\([^\)]*?(?:(?:(\'|")[^"]*?\3)[^\)]*?)*\))/m';

        $infoFound = false;

        foreach($attrLines as $attrLine) {
            if(is_array($attrLine)) {
                $attrLine = $attrLine[0];
            }

            preg_match_all($paramsRegex, $attrLine, $attributes, PREG_SET_ORDER, 0);
            foreach($attributes as $parts) {
                if(count($parts) < 3) {
                    throw new Exceptions\InvalidSourceException("Attributes invalid: ".count($parts)." - ".print_r($parts, true));
                }

                $type = $parts[1];
                $params = $parts[2];

                switch(strtolower(trim($type))) {
                    case 'info':
                        $infoFound = true;
                        $items = $this->extractInfoParams($info, $params);

                        if(count($items) == 0) {
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
                        $info->version = $this->evaluateVersion($info->version);
                        break;
                    case 'description':
                        $info->description = $this->extractDescription($info, $params);
                        break;
                }
            }
        }

        if(!$infoFound) {
            throw new Exceptions\NoInfoAttributeException('Info attribute invalid or not found');
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
                    $inQuote = true;
                }
            } elseif($char == ',' && !$inQuote) {
                $items[] = trim($buffer);
                $buffer = '';
            } elseif(($char == '(' || $char == ')') && !$inQuote) {

            } else {
                $buffer.=$char;
            }
        }

        if(!empty($buffer)) {
            $items[] = trim($buffer);
        }

        return $items;
    }

    private function extractDescription(PluginInfo $info, $params) {
        $start = strpos($params,'"') + 1;
        $end = strpos($params,'"', $start);

        return substr($params, $start, $end - $start);
    }

    private function evaluateVersion($version) {
        if(strpos($version,'.') === false) {
            throw new Exceptions\InvalidInfoVersionException('Info version invalid ('.$version.'), must specify at least two version parts #.#');
        }

        $parts = explode('.', $version);

        if(count($parts) >= 6) {
            throw new Exceptions\InvalidInfoVersionException('Info version invalid ('.$version.'), too many version parts ('.count($parts).'/5)');
        }

        $i = 0;
        foreach($parts as $part) {
            if(!is_numeric($part)) {
                throw new Exceptions\InvalidInfoVersionException('Info version invalid ('.$part.'), version part is not numeric ('.$part.')');
            }

            $parts[$i] = intval($part);
            $i++;
        }

        return implode('.', $parts);
    }
}
