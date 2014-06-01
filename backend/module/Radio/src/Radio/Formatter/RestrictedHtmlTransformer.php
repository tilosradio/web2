<?php
/**
 * Created by IntelliJ IDEA.
 * User: elek
 * Date: 12/30/13
 * Time: 11:07 PM
 */

namespace Radio\Formatter;


use Zend\XmlRpc\Generator\DomDocument;

class RestrictedHtmlTransformer
{

    protected $allowedTags = [];

    protected $customHandlers = [];

    function __construct()
    {
        libxml_use_internal_errors(true);
        $this->allowedTags['p'] = [];
        $this->allowedTags['html'] = [];
        $this->allowedTags['body'] = [];
        $this->allowedTags['b'] = [];
        $this->allowedTags['br'] = [];
        $this->allowedTags['ul'] = [];
        $this->allowedTags['nl'] = [];
        $this->allowedTags['i'] = [];
        $this->allowedTags['em'] = [];

        $this->allowedTags['li'] = [];

        $this->allowedTags['h1'] = [];
        $this->allowedTags['h2'] = [];
        $this->allowedTags['h3'] = [];
        $this->allowedTags['strong'] = [];
        $this->allowedTags['img'] = ['src'];
        $this->allowedTags['a'] = ['href','target'];

        $this->customHandlers['iframe'] = array($this, 'iframe');
    }

    public function format($content)
    {
        $html = new \DomDocument('1.0', 'UTF-8');
        $html->substituteEntities = false;
        $html->preserveWhiteSpace = true;
        $html->formatOutput = false;
        $html->loadHTML('<?xml encoding="UTF-8">' . $content);
        $this->parseElement($html);

        return trim(preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(array('<?xml encoding="UTF-8">', '<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $html->saveHTML())));

    }

    protected function parseElement($node)
    {


        $dom = $node;
        //visit child nodes
        if ($dom->hasChildNodes()) {
            $toVisit = [];
            foreach ($dom->childNodes as $n) {
                $toVisit[] = $n;
            }
            foreach ($toVisit as $n) {
                $this->parseElement($n);
            }
        }


        //check the current node

        $allowed = false;

        if (array_key_exists($node->nodeName, $this->customHandlers)) {
            $allowed = call_user_func($this->customHandlers[$dom->nodeName], $dom);
        }
        if ($allowed === false && $node->nodeType == XML_ELEMENT_NODE && array_key_exists($dom->nodeName, $this->allowedTags)) {
            $allowed = $this->allowedTags[$node->nodeName];
        }


        //check attributes
        if ($node->nodeType == XML_ELEMENT_NODE && $allowed !== false) {
            if ($node->hasAttributes()) {
                $toRemove = [];
                foreach ($node->attributes as $attr) {
                    if (!in_array($attr->name, $allowed)) {
                        $toRemove[] = $attr->name;
                    }

                }
                foreach ($toRemove as $attrName) {
                    $node->removeAttribute($attrName);
                }
            }

        }


        if (($dom->nodeType == XML_ELEMENT_NODE) && $allowed === false) {
            $parent = $dom->parentNode;
            if ($parent) {
                $toAdd = [];

                //the child nodes will be retained
                if ($dom->hasChildNodes()) {
                    foreach ($dom->childNodes as $n) {
                        $toAdd[] = $n;
                    }
                    foreach ($toAdd as $n) {
                        $parent->insertBefore($n, $dom);
                    }
                    $parent->removeChild($dom);
                } else {
                    $parent->removeChild($dom);
                }


            }
        }

    }

    public function iframe($dom)
    {
        $src = $dom->getAttribute('src');
        if ($src && preg_match("/^(http(s)?:)?\/\/(www.)?youtube.com\/.*/", $src)) {
            return array("src", "width", "height","frameborder","allowfullscreen");
        } else {
            return false;
        }
    }


} 