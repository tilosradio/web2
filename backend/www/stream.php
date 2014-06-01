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
        $end = $start + $duration * 60;

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


        return [$start - $from, $res];
    }


    function chunked_copy($from, $offset = 0)
    {
        # 1 megabyte buffer
        $buffer_size = 1048576;
        $ret = 0;
        $fin = fopen($from, "rb");
        if ($fin == false) {
            echo "--$from--";
            die("file open error");
        }
        if ($offset) {
            fseek($fin, $offset);
        }
        while (!feof($fin)) {
            echo fread($fin, $buffer_size);
        }
        fclose($fin);
    }

    public function combinedMp3Action()
    {

        $archiveLocation = "/home/elek/projects/tilos/backend/archive-files/online";

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

        $origin = $this->getMp3Links($start, $duration);
        //skip the first $offset seconds.
        $offset = $origin[0];
        $files = $origin[1];
        //convert to byte
        $offset = (int)($offset * 38.28125 * 836);

        //check the files and caclucate the sizes
        $filesize = 0;
        foreach ($files as $resource) {
            $fn = $archiveLocation . $resource['filename'];
            if (!file_exists($fn)) {
                header('HTTP/1.0 404 Not Found');
                die("Archive is missing: " . $fn);

            } else {
                $filesize += filesize($fn);
            }

        }
        $filesize -= $offset;
        //stream the content to the browser
        header("Content-Length: " . $filesize);
        for ($i = 0; $i < sizeof($files); $i++) {
            $this->chunked_copy($archiveLocation . $files[$i]['filename'], $i == 0 ? $offset: 0);
        }

        die("");
    }
}

$mp3 = new Mp3Streamer();
$mp3->combinedMp3Action();
