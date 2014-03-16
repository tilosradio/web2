<?php
/**
 * Created by IntelliJ IDEA.
 * User: elek
 * Date: 12/12/13
 * Time: 8:01 PM
 */

namespace Radio\Mapper;

use Radio\Formatter\Formatter;

/**
 * Simple field mapping based on name.
 *
 * @package Radio\Mapper
 */
class TextContent implements Mapper
{

    private $name;
    private $formatName;
    private $formatter;

    function __construct()
    {
        $this->name = 'content';
        $this->formatName = "format";
        $this->formatter = new Formatter();
    }

    public function map(&$from, &$to, $setter)
    {
        if (array_key_exists($this->name, $from)) {
            $to[$this->name] = $from[$this->name];
            $to['formatted'] = $this->formatter->format($from[$this->formatName], $from[$this->name]);
        }
    }
}