<?php

include 'vendor/mmerian/phpcrawl/libs/PHPCrawler.class.php';

class Crawler extends PHPCrawler
{
    public $counter = 0;
    public $folderCounter = 0;
    public $fileCounter = 0;
    public $lb = "\n";

    public $createPathStructure = true;
    public $searchForNeedle = false;
    public $needle = '';

    public $domain = '';
    // Folder on the Filesystem where the webcontent should be saved
    public $destination = '';
    public $logfile = '../logfiles/log.txt';

    /**
     * Gets executed when running the Crawler.
     *
     * @return int|void
     */
    public function handleDocumentInfo(PHPCrawlerDocumentInfo $PageInfo)
    {
        if (200 == $PageInfo->http_status_code && true == $this->createPathStructure) {
            $path = explode($this->domain, $PageInfo->url);
            echo $path[1].$this->lb;
            $fileData = ['dest' => $this->destination, 'path' => $path[1]];
            $this->createPathStructure($fileData, $PageInfo);
        }

        if (true == $this->searchForNeedle) {
            $this->findOccurences($PageInfo, $this->needle);
        }

        if (!$PageInfo->received_completely && 0 == $PageInfo->bytes_received) {
            ++$this->counter;
        }
    }

    /**
     * Returns Line as String in which searched Keyword appears.
     *
     * @param $content
     * @param $needle
     *
     * @return mixed|string
     */
    public function getLineWithString($content, $needle)
    {
        $matches = [];
        preg_match_all($needle, $content, $matches);
        foreach ($matches as $match) {
            return $match;
        }

        return '';
    }

    /**
     * Puts all Occurences of Key into Status-File.
     *
     * @param $PageInfo
     * @param $needle
     */
    public function findOccurences($PageInfo, $needle)
    {
        if (preg_match_all($needle, $PageInfo->content, $matches)) {
            foreach ($matches as $match) {
                var_dump($match);
                $line = 'Zeile:'.$match[0].$this->lb;
                $res = 'Seite: '.$PageInfo->url.$this->lb.$line.$this->lb;
                echo '('.count($match).') Matches currently';
                file_put_contents($this->logfile, $res, FILE_APPEND);
            }
        }
    }

    /**
     * Creates Pathfolder Structure from given Domain
     * and copies it into given Directory.
     *
     * @param array  $fileData
     * @param object $PageInfo
     */
    public function createPathStructure($fileData, $PageInfo)
    {
        $filePathName = $fileData['dest'].$fileData['path'];
        $folderPathName = $fileData['dest'].dirname(parse_url($PageInfo->url, PHP_URL_PATH)).'/';

        echo $filePathName.$this->lb;
        echo $folderPathName.$this->lb;

        if (file_exists($folderPathName)) {
            if (false !== strpos($PageInfo->url, ' ?')) {
                $corrected_filepath = explode('?', $fileData['path']);
                echo $corrected_filepath;
                file_put_contents($fileData['dest'].$corrected_filepath[0], $PageInfo->content);
            } else {
                file_put_contents($filePathName, $PageInfo->content);
                ++$this->fileCounter;
            }
        } else {
            mkdir($folderPathName, 777, true);
            ++$this->folderCounter;
            echo $filePathName;
            file_put_contents($filePathName, $PageInfo->content);
        }
    }
}

$crawler = new Crawler();

$crawler->setURL($crawler->domain);
$crawler->addContentTypeReceiveRule('#text/html#');
$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png|svg|ico)$# i");
$crawler->addURLFilterRule('#(css|js)$# i');
$crawler->setRequestDelay(1);
//$crawler->setFollowMode(3);
//$crawler->obeyRobotsTxt(true);
$crawler->go();

$report = $crawler->getProcessReport();

echo 'Summary:'.$crawler->lb;

echo 'The Content of: '.$crawler->counter.' Pages could not get received.'.$crawler->lb;
echo $crawler->folderCounter.' Folders have been created.'.$crawler->lb;
echo $crawler->fileCounter.' Files have been created.'.$crawler->lb.$crawler->lb;

echo 'Links followed: '.$report->links_followed.$crawler->lb;
echo 'Documents received: '.$report->files_received.$crawler->lb;
echo 'Bytes received: '.$report->bytes_received.' bytes'.$crawler->lb;
echo 'Process runtime: '.gmdate('H:i:s', $report->process_runtime).$crawler->lb;
