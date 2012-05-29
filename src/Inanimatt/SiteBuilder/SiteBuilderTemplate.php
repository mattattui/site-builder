<?php
namespace Inanimatt\SiteBuilder;

use Inanimatt\SiteBuilder\Exception\ArgumentException;
use Inanimatt\SiteBuilder\Exception\RenderException;

/**
 * Basic PHP-based template class.
 *
 * Credit (but no blame) to Chad Emrys Minick for this Template pattern.
 * Major modifications:
 *  - namespaced :P
 *  - bulk setters (for setup) and getters (for testing)
 *  - extract() has the extra argument to prevent overwriting of
 *    non-template variables.
 */
class SiteBuilderTemplate
{
    private $vars = array(
    );

    /**
     * Fetch template var.
     *
     * @param  string $name Variable name to fetch
     * @return mixed  Requested variable or null
     */
    public function __get($name)
    {
        if (!is_string($name)) {
            throw new ArgumentException($name . ' is not a valid variable name');
        }

        if (!isset($this->vars[$name])) {
            return null;
        }

        return $this->vars[$name];
    }

    /**
     * Set template var.
     *
     * @param  string $name  Variable name to fetch
     * @param  mixed  $value Variable value
     * @return null
     */
    public function __set($name, $value)
    {
        if (!is_string($name)) {
            throw new ArgumentException($name . ' is not a valid variable name');
        }

        $this->vars[$name] = $value;
    }

    /**
     * Retrieve all vars
     *
     * @return array Template variables
     */
    public function __getVars()
    {
        return $this->vars;
    }

    /**
     * Bulk set template variables
     *
     * @param  array $vars Template variables (array or array-like)
     * @return null
     */
    public function __setVars($vars)
    {
        if (! (is_array($vars) || ($vars instanceof \ArrayAccess)) ) {
            throw new ArgumentException('Argument 1 must be an array or array-like.');
        }

        $this->vars = $vars;
    }

    /**
     * Render template.
     *
     * Extracts all the template variables into the local scope (without
     * overwriting existing template vars, like $this, which would be weird)
     * and then includes the given file, capturing all output in a buffer,
     * which is returned.
     *
     * @return string Rendered template
     */
    public function render($__file)
    {
        if (!is_file($__file)) {
            throw new RenderException('Template file does not exist.');
        }

        extract($this->vars, EXTR_SKIP);
        ob_start();
        include($__file);

        return ob_get_clean();
    }
}

