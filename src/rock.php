<?php

namespace MongodbTools;

/**
 * Application class
 *
 */
class Rock {
	private static $_controller;

	/**
	 * Start application
	 *
	 */
	public static function start() {
		$path = x("action");
		if (!$path) {
			$path = "index.index";
		}
		if (!strstr($path, ".")) {
			$path .= ".index";
		}
		if (!preg_match("/(^.*(?:^|\\.))(\\w+)\\.(\\w+)$/", $path, $match)) {
			trigger_error("你称其为无效行为");
		}
		$name = $match[1] . $match[2];
		define("__CONTROLLER__", $name);
		$controller = $match[2];
		$action = $match[3];
		$mainRoot = null;
		$isInPlugin = false;
		if (substr($name, 0, 1) == "@") {
			$isInPlugin = true;
			$mainRoot = __ROOT__ . DS . "plugins" . DS . substr($name, 1, strpos($name, ".") - 1);
			if (!is_dir($mainRoot)) {
				$mainRoot = dirname(dirname(__ROOT__)) . DS . "plugins" . DS . substr($name, 1, strpos($name, ".") - 1);
			}
			$name = substr($name, strpos($name, ".") + 1);
		}
		else {
			$isInPlugin = false;
			$mainRoot = __ROOT__;
		}
		$dir = str_replace(".", DS, $name);
		$file = $mainRoot . DS . "controllers" . DS . $dir . ".php";
		if (!is_file($file)) {
			trigger_error("file '{$file}' of controller '{$controller}' is not found", E_USER_ERROR);
		}
		require($file);
		$class = ucfirst(rock_name_to_java($controller)) . "Controller";
		if (!class_exists($class, false)) {
			$file = realpath($file);
			trigger_error("class '{$class}' is not found in controller file '{$file}'", E_USER_ERROR);
		}
		$obj = new $class;
		if (!($obj instanceof RController)) {
			trigger_error("controller class '{$class}' must be a subclass of RController", E_USER_ERROR);
		}

		define("__ACTION__", $action);
		$obj->setRoot($mainRoot);
		$obj->setAction($action);
		$obj->setPath($file);
		$obj->setName($name);
		$obj->setInPlugin($isInPlugin);
		$obj->exec();
	}

	public static function setController($controller) {
		self::$_controller = $controller;
	}

	/**
	 * get current running controller object
	 *
	 * @return RController
	 */
	public static function controller() {
		return self::$_controller;
	}
}

/**
 * Controller parent class
 *
 */
class RController {
	private $_action;
	private $_path;
	private $_name;
	private $_inPlugin = false;

	/**
	 * set current action name
	 *
	 * @param string $action action name
	 */
	public function setAction($action) {
		$this->_action = $action;
	}

	/**
	 * Get action name
	 *
	 * @return string
	 */
	public function action() {
		return $this->_action;
	}

	public function setRoot($root) {
		$this->_root = $root;
	}

	public function root() {
		return $this->_root;
	}

	/**
	 * Set controller file path
	 *
	 * @param string $path file path
	 */
	public function setPath($path) {
		$this->_path = $path;
	}

	/**
	 * Set controller name
	 *
	 * @param string $name controller name
	 */
	public function setName($name) {
		$this->_name = $name;
	}

	/**
	 * Get controller name
	 *
	 * @return string
	 */
	public function name() {
		return $this->_name;
	}

	/**
	 * Set if the controller is in a plugin
	 *
	 * @param boolean $isInPlugin true or false
	 */
	public function setInPlugin($isInPlugin) {
		$this->_inPlugin = $isInPlugin;
	}

	/**
	 * Call before actions
	 *
	 */
	public function onBefore() {

	}

	/**
	 * Call after actions
	 *
	 */
	public function onAfter() {

	}

	/**
	 * Execute action
	 *
	 */
	public function exec() {
		Rock::setController($this);

		if (class_exists("RPlugin")) {
			\RPlugin::callBefore();
		}
		$this->onBefore();

		$method = "do" . $this->_action;
		if (!method_exists($this, $method)) {
			trigger_error("can not find action '{$this->_action}' in class '" . get_class($this) . "'", E_USER_ERROR);
		}
		$ret = $this->$method();
		if (is_object($ret) && ($ret instanceof RView)) {
			$ret->display();
		}

		$this->onAfter();
		if (class_exists("RPlugin")) {
			\RPlugin::callAfter();
		}
	}

	/**
	 * Display View
	 *
	 * @param string $action action name, if not NULL, find view with this name
	 */
	public function display($action = null) {
		if (is_null($action)) {
			$action = $this->_action;
		}
		$view = null;
		if ($this->_inPlugin) {
			$view = dirname(dirname($this->_path))  . "/views/" . str_replace(".", "/", $this->_name) . "/{$action}.php";
		}
		else {
			$view = dirname(__ROOT__) . DS . rock_theme_path()  . "/views/" . str_replace(".", "/", $this->_name) . "/{$action}.php";
		}
		if (is_file($view)) {
			extract(get_object_vars($this), EXTR_OVERWRITE);
			require($view);
		}
	}
}

/**
 * Model class
 *
 */
class RModel {

}

/**
 * View class
 *
 */
class RView {
	/**
	 * Display view
	 *
	 */
	public function display() {

	}
}

