<?php


class Mp3Streamer
{
    static public function getPrevHalfHour($time)
    {
        $processed = getdate($time);
        $min = $processed['minutes'];
        if ($min >= 30) {
            $min -= 30;
        }
        return $time - $min * 60;
    }


    /**
     *
     * Calculate the mp3 links based on a start time and duration.
     *
     * @param $start
     * @param $duration
     *
     */
    public function getMp3Links($start, $duration)
    {
        $res = [];
        $from = Mp3Streamer::getPrevHalfHour($start);
        $end = $from + $duration * 60;

        $curr = $from;

        for ($i = $from; $i < $end; $i += 30 * 60) {
            $d = getdate($i);
            $timestr = sprintf("%02d%02d", $d['hours'], $d['minutes']);
            $filename = sprintf("/%02d/%02d/%02d/tilosradio-%02d%02d%02d-%s.mp3", $d['year'], $d['mon'], $d['mday'], $d['year'],
                $d['mon'], $d['mday'], $timestr);
            $res[] = array("filename" => $filename, "file" => "http://archive.tilos.hu/online" . $filename, 'epoch' => $i,
                'datearray' => $d);
            if ($curr % 100 < 25) {
                $curr += 30;
            } else {
                $curr += 70;
            }
        }


        return $res;
    }


    function chunked_copy($from)
    {
        # 1 megabyte buffer
        $buffer_size = 1048576;
        $ret = 0;
        $fin = fopen($from, "rb");
        if ($fin == false) {
            echo "--$from--";
            die("file open error");
        }
        while (!feof($fin)) {
            echo fread($fin, $buffer_size);
        }
        fclose($fin);
    }

    public function combinedMp3Action()
    {

        $uri = $_SERVER['REQUEST_URI'];
        $matches = [];
        if (!preg_match('/^\/mp3\/(\d+)-(\d+).mp3$/', $uri, $matches, PREG_OFFSET_CAPTURE)) {
            header('HTTP/1.0 500 Internal server error');
            die("Unparsable parameter");
        }

        //ok we have the parameters
        $start = (int)$matches[1][0];
        $duration = (int)$matches[2][0];

        //requried headers
        $filename = sprintf("tilos-%d-%d", $start, $duration);
        header("Content-Type: audio/mpeg");
        header("Content-Disposition: attachment; filename=\"$filename.mp3\"");


        //check the files and caclucate the sizes
        $filesize = 0;
        foreach ($this->getMp3Links($start, $duration) as $resource) {
            $fn = "../archive-files/online" . $resource['filename'];
            if (!file_exists($fn)) {
                header('HTTP/1.0 404 Not Found');
                die("Archive is missing: " . $fn);

            } else {
                $filesize += filesize($fn);
            }

        }

        //stream the content to the browser
        header("Content-Length: " . $filesize);
        foreach ($this->getMp3Links($start, $duration) as $resource) {
            $this->chunked_copy("../archive-files/online" . $resource['filename']);
        }

        die("");
    }
}

$mp3 = new Mp3Streamer();
$mp3->combinedMp3Action();
