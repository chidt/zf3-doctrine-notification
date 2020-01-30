<?php


namespace Application\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User Entity Class
 * @ORM\Entity()
 * @ORM\Table("notification")
 */
class Notification
{
    const TYPE_POST_COMMENT = 'post.comment';

    /**
     * @ORM\Id()
     * @ORM\Column("id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column("type")
     */
    protected $type;

    /**
     * @ORM\Column("parameters")
     */
    protected $parameters;


    /**
     * @ORM\OneToMany(targetEntity="Application\Entity\NotificationReader", mappedBy="notification",cascade={"persist","remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="notification_id",onDelete="CASCADE")
     */
    protected $notification_readers;

    /**
     * Notification constructor.
     */
    public function __construct()
    {
        $this->notification_readers = new ArrayCollection();
    }


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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param mixed $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = json_encode($parameters);
    }

    /**
     * @return User
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param mixed $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return mixed
     */
    public function getNotificationReaders()
    {
        return $this->notification_readers;
    }

    /**
     * @param mixed $notification_readers
     */
    public function setNotificationReaders($recipients)
    {
        if (is_array($recipients)) {
            /**
             * @var $recipient User
             */
            foreach ($recipients as $recipient) {
                $notificationReader = new NotificationReader();
                $notificationReader->setUnread(1);
                $notificationReader->setRecipient($recipient);
                $notificationReader->setNotification($this);
                $this->notification_readers[] = $notificationReader;
            }
        }else{
            $notificationReader = new NotificationReader();
            $notificationReader->setUnread(1);
            $notificationReader->setRecipient($recipients);
            $notificationReader->setNotification($this);
            $this->notification_readers[] = $notificationReader;
        }
    }


    public function addNotificationReaders(Collection $notificationsReader)
    {
        /**
         * @var $notificationReader NotificationReader
         */
        foreach ($notificationsReader as $notificationReader) {
            $notificationReader->setNotification($this);
            $this->notification_readers->add($notificationReader);
        }
    }

    public function removeNotificationReaders($notifications)
    {
        if (count($notifications) > 1) {
            /**
             * @var $notification NotificationReader
             */
            foreach ($notifications as $notification) {
                $this->notification_readers->removeElement($notification);
            }
        } else {
            $this->notification_readers->removeElement($notifications);

        }

    }


}