<?php

require __DIR__ . "/vendor/autoload.php";

$url = "https://support.skype.com/en/faq/FA12330/what-is-the-full-list-of-emoticons";

$html = file_get_contents( $url );

$crawler = new \Symfony\Component\DomCrawler\Crawler();
$crawler->addHtmlContent($html);
$tables = $crawler->filter( 'div.collapsibleContent div.content table');
$emoticons_list = [];
$tables->each( function ( \Symfony\Component\DomCrawler\Crawler $table ) use (&$emoticons_list){
    $table->filter( 'tr')->each( function ( \Symfony\Component\DomCrawler\Crawler $tr, $i ) use (&$emoticons_list) {
        if($i == 0){
            return;
        }
        $tds = $tr->filter( 'td');
        $emoticon = [];
        $emoticon['image'] = $tds->first()->filter( 'img')->attr( 'src');
        $emoticon['name'] = $tds->eq(1)->text();
        $emoticon['des'] = $tds->eq(2)->text();
        preg_match( "/\(([^\(\)]+)\)/", $emoticon['des'], $matches);
        $emoticon['code'] = $matches[1];
        $emoticons_list[] = $emoticon;
    });
});

file_put_contents( __DIR__ . '/emoticons_list.js', "var emoticons_list = " . json_encode( $emoticons_list) . ";");