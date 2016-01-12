<?php

namespace Citrix;

use Citrix\Authentication\Authentication;
use Citrix\Entity\Meeting;
use Citrix\Entity\Consumer;

/**
 * Use this to get/post data from/to Citrix.
 * 
 * @uses \Citrix\ServiceAbstract
 * @uses \Citrix\CitrixApiAware
 */
class GoToMeeting extends ServiceAbstract implements CitrixApiAware {

    /**
     * Authentication Client
     * 
     * @var Citrix
     */
    private $client;

    /**
     * Meeting API Base URI
     * @var String
     */
    private $uri = 'https://api.citrixonline.com/G2M/rest/';

    /**
     * Meeting API Base URI
     * @var String
     */
    private $organizerUri = 'organizers/';

    /**
     * Begin here by passing an authentication class.
     * 
     * @param $client - authentication client
     */
    public function __construct($client) {
        $this->setClient($client);
    }

    /**
     * Get All upcoming Meeting.
     * 
     * @return \ArrayObject - Processed response
     */
    public function getUpcoming() {

        $url = $this->getUri() . '/upcomingMeetings';
        $this->setHttpMethod('GET')
                ->setUrl($url)
                ->sendRequest($this->getClient()->getAccessToken())
                ->processResponse();

        return $this->getResponse();
    }

    /**
     * Get upcoming Meeting.
     * 
     * @return \ArrayObject - Processed response
     */
    public function getUpcomingByOrganizer() {

        $url = $this->getUri() . $this->getOrganizerUri() . $this->getClient()->getOrganizerKey() . '/upcomingMeetings';
        $this->setHttpMethod('GET')
                ->setUrl($url)
                ->sendRequest($this->getClient()->getAccessToken())
                ->processResponse();

        return $this->getResponse();
    }

    /**
     * Get all Meetings.
     *
     * @return \ArrayObject - Processed response
     */
    public function getMeetings() {

        $url = $this->getUri() . '/historicalMeetings';
        $this->setHttpMethod('GET')
                ->setUrl($url)
                ->sendRequest($this->getClient()->getAccessToken())
                ->processResponse();

        return $this->getResponse();
    }

    /**
     * Get all Meetings.
     *
     * @return \ArrayObject - Processed response
     */
    public function getMeetingsByOrganizer() {

        $url = $this->getUri() . $this->getOrganizerUri() . $this->getClient()->getOrganizerKey() . '/historicalMeetings';
        $this->setHttpMethod('GET')
                ->setUrl($url)
                ->sendRequest($this->getClient()->getAccessToken())
                ->processResponse();

        return $this->getResponse();
    }

    /**
     * Get info for a single meetings by passing the meeting id or 
     * in Citrix's terms meetingKey.
     * 
     * @param int $meetingKey
     * @return \Citrix\Entity\Meeting
     */
    public function getMeeting($meetingId) {
        $url = $this->getUri() . '/meetings/' . $meetingId;
        $this->setHttpMethod('GET')
                ->setUrl($url)
                ->sendRequest($this->getClient()->getAccessToken())
                ->processResponse(true);

        return $this->getResponse();
    }

    public function createMeeting($params) {
        $url = $this->getUri() . '/meetings';
//         print_r($url);
//         exit;
        $this->setHttpMethod('POST')
                ->setUrl($url)
                ->setParams($params)
                ->sendRequest($this->getClient()->getAccessToken());
//                ->processResponse();

        return $this->getResponse();
    }

    /**
     * Get all attendees for a given meeting.
     *
     * @param int $meetingInstanceKey
     * @return \Citrix\Entity\Consumer
     */
    public function getAttendees($meetingInstanceKey) {

        $url = $this->getUri() . '/meetings/' . $meetingInstanceKey . '/attendees';
        $this->setHttpMethod('GET')
                ->setUrl($url)
                ->sendRequest($this->getClient()->getAccessToken())
                ->processResponse();

        return $this->getResponse();
    }

    /**
     * Get all attendees for a given meeting.
     *
     * @param int $meetingInstanceKey
     * @return \Citrix\Entity\Consumer
     */
    public function startMeeting($meetingInstanceKey) {

        $url = $this->getUri() . 'meetings/' . $meetingInstanceKey . '/start';
        $this->setHttpMethod('GET')
                ->setUrl($url)
                ->sendRequest($this->getClient()->getAccessToken())
                ->processResponse(TRUE);

        return $this->getResponse();
    }

    /**
     *
     * @return $uri
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     *
     * @return void
     * set the $uri
     */
    public function setUri($uri = '') {
        $this->uri = (!empty($uri)) ? $uri : $this->uri;
        return $this;
    }

    /**
     *
     * @return the $organizerUri
     */
    public function getOrganizerUri() {
        return $this->organizerUri;
    }

    /**
     *
     * set the $organizerUri
     */
    public function setOrganizerUri($uri = '') {
        $this->organizerUri = (!empty($uri)) ? $uri : $this->organizerUri;
        return $this;
    }

    /**
     *
     * @return the $client
     */
    private function getClient() {
        return $this->client;
    }

    /**
     *
     * @param Citrix $client          
     */
    private function setClient($client) {
        $this->client = $client;

        return $this;
    }

    /* (non-PHPdoc)
     * @see \Citrix\CitrixApiAware::processResponse()
     */

    /**
     * @param bool $single    If we expect a single entity from the server, make this true.
     *                        Single webinar request wasn't working because it was looping its properties.
     */
    public function processResponse($single = false) {

        $response = $this->getResponse();

        $this->reset();

        if (isset($response['int_err_code'])) {
            $this->addError($response['msg']);
        }

        if ($single === true) {

            if (isset($response['hostURL'])) {
                
                $meeting = new Meeting($this->getClient());
                $meeting->setData($response)->populate();
                $this->setResponse($meeting);
                
            } elseif ($response['meetingType'] == 'scheduled') {

                if (isset($response['meetingId'])) {
                    $meeting = new Meeting($this->getClient());
                    $meeting->setData($response)->populate();
                    $this->setResponse($meeting);
                }

                if (isset($response['numAttendees']) && ($response['numAttendees'] > 0)) {
                    $meeting = new Consumer($this->getClient());
                    $meeting->setData($response)->populate();
                    $this->setResponse($meeting);
                }
            }
        } else {
            $collection = new \ArrayObject(array());

            foreach ($response as $entity) {

//                if ($entity['meetingType'] == 'scheduled') {

                if (isset($entity['meetingId'])) {
                    $meeting = new Meeting($this->getClient());
                    $meeting->setData($entity)->populate();
                    $collection->append($meeting);
                }

                if (isset($entity['numAttendees']) && ($entity['numAttendees'] > 0)) {
                    $meeting = new Consumer($this->getClient());
                    $meeting->setData($entity)->populate();
                    $collection->append($meeting);
                }
//                }
            }

            $this->setResponse($collection);
        }
    }

}

?>
