# Webcrawler

Webcrawler written in PHP using the [PHPCrawl library](http://phpcrawl.cuab.de/).

[`Crawler.php`](Crawler.php) archives the content found under the specified domain to your local file system.

Parallel or separately, the content of web pages can also be checked for certain regex patterns, whereby matches are written to a log file.

[`DomainCheck.php`](DomainCheck.php) checks the URLs under the specified domain for regex patterns (e.g. whether certain paths are under a domain).

Use it with care and at your own risk.
To crawl the domain less aggressively and thus attract attention, only one request per second is sent.

If the crawler is used for a foreign site it is also advisable to remove the comment of the line `//$crawler->obeyRobotsTxt(true);` at the end of the classes.

This way the crawler will only consider paths that are defined in the `robots.txt` of the website.
