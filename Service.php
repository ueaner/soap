<?php
/**
 * PHP Soap Server
 * @author ueaner <ueaner@gmail.com> www.aboutc.net
 */

class Service {

    private $className = '';

    private $classPath = '../class/';

    private $classFileSuffix = '.class.php';

    private $serviceName = '';

    private $serviceNamePrefix = 'ABOUTC_';

    private $wsdlCacheEnabled = 0; // WSDL 缓存：1开启，0关闭

    private $xmlPath = '../xml/';

    private $mode = 'wsdl';

    /**
     * 初始化函数
     * @param string $mode
     * @param string $className
     * @param string $serviceName
     */
    public function __construct($mode = '', $className = '', $serviceName = '') {
        $this->mode = $mode;
        $this->className = $className;
        $this->serviceName = $serviceName ? $serviceName : $this->serviceNamePrefix . $className;
    }

    /**
     * 获取 WSDL xml 文件内容
     * @return string
     * @throws Exception
     */
    public function getWSDL() {
        if (empty($this->serviceName)) {
            throw new Exception('No service name.');
        }

        if (is_file($this->xmlPath . $this->className . '.wsdl')) {
            return file_get_contents($this->xmlPath . $this->className . '.wsdl');
        } else {
            /**
             * SoapDiscovery Class that provides Web Service Definition Language (WSDL).
             *
             * @package SoapDiscovery
             * @author Braulio José Solano Rojas
             * @link http://www.phpclasses.org/browse/file/9476.html
             */
            $headerWSDL = "<?xml version=\"1.0\" ?>\n";
            $headerWSDL.= "<definitions name=\"$this->serviceName\" targetNamespace=\"urn:$this->serviceName\" xmlns:wsdl=\"http://schemas.xmlsoap.org/wsdl/\" xmlns:soap=\"http://schemas.xmlsoap.org/wsdl/soap/\" xmlns:tns=\"urn:$this->serviceName\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:SOAP-ENC=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns=\"http://schemas.xmlsoap.org/wsdl/\">\n";
            $headerWSDL.= "<types xmlns=\"http://schemas.xmlsoap.org/wsdl/\" />\n";

            if (empty($this->className)) {
                throw new Exception('No class name.');
            }

            $class = new ReflectionClass($this->className);

            if (!$class->isInstantiable()) {
                throw new Exception('Class is not instantiable.');
            }

            $methods = $class->getMethods();

            $portTypeWSDL = "\n<!-- Ports -->\n".'<portType name="'.$this->serviceName.'Port">';
            $bindingWSDL = "\n<!-- SOAP Bindings -->\n".'<binding name="'.$this->serviceName.'Binding" type="tns:'.$this->serviceName."Port\">\n<soap:binding style=\"rpc\" transport=\"http://schemas.xmlsoap.org/soap/http\" />\n";
            $serviceWSDL = "\n<!-- Service (location) -->\n".'<service name="'.$this->serviceName."\">\n<documentation />\n<port name=\"".$this->serviceName.'Port" binding="tns:'.$this->serviceName."Binding\"><soap:address location=\"http://".$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI']."\" />\n</port>\n</service>\n";
            $messageWSDL = "\n<!-- Messages -->\n";
            foreach ($methods as $method) {
                if ($method->isPublic() && !$method->isConstructor()) {
                    $portTypeWSDL.= '<operation name="'.$method->getName()."\">\n".'<input message="tns:'.$method->getName()."Request\" />\n<output message=\"tns:".$method->getName()."Response\" />\n</operation>\n";
                    $bindingWSDL.= '<operation name="'.$method->getName()."\">\n".'<soap:operation soapAction="urn:'.$this->serviceName.'#'.$this->className.'#'.$method->getName()."\" />\n<input><soap:body use=\"encoded\" namespace=\"urn:$this->serviceName\" encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\" />\n</input>\n<output>\n<soap:body use=\"encoded\" namespace=\"urn:$this->serviceName\" encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\" />\n</output>\n</operation>\n";
                    $messageWSDL.= "<!-- Input parameters for method {$method->getName()} -->\n".'<message name="'.$method->getName()."Request\">\n";
                    $parameters = $method->getParameters();
                    foreach ($parameters as $parameter) {
                        if ($method->getDocComment()) {
                            $pattern = '/@param\s+(string|boolean|int|integer|float|double)/i';
                            preg_match($pattern, $method->getDocComment(), $matches);
                            $type = $matches[1];
                        }
                        else {
                            $type = 'string';
                        }
                        $messageWSDL.= '<part name="'.$parameter->getName()."\" type=\"xsd:{$type}\" />\n";
                    }
                    $messageWSDL.= "</message>\n";
                    if ($method->getDocComment()) {
                        $pattern = '/@return\s+(string|boolean|int|integer|float|double)/i';
                        preg_match($pattern, $method->getDocComment(), $matches);
                        $return = $matches[1];
                    }
                    else {
                        $return = 'string';
                    }
                    $messageWSDL.= "<!-- Output for method {$method->getName()} -->\n".'<message name="'.$method->getName()."Response\">\n";
                    $messageWSDL.= '<part name="'.$method->getName()."\" type=\"xsd:{$return}\" />\n";
                    $messageWSDL.= "</message>\n";
                }
            }
            $portTypeWSDL.= "</portType>\n";
            $bindingWSDL.= "</binding>\n";
            $wsdl = sprintf('%s%s%s%s%s%s', $headerWSDL, $messageWSDL, $portTypeWSDL, $bindingWSDL, $serviceWSDL, '</definitions>');
            file_put_contents($this->xmlPath . $this->className . '.wsdl', $wsdl);
            return $wsdl;
        }
    }

    /**
     * 获取 wsdl 引导地址（xml格式）
     * @return string
     */
    public function getDiscovery() {
        $wsdlRef = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['SCRIPT_NAME'] . '?wsdl';
        return '<?xml version="1.0" ?>'
        . '<disco:discovery xmlns:disco="http://schemas.xmlsoap.org/disco/" xmlns:scl="http://schemas.xmlsoap.org/disco/scl/">'
        . '<scl:contractRef ref="' . $wsdlRef .'" /> '
        . '</disco:discovery>';
    }

    /**
     * 运行服务
     * @throws Exception
     */
    public function run() {
        if (!is_readable($this->classPath . $this->className . $this->classFileSuffix)) {
            throw new Exception('No class name.');
        }

        require_once $this->classPath . $this->className . $this->classFileSuffix;
        // WSDL 缓存
        ini_set('soap.wsdl_cache_enabled', $this->wsdlCacheEnabled);

        // 创建 WSDL 服务
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->getServer();
        } elseif ($this->mode == 'wsdl') {
            // 查看 WSDL xml，删除以下程序就相当于 non-WSDL 模式
            header('Content-type: text/xml');
            if (isset($_SERVER['QUERY_STRING']) && strcasecmp($_SERVER['QUERY_STRING'], 'wsdl') == 0) {
                echo $this->getWSDL();
            } else {
                echo $this->getDiscovery();
            }
        } else {
            echo 'No wsdl xml file';
        }
    }

    /**
     * 获取服务
     */
    public function getServer() {
        $options['uri'] = 'http://'.$_SERVER['SERVER_NAME'];
        if ($this->mode == 'wsdl') {
            $wsdl = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['SCRIPT_NAME'] . '?wsdl';
            // WSDL 模式不用传 uri 参数，但传了也不会有问题
        } else {
            $wsdl = null;
        }
        $server = new SoapServer($wsdl, $options);
        $server->setClass($this->className);
        $server->handle();
    }

}