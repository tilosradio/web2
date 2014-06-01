<?php


namespace Radio\Formatter;


class CompositeFormatter
{
    public $formatters = array();


    public function format($content)
    {
        foreach ($this->formatters as $formatter) {
            $content = $formatter->format($content);
        }
        return $content;
    }

    public function addFormatter($formatter)
    {
        $this->formatters[] = $formatter;
    }

} 