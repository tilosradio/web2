<?php

namespace Radio\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Generate m3u files on the fly.
 */
class M3u extends AbstractActionController
{



    public function downloadAction()
    {
        $out = "#EXTM3U\n";
        $start = (int)$this->params()->fromRoute("from");
        $duration = (int)$this->params()->fromRoute("duration");

    	$response = $this->getM3Ufile($start, $duration);
    	return $response;
    }

	public function anotherlinkAction()
	{
		$date = (int)$this->params()->fromRoute("date");
		$from = $this->params()->fromRoute("from");
		$to = $this->params()->fromRoute("to");


		$start = (strtotime($date." ".$from));
		$end = (strtotime($date." ".$to));
		$duration = round(abs($end - $start) / 60,2);

		$response = $this->getM3Ufile($start, $duration+1);
		return $response;
	}

	public function getM3Ufile($start, $duration)
	{
        $out="";
		$from = EpisodeUtil::getPrevHalfHour($start);
		foreach (EpisodeUtil::getMp3StreamLinks($start, $duration) as $resource) {
			$d = $resource['datearray'];
			$timestr = sprintf("%02d%02d", $d['hours'], $d['minutes']);
			$out .= sprintf("#EXTINF:1805,Tilos %02d.%02d.%02d %s\n", $d['year'], $d['mon'], $d['mday'], $timestr);
			$out .= $resource['file'] . "\n";
		}

		$response = $this->getResponse();
		$response->setStatusCode(200);
		$response->getHeaders()->addHeaders(array(
                'Content-Type' => 'audio/x-mpegurl; charset=utf-8',
                'Content-Disposition' => sprintf("filename=\"tilos-%02d%02d%02d-%d-%d.m3u\"", $d['year'], $d['mon'], $d['mday'], $timestr, $from))
		);

		$response->setContent($out);
		return $response;
	}

    function chunked_copy($from)
    {
        # 1 meg at a time, you can adjust this.
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

    public function combinedMp3Action($e)
    {

        $start = (int)$this->params()->fromRoute("from");
        $duration = (int)$this->params()->fromRoute("duration");

        $filename = sprintf("tilos-%d-%d", $start, $duration);
        header("Content-Type: audio/mpeg");
        header("Content-Disposition: attachment; filename=\"$filename.mp3\"");


        $filesize = 0;
        foreach (EpisodeUtil::getMp3Links($start, $duration) as $resource) {
            //passthru($resource['file']);
            $fn = "../archive" . $resource['filename'];
            if (!file_exists($fn)) {
                header('HTTP/1.0 404 Not Found');
                die("Archive is missing: " . $fn);

            } else {
                $filesize += filesize($fn);
            }

        }
        header("Content-Length: " . $filesize);
        foreach (EpisodeUtil::getMp3Links($start, $duration) as $resource) {
            //passthru($resource['file']);
            $this->chunked_copy("../archive" . $resource['filename']);
        }

        die("");
    }

}

?>














