<?php

namespace Citrix\Entity;

use Citrix\GoToMeeting;

/**
 * Webinar Entity
 *
 * Contains all fields for a Webinar. It also provides additional functionality
 * such as registering a user for a webinar
 *
 * @uses \Citrix\Entity\EntityAbstract
 * @uses \Citrix\Entity\EntityAware
 *      
 */
class Meeting extends EntityAbstract implements EntityAware {

    /**
     * Unique identifier, in Citrix World
     * this is called MeetingKey
     *
     * @var integer
     */
    public $id;

    /**
     * Start time of the meeting in ISO8601 format
     *
     * @var string
     */
    public $startTime;

    /**
     * End time of the meeting in ISO8601 format
     *
     * @var string
     */
    public $endTime;

    /**
     * First Name of the meeting creator
     *
     * @var string
     */
    public $firstName;

    /**
     * Last Name of the meeting creator
     *
     * @var string
     */
    public $lastName;

    /**
     * Go to Meeting Account Id
     * in Citrix none as accountKey
     *
     * @var big integer
     */
    public $accountKey;

    /**
     * whether password required to join the meeting
     *
     * @var boolean
     */
    public $passwordRequired;

    /**
     * email of the meeting creator
     *
     * @var string
     */
    public $email;

    /**
     * status of the meeting
     *
     * @var string
     */
    public $status;

    /**
     * subject of the meeting
     *
     * @var string
     */
    public $subject;

    /**
     * language seeting of the meeting
     *
     * @var string
     */
    public $locale;

    /**
     * organizerKey of the meeting
     *
     * @var string
     */
    public $organizerKey;

    /**
     * Type of the meeting
     *
     * @var string
     */
    public $meetingType;

    /**
     * conference Call Info of the meeting
     *
     * @var string
     */
    public $conferenceCallInfo;

    /**
     * Host URL of the meeting
     *
     * @var string
     */
    public $hostURL;

    /**
     * Beging here by injecting an authentication object.
     * 
     * @param $client
     */
    public function __construct($client) {
        $this->setClient($client);
        $this->consumers = new \ArrayObject();
    }

    /*
     * (non-PHPdoc) @see \Citrix\Entity\EntityAware::populate()
     */

    public function populate() {
        $data = $this->getData();
        
        if (isset($data['meetingId'])) {
            $this->id = $data['meetingId'];
            unset($data['meetingId']);
        }

        foreach (get_object_vars($this) as $name => $val) {
            if (empty($val) && ($name != 'data') && isset($data[$name])) {
                $this->$name = $data[$name];
            }
        };

        return $this;
    }

    /**
     * Get all people that registered for
     * this webinar.
     * 
     * @return \ArrayObject
     */
    public function getAttendees() {
        $goToWebinar = new GoToMeeting($this->getClient());
        $registrants = $goToWebinar->getRegistrants($this->getId());
        return $registrants;
    }

    /**
     *
     * @return the $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     *
     * @param int $id          
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     *
     * @return the $organizerKey
     */
    public function getOrganizerKey() {
        return $this->organizerKey;
    }

    /**
     *
     * @param int $organizerKey          
     */
    public function setOrganizerKey($organizerKey) {
        $this->organizerKey = $organizerKey;

        return $this;
    }

    /**
     *
     * @return the $subject
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     *
     * @param String $subject          
     */
    public function setSubject($subject) {
        $this->subject = $subject;

        return $this;
    }

    /**
     *
     * @return the $starttime
     */
    public function getStartTime() {
        return $this->startTime;
    }

    /**
     *
     * @param string $time
     */
    public function setStartTime($time) {
        $this->startTime = $times;

        return $this;
    }

    /**
     *
     * @return the $endTime
     */
    public function getEndTime() {
        return $this->endTime;
    }

    /**
     *
     * @param string $time
     */
    public function setEndTime($time) {
        $this->endTime = $times;

        return $this;
    }

    /**
     *
     * @return the $firstName
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     *
     * @param string $firstName
     */
    public function setFirstName($firstName) {
        $this->firstName = $name;

        return $this;
    }

    /**
     *
     * @return the $lastName
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     *
     * @param string $lastName
     */
    public function setLastName($lastName) {
        $this->lastName = $name;

        return $this;
    }

    /**
     *
     * @return the $accountKey
     */
    public function getAccountKey() {
        return $this->accountKey;
    }

    /**
     *
     * @param string $accountKey
     */
    public function setAccountKey($accountKey) {
        $this->accountKey = $accountKey;

        return $this;
    }

    /**
     *
     * @return the $passwordRequired
     */
    public function getPasswordRequired() {
        return $this->passwordRequired;
    }

    /**
     *
     * @param string $passwordRequired
     */
    public function setPasswordRequired($passwordRequired) {
        $this->passwordRequired = boolval($passwordRequired);

        return $this;
    }

    /**
     *
     * @return the $email
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     *
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     *
     * @return the $status
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     *
     * @param string $status
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     *
     * @return the $locale
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     *
     * @param string $locale
     */
    public function setLocale($locale) {
        $this->locale = $locale;

        return $this;
    }

    /**
     *
     * @return the $meetingType
     */
    public function getMeetingType() {
        return $this->meetingType;
    }

    /**
     *
     * @param string $meetingType
     */
    public function setMeetingType($meetingType) {
        $this->meetingType = $meetingType;

        return $this;
    }

    /**
     *
     * @return the $conferenceCallInfo
     */
    public function getConferenceCallInfo() {
        return $this->meetingType;
    }

    /**
     *
     * @param string $conferenceCallInfo
     */
    public function setConferenceCallInfo($conferenceCallInfo) {
        $this->conferenceCallInfo = $conferenceCallInfo;

        return $this;
    }

}
