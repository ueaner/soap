<?php
/**
 * Person class
 * @author ueaner <ueaner@gmail.com> www.aboutc.net
 */

class Person {
	private $name = '';

    /**
     * __construct
     * @param string $name
     */
    public function __construct($name = 'ueaner') {
		$this->name = $name;
	}

    /**
     * say something
     * @param string $name
     * @return string
     */
    public function say($name = '') {
		$name = $name ? $name : $this->name;
		return "My name is $name.";
	}

    /**
     * _SERVER
     * @return array
     */
    public function serverVar() {
		return $_SERVER;
	}
}
