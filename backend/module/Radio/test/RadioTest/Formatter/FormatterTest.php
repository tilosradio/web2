<?php
namespace RadioTest\Controller;

use RadioTest\Bootstrap;
use PHPUnit_Framework_TestCase;
use Radio\Formatter\Formatter;

class FormatterTest extends \PHPUnit_Framework_TestCase {

    protected $formatter;

    protected function setUp() {
        $this->formatter = new Formatter();
    }

    public function testLegacy() {
        // given
        $content = "<p>qwe</p>";

        // when
        $formatted = $this->formatter->format('legacy', $content);
        $this->assertEquals($content, $formatted);
    }

    public function testNormal() {
        // given
        $content = "qwe\n\nesdf";
        // when
        $formatted = $this->formatter->format('normal', $content);
        $this->assertEquals("<p>qwe\n\nesdf</p>", $formatted);
    }
}

?>