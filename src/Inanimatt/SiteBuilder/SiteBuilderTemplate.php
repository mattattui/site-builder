<?php
namespace Inanimatt\SiteBuilder;

/* Credit (but no blame) to Chad Emrys Minick for this Template pattern */
class SiteBuilderTemplate {
	private $vars = array(
	);

	public function __get($name) {
		return $this->vars[$name];
	}
 
	public function __set($name, $value) {
		$this->vars[$name] = $value;
	}

	public function __getVars() {
		return $this->vars;
	}

	public function __setVars($vars) {
		$this->vars = $vars;
	}
 
	public function render($__file) {
		extract($this->vars, EXTR_SKIP);
		ob_start();
		include($__file);
		return ob_get_clean();
	}
}


