<?php


namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Entity\Notification;

/**
 * User Entity Class
 * @package Notification\Entity
 * @ORM\Entity()
 * @ORM\Table("notification_reader")
 */
class NotificationReader
{
    /**
     * @ORM\Id()
     * @ORM\Column("id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column("unread",options={"default" : 1})
     */
    protected $unread;


    /**
     * @var $sender Notification
     * @ORM\ManyToOne(targetEntity="Application\Entity\Notification",inversedBy="notification_readers")
     * @ORM\JoinColumn(name="notification_id",referencedColumnName="id")
     */
    protected $notification;

    /**
     * @var $recipients User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User",inversedBy="notification_readers")
     * @ORM\JoinColumn(name="recipient_id",referencedColumnName="id")
     */
    protected $recipient;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUnread()
    {
        return $this->unread;
    }

    /**
     * @param mixed $unread
     */
    public function setUnread($unread)
    {
        $this->unread = $unread;
    }

    /**
     * @return Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param User $notification
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return User
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param User $recipient
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    public function makeRead()
    {
        $this->unread = 0;
    }

}