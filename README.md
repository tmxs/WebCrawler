# Webcrawler

Webcrawler written in PHP using the [PHPCrawl library](http://phpcrawl.cuab.de/).

[`Crawler.php`](Crawler.php) archives the content found under the specified domain to your local file system.

Parallel or separately, the content of web pages can also be checked for certain regex patterns, whereby matches are written to a log file.

[`DomainCheck.php`](DomainCheck.php) checks the URLs under the specified domain for regex patterns (e.g. whether certain paths are under a domain).

Use it with care and at your own risk.
To crawl the domain less aggressively and thus attract attention, only one request per second is sent.

If the crawler is used for a foreign site it is also advisable to remove the comment of the line `//$crawler->obeyRobotsTxt(true);` at the end of the classes.

This way the crawler will only consider paths that are defined in the `robots.txt` of the website.


## Installation

1. Clone the Repository `git clone https://github.com/tmxs/WebCrawler.git`
2. Run `composer install` (in the project root folder) if you have PHP installed on your host system
2.1 Otherwise use a simple [Docker environment](https://github.com/tmxs/DockerTemplates/tree/master/Minimal-PHP-MySQL)
2.2 Move the content of this repository to the src-Directory
2.3 Then log into the container with `docker-compose exec web bash` and run `composer install`
3. In order to use the crawler correctly you can adjust some of the defined variables in the two classes to define the domain or the regex pattern.
4. No matter if you are in the container or on your host system you have to start the crawler by executing the particular file with `php Filename.php`
