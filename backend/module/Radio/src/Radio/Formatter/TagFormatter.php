<?php
/**
 * Created by IntelliJ IDEA.
 * User: elek
 * Date: 12/30/13
 * Time: 11:07 PM
 */

namespace Radio\Formatter;


use Zend\XmlRpc\Generator\DomDocument;

class TagFormatter
{


    public function format($content)
    {
        $w = "[\w&;]";

        $content = preg_replace("/(?<!&)#(" . $w . "+)/", "<a href=\"/tag/$1\"><span class=\"label label-primary\">$1</span></a>", $content);
        $content = preg_replace("/\#\{(.+?)\}/", "<a href=\"/tag/$1\"><span class=\"label label-primary\">$1</span></a>", $content);
        $content = preg_replace("/\@(" . $w . "+)/", "<a href=\"/tag/$1\"><span class=\"label label-success\">$1</span></a>", $content);
        $content = preg_replace("/\@\{(.+?)\}/", "<a href=\"/tag/$1\"><span class=\"label label-primary\">$1</span></a>", $content);

        return $content;
    }


}