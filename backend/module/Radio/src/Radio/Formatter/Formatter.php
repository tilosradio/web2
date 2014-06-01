<?php
namespace Radio\Formatter;

class Formatter
{

    public $transformers;

    public function __construct()
    {
        $default = new CompositeFormatter();
        $default->addFormatter(new RestrictedHtmlTransformer());
        $default->addFormatter(new TagFormatter());
        $this->transformers = [
            'legacy' => $default,
            'normal' => $default,
            'html' => $default,
        ];
    }

    public function format($type, $content)
    {
        if (key_exists($type, $this->transformers)) {
            return $this->transformers[$type]->format($content);
        } else {
            return null;
        }
    }
}

?>