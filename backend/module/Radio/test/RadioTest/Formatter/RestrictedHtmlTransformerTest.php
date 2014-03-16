<?php


namespace RadioTest\Controller;


use Radio\Formatter\RestrictedHtmlTransformer;

class RestrictedHtmlTransformerTest extends \PHPUnit_Framework_TestCase {

    public function testSimple() {
        $r = new RestrictedHtmlTransformer();
        $this->assertEquals("<p>&Aacute;rv&iacute;zt&#369;r&#337;</p>", $r->format("<p>Árvíztűrő</p>"));
        $this->assertEquals("<p>asasdasd</p>", $r->format("<p class=\"fekete\">asasdasd</p>"));
        $this->assertEquals("asd", $r->format('<tab heading="qwe">asd</tab>'));
        $this->assertEquals("<p>asasdaeesd</p>", $r->format("<p class=\"fekete\">asasda<script>ee</script>sd</p>"));
        $this->assertEquals('<a href="http://asd.hu">qwe</a>', $r->format('<a href="http://asd.hu" target="blank">qwe</a>'));


        $this->assertEquals('<p>asd</p>', $r->format('asd<iframe></iframe>'));
        $this->assertEquals('<p>asd<iframe src="http://youtube.com/a"></iframe></p>', $r->format('asd<iframe src="http://youtube.com/a"></iframe>'));


        $this->assertEquals('<p>asd<iframe width="560" height="315" src="//www.youtube.com/embed/nXMWS5dzk7c" frameborder="0" allowfullscreen=""></iframe></p>', $r->format('asd<iframe width="560" height="315" src="//www.youtube.com/embed/nXMWS5dzk7c" frameborder="0" allowfullscreen=""></iframe>'));

        $res = $r->format('<tabset><tab heading="qwe">asd</tab><tab heading="bsd">bsd</tab></tabset>');
        //$res = $r->format(file_get_contents("/home/elek/projects/tilos/module/Radio/test/RadioTest/Util/201.html"));
        //echo $res;
    }

    public function testIframe() {
        $r = new RestrictedHtmlTransformer();


        $this->assertTrue(false !== $r->iframe(new Node("//youtube.com/video")));

        $this->assertTrue(false !== $r->iframe(new Node("http://youtube.com/video")));
        $this->assertTrue(false !== $r->iframe(new Node("https://youtube.com/video")));
        $this->assertTrue(false !== $r->iframe(new Node("http://www.youtube.com/video")));
        $this->assertTrue(false === $r->iframe(new Node("http://index.hu?http://www.youtube.com/video")));
        $this->assertTrue(false === $r->iframe(new Node("http://www.youtube.com.index.hu/")));

    }


}

class Node{
    public $src;
    public function __construct($src){
        $this->src = $src;
    }
    public function getAttribute($a){
        return $this->src;
    }

}