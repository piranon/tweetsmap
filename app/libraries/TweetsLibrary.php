<?php
/**
 * TweetsLibrary is responsible for prepare search criteria,
 * Interrogating Twitter API for list of tweet objects and response tweets in array format.
 *
 */
class TweetsLibrary
{

    /**
     * @var \Phalcon\Config $config
     *          An instance of \Phalcon\Config
     */
    private $config;

    /**
     * @var TwitterAPIExchange $twitterAPIExchange
     *          An instance of TwitterAPIExchange Class
     */
    private $twitterAPIExchange;

    /**
     * Sets the config
     *
     * @param \Phalcon\Config $config
     *         Configuration data within applications.
     */
    public function setConfig(\Phalcon\Config $config)
    {
        $this->config = $config;
    }

    /**
     * Sets the TwitterAPIExchange
     *
     * @param TwitterAPIExchange $twitterAPIExchange
     *         Simple PHP Wrapper for Twitter API v1.1 calls
     */
    public function setTwitterAPIExchange(TwitterAPIExchange $twitterAPIExchange)
    {
        $this->twitterAPIExchange = $twitterAPIExchange;
    }

    /**
     * Get the Tweets from api twitter with search criteria.
     *
     * @param array $options    search criteria.
     * @return array    Collection of relevant Tweets matching a specified query.
     */
    public function getTweets($options)
    {
        $searchFields = $this->createSearchFields($options);
        $twitterAPIData = $this->getDataFromAPI($searchFields);
        $tweets = $this->prepareDataTweets($twitterAPIData);
        return $tweets;
    }

    /**
     * Create search fields for Twitter API.
     *
     * @param array $options    search criteria.
     * @return string   search query string.
     */
    private function createSearchFields($options)
    {
        $city = $options['city'];
        $lat = $options['lat'];
        $lng = $options['lng'];
        $limit = $this->config->searchLimit;
        $radius = $this->config->searchRadius;
        $getfield = '?q=' . $city;
        $getfield .= '&result_type=mixed';
        $getfield .= '&count=' . $limit;
        $getfield .= '&geocode=' . $lat . ',' . $lng . ',' . $radius;
        return $getfield;
    }

    /**
     * Get tweets data from Twitter API.
     *
     * @param string $searchFields  search query string.
     * @return array    Tweets objects
     */
    private function getDataFromAPI($searchFields)
    {
        $url = $this->config->searchUrl;
        $twitterAPI = $this->twitterAPIExchange->setGetfield($searchFields);
        $twitterAPI = $twitterAPI->buildOauth($url, 'GET');
        $jsonData = $twitterAPI->performRequest();
        return json_decode($jsonData, true);
    }

    /**
     * Convert Tweets objects to array format.
     *
     * @param array $twitterAPIData Tweets objects.
     * @return array    Tweets in array format.
     */
    private function prepareDataTweets($twitterAPIData)
    {
        $tweets = array();
        foreach ($twitterAPIData['statuses'] as $data) {
            $tweet = array();
            $tweet['text'] = $data['text'];
            $tweet['createdAt'] = $this->convertTwitterDate($data['created_at']);
            $tweet['userName'] = $data['user']['name'];
            $tweet['userProfileImageUrl'] = $data['user']['profile_image_url'];
            $tweet['lat'] = $data['coordinates']['coordinates'][1];
            $tweet['lng'] = $data['coordinates']['coordinates'][0];
            $tweets[] = $tweet;
        }
        return $tweets;
    }

    /**
     * Convert Twitter date format to php date format.
     *
     * @param string $date Twitter date.
     * @return string   PHP date format.
     */
    private function convertTwitterDate($date)
    {
        $date = str_replace('+0000', '', $date);
        return date('Y-m-d H:i:s', strtotime($date));
    }
}
