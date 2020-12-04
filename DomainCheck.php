<?php

include 'vendor/mmerian/phpcrawl/libs/PHPCrawler.class.php';

class DomainCheck extends PHPCrawler
{
    public $lb = "\n";
    public $domain = '';
    public $logfile = '../logfiles/logfile1.txt';
    public $errorLogFile = '../logfiles/errors.txt';
    public $counter = 0;

    /**
     * Gets executed when running the Crawler.
     *
     * @return int|void
     */
    public function handleDocumentInfo(PHPCrawlerDocumentInfo $PageInfo)
    {
        // Some url regex pattern to check if
        $pattern = '';
        $replacement = '';

        $subject = $PageInfo->url;

        if (1 == preg_match($pattern, $subject)) {
            if ($this->checkIfLinkExists($subject, $PageInfo->url, false)) {
                $modifiedUrl = preg_replace($pattern, $replacement, $subject);
                $this->checkIfLinkExists($modifiedUrl, $subject, true);
            }
        }
    }

    /**
     * Checks the HTTP-Statuscode by the given URL.
     *
     * @param $modifiedUrl string
     * @param $originUrl string
     * @param $logNonExistentLink boolean
     *
     * @return bool
     */
    public function checkIfLinkExists($modifiedUrl, $originUrl, $logNonExistentLink)
    {
        if ($logNonExistentLink) {
            $url = $modifiedUrl;
        } else {
            $url = $originUrl;
        }
        $timeout = 10;
        $curlResource = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER, true,
            CURLOPT_NOBODY, true,
        ];

        curl_setopt_array($curlResource, $options);
        curl_exec($curlResource);
        $status = curl_getinfo($curlResource, CURLINFO_HTTP_CODE);

        if (curl_errno($curlResource) | 200 != $status && 404 == $status) {
            $unreachableUrl = "[{$status}] => ".$modifiedUrl.$this->lb.'[ORIGIN_URL] => '.$originUrl.$this->lb.$this->lb;
            echo $unreachableUrl;
            echo curl_error($curlResource).$this->lb;
            if ($unreachableUrl != $originUrl) {
                file_put_contents($this->errorLogFile, $unreachableUrl, FILE_APPEND);
            }
            ++$this->counter;
        } else {
            return true;
        }
        curl_close($curlResource);
    }
}

$crawler = new DomainCheck();
$crawler->setURL($crawler->domain);
$crawler->addContentTypeReceiveRule('#text/html#');
$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png|svg|ico)$# i");
$crawler->addURLFilterRule('#(css|js)$# i');
$crawler->setFollowMode(3);
$crawler->setRequestDelay(1);
//$crawler->obeyRobotsTxt(true);
$crawler->go();

$report = $crawler->getProcessReport();

echo 'Summary:'.$crawler->lb;
echo 'Links followed: '.$report->links_followed.$crawler->lb;
echo 'Documents received: '.$report->files_received.$crawler->lb;
echo 'Bytes received: '.$report->bytes_received.' bytes'.$crawler->lb;
echo 'Process runtime: '.gmdate('H:i:s', $report->process_runtime).$crawler->lb;
echo $crawler->counter.' INHALTE SIND NICHT UNTER '.$domain.'VERFÜGBAR'.$crawler->lb;
