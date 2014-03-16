<?php
/**
 * Created by IntelliJ IDEA.
 * User: elek
 * Date: 12/12/13
 * Time: 8:52 PM
 */

namespace Radio\Mapper;


use Radio\Entity\Contribution;

class MapperFactory {

    private static function text(&$m, $name, $context) {
        $t = $m->addMapper(new ChildObject($name));
        $t->addMapper(new Field("id"));
        $t->addMapper(new Field("title"));
        $t->addMapper(new Field("format"));
        $t->addMapper(new TextContent());
        return $t;

    }

    private static function shortAuthor(&$m, $context) {
        $m->addMapper(new Field("id"));
        $m->addMapper(new Field("name"));
        $m->addMapper(new Field("alias"));
        $m->addMapper(new ResourceField("photo", $context['baseUrl']));
        $m->addMapper(new ResourceField("avatar", $context['baseUrl']));
    }

    private static function shortShow(&$m, $context) {
        $m->addMapper(new Field("id"));
        $m->addMapper(new Field("name"));
        $m->addMapper(new Field("definition"));
        $m->addMapper(new Field("type"));
        $m->addMapper(new Field("status"));
        $m->addMapper(new Field("alias"));
        $m->addMapper(new ResourceField("banner", $context['baseUrl']));


    }

    public static function authorMapper($context) {
        $m = MapperFactory::authorElementMapper($context);
        $m->addMapper(new FormattedTextField("introduction","html"));
        $um = $m->addMapper(new ChildCollection("urls"));
        $um->addMapper(new Field('url'));
        return $m;
    }

    public static function authorElementMapper($context) {
        $m = new ObjectMapper();
        MapperFactory::shortAuthor($m, $context);

        $cm = $m->addMapper(new ChildCollection("contributions"));
        $cm->addMapper(new Field("nick"));
        $sm = $cm->addMapper(new ChildObject("show"));
        MapperFactory::shortShow($sm, $context);
        return $m;
    }

    public static function showMapper($context) {
        $m = new ObjectMapper();

        MapperFactory::shortShow($m, $context);
        $cm = $m->addMapper(new ChildCollection("contributors"));
        $cm->addMapper(new Field("nick"));
        $cm->addMapper(new Field("id"));

        $am = $cm->addMapper(new ChildObject("author"));
        MapperFactory::shortAuthor($am, $context);


        $em = $m->addMapper(new ChildCollection("episodes"));
        $em->addMapper(new EpisodeURLField());
        $em->addMapper(new Field("id"));
        $em->addMapper(new DateField("plannedFrom"));
        $em->addMapper(new DateField("plannedTo"));
        $em->addMapper(new InternalLinkField("m3uUrl", $context['baseUrl']));
        $em->addMapper(new Field("url", $context['baseUrl']));
        $emt = $em->addMapper(new ChildObject("text"));
        $emt->addMapper(new Field("title"));
        $emt->addMapper(new Field("format"));
        $emt->addMapper(new TextContent());



        $m->addMapper(new Field("description"));
        $m->addMapper(new FormattedTextField("description", "legacy"));


        $um = $m->addMapper(new ChildCollection("urls"));
        $um->addMapper(new Field('url'));

        $schm = $m->addMapper(new ChildCollection("schedulings"));
        $schm->addMapper(new Field("hourFrom"));
        $schm->addMapper(new Field("minFrom"));
        $schm->addMapper(new DateField("base"));
        $schm->addMapper(new DateField("validFrom"));
        $schm->addMapper(new DateField("validTo"));
        $schm->addMapper(new Field("weekType"));



        $schm = $m->addMapper(new SchedulingCollection("schedulings"));

        return $m;
    }

    public static function showElementMapper($context) {
        $m = new ObjectMapper();

        MapperFactory::shortShow($m, $context);
        $cm = $m->addMapper(new ChildCollection("contributors"));
        $cm->addMapper(new Field("nick"));
        $am = $cm->addMapper(new ChildObject("author"));
        MapperFactory::shortAuthor($am, $context);

        return $m;
    }

    public static function episodeMapper($context) {
        return MapperFactory::episodeElementMapper($context);
    }

    public static function shortEpisodeElementMapper($context) {
        $m = new ListMapper();
        $m->addMapper(new Field("id"));
        $m->addMapper(new DateField("plannedFrom"));
        $m->addMapper(new DateField("plannedTo"));
        $m->addMapper(new InternalLinkField("m3uUrl", $context['baseUrl']));
        $m->addMapper(new InternalLinkField("url", $context['baseUrl']));
        $m->addMapper(new EpisodeURLField());
        $em = $m->addMapper(new ChildObject("text"));
        $em->addMapper(new Field("title"));
        $em->addMapper(new DateField("created"));
        $em->addMapper(new TextContent());
        return $m;
    }

    public static function episodeElementMapper($context) {
        $m = MapperFactory::shortEpisodeElementMapper($context);
        $sm = $m->addMapper(new ChildObject("show"));
        MapperFactory::shortShow($sm, $context);
        $cm = $sm->addMapper(new ChildCollection("contributors"));
        $cm->addMapper(new Field("nick"));
        $am = $cm->addMapper(new ChildObject("author"));
        MapperFactory::shortAuthor($am, $context);
        return $m;
    }


}

;