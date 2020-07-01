<?php
namespace acidjazz\tubestrip;

use Exception;
use Illuminate\Support\Facades\Http;
use stdClass;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class TubeStrip
 * @package acidjazz\tubestrip
 */
class TubeStrip
{
    /*
     * Javascript response regex
     */
    const JSON_REGEX = '/{"responseContext":(.*)}/';

    /*
     * HTML response regex
     */
    const BODY_REGEX = '/<body .*>(.*?)<\/body>/s';

    /**
     * Force a JSON response for coverage
     */
    private bool $forceJson;

    public function __construct($forceJson = false)
    {
        $this->forceJson = $forceJson;
    }

    /**
     * Get a particular video detail
     * @param string $videoId
     * @return object
     */
    public function get(string $videoId)
    {
        $response = Http::withHeaders($this->forceJson ? [
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36',
        ] : [
            'User-Agent' => 'tubestrip',
        ])
            ->get('https://www.youtube.com/watch?v=' . $videoId);

        $html = $response->body();
        preg_match(self::JSON_REGEX, $html, $matches);
        if (isset($matches[0])) {
            $contents = json_decode($matches[0])
                ->contents
                ->twoColumnWatchNextResults
                ->results
                ->results
                ->contents;
            $primary = $contents[0]->videoPrimaryInfoRenderer;
            $secondary = $contents[1]->videoSecondaryInfoRenderer;
            $description = join('', array_map(fn ($d) => $d->text, isset($secondary->description) ? $secondary->description->runs : []));

            return (object) [
                'title' => $primary->title->runs[0]->text,
                'description' => $description,
                'viewCount' => $this->viewsToInt(
                    $primary->viewCount->videoViewCountRenderer->viewCount->simpleText
                ),
                'date' => $this->parseDate($primary->dateText->simpleText),
            ];
        }
        return $this->getParseHTML($html);
    }

    /**
     * Strip textual parts of a date for Carbon or strtotime
     *
     * @param $date
     * @return string|string[]
     */
    private function parseDate($date)
    {
        return str_replace(['Premiered ','Published on ', ','], ['', '', ''], $date);
    }

    /**
     * Parse an HTML response
     *
     * @param $html
     * @return object
     */
    public function getParseHTML($html)
    {
        preg_match(self::BODY_REGEX, $html, $matches);
        $crawler = new Crawler($matches[0]);

        $title = $crawler->filter('#eow-title')->text();
        $description = strip_tags(
            $crawler->filter('#eow-description')->html(),
            '<br>');
        $viewCount = $crawler->filter('.watch-view-count')->text();
        $date = $crawler->filter('.watch-time-text')->text();
        return (object) [
            'title' => $title,
            'description' => str_replace('<br>', "\n",$description),
            'viewCount' => $this->viewsToInt($viewCount),
            'date' => $this->parseDate($date),
        ];
    }

    /**
     * Strip views into an integer
     *
     * @param string $text
     * @return int
     */
    public function viewsToInt(string $text): int {
        return (int) str_replace([' views', ','], ['', ''], $text);
    }

    /**
     * Search and return the 1st pages' results
     *
     * @param string $term
     * @return array
     */
    public function search(string $term): array
    {
        $response = Http::withHeaders($this->forceJson ? [
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36',
        ] : [
            'User-Agent' => 'tubestrip',
        ])
            ->get('https://www.youtube.com/results?search_query=' . urlencode($term));
        $html = $response->body();
        preg_match(self::JSON_REGEX, $html, $matches);
        if (isset($matches[0])) {
            return $this->searchParseJson($matches[0]);
        } else {
            return $this->searchParseHTML($html);
        }
    }

    /**
     * Parse a search term Javascript response
     *
     * @param $json
     * @return array
     */
    public function searchParseJson($json): array
    {
        $contents = json_decode($json)
            ->contents
            ->twoColumnSearchResultsRenderer
            ->primaryContents
            ->sectionListRenderer
            ->contents[0]
            ->itemSectionRenderer
            ->contents;
        $results = [];
        foreach ($contents as $video) {
            if (property_exists($video, 'videoRenderer')) {
                $results[] = (object) [
                    'id' => $video->videoRenderer->videoId,
                    'title' => $video->videoRenderer->title->runs[0]->text,
                ];
            }
        }
        return $results;
    }

    /**
     * Parse an HTML search response
     *
     * @param $html
     * @return array
     */
    private function searchParseHTML($html): array
    {
        preg_match(self::BODY_REGEX, $html, $matches);
        $crawler = new Crawler($matches[0]);
        $crawler = $crawler->filter('.item-section');
        $results = $crawler->filter('li')->each(fn ($node) => $this->parseNode($node));
        $parsed = (array_values(array_filter($results, fn($r) => property_exists($r, 'id'))));
        return $parsed;
    }

    /**
     * Parse a particular result node
     *
     * @param Crawler $node
     * @return object
     */
    private function parseNode(Crawler $node): object
    {
        try {
            return (object) [
                'id' => substr(
                    $node->filter('.yt-lockup-title > a')->attr('href'),
                    9
                ),
                'title' => $node->filter('.yt-lockup-title > a')->attr('title'),
            ];
        } catch (Exception $exception) {}
        return new stdClass();
    }
}
