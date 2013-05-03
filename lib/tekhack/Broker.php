<?php
require_once 'facebook-php-sdk-master/src/facebook.php';
class Broker
{
    /*
     * Calling access token service
     * curl "https://graph.facebook.com/oauth/access_token?
     *   client_id=562151333825424&
     *   client_secret=4f34a1004b180dd3c0994164e5db011e&
     *   grant_type=client_credentials"
     *
     * Response
     * access_token=562151333825424|JaeMyAxGBgMf_n2cfbw5Aq9PutUphpbook:tekhack
     */


    const FB_API_KEY = '562151333825424';
    const FB_API_SEC = '4f34a1004b180dd3c0994164e5db011e';
    const FB_ACCESS_TOKEN = '562151333825424|JaeMyAxGBgMf_n2cfbw5Aq9PutU';
    const FB_EVENT_ID = '364378906997047';
    const FB_GRAPH_URL = 'https://graph.facebook.com';

    /**
     * @var Cache
     */
    protected $_cache;

    /**
     * Sets an optional cache object
     * @param Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Retrieve the cache object
     * @return Cache
     */
    public function getCache()
    {
        if (null === $this->_cache) {
            throw new RuntimeException('Cache is not set yet');
        }
        return $this->_cache;
    }

    /**
     * Get all participants for a particular Facebook event
     *
     * @params int $eventId
     * @return array
     */
    public function getEventParticipants($eventId = null)
    {
        if (null === $eventId) {
            $eventId = self::FB_EVENT_ID;
        }
        if (false === ($fql_query_obj = $this->getCache()->load($eventId . '_participants'))) {
            $query1 = 'SELECT uid FROM event_member WHERE eid=' . $eventId . ' AND rsvp_status="attending"';
            $query2 = 'SELECT username, name, url, pic, pic_crop FROM profile WHERE id IN (' . $query1 . ')';
            $fql_query_url = sprintf('%s/fql?q=%s&access_token=%s',
                self::FB_GRAPH_URL,
                urlencode($query2),
                self::FB_ACCESS_TOKEN
            );
            $fql_query_result = file_get_contents($fql_query_url);
            $fql_query_obj = json_decode($fql_query_result, true);
            $this->getCache()->save($eventId . '_participants', $fql_query_obj);
        }
        return $fql_query_obj;
    }
}