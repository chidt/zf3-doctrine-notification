<?php
/**
 * Created by PhpStorm.
 * User: CHI-DT
 * Date: 7/10/2018
 * Time: 4:27 PM
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * This class represents a single post in a blog
// * @ORM\Entity(repositoryClass= "\Application\Repository\PostRepository")
 * @ORM\Entity()
 * @ORM\Table(name="user")
 */

class User
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id")
     */
    protected $id;

    /**
     * @ORM\Column(name="name")
     */
    protected $name;

    /**
     * @ORM\Column(name="username")
     */
    protected $username;

    /**
     * @ORM\Column(name="password")
     */
    protected $password;


    /**
     * @ORM\OneToMany(targetEntity="\Application\Entity\Comment", mappedBy="author")
     * @ORM\JoinColumn(name="id", referencedColumnName="author_id")
     */
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="\Application\Entity\Comment", mappedBy="author")
     * @ORM\JoinColumn(name="id", referencedColumnName="author_id")
     */
    protected $posts;

    /**
     * @ORM\OneToMany(targetEntity="Application\Entity\NotificationReader", mappedBy="recipient")
     * @ORM\JoinColumn(name="id", referencedColumnName="recipient_id")
     */
    protected $notification_readers;


    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->posts = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }



    /**
     * Returns comments for this post.
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return ArrayCollection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param ArrayCollection $posts
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;
    }


    /**
     * Adds a new comment to this post.
     * @param $comment
     */
    public function addComment($comment)
    {
        $this->comments[] = $comment;
    }

    /**
     * Adds a new comment to this post.
     * @param $post
     */
    public function addPost($post)
    {
        $this->posts[] = $post;
    }

    /**
     * @return ArrayCollection
     */
    public function getNotificationReaders()
    {
        return $this->notification_readers;
    }

    /**
     * @param ArrayCollection $notification_readers
     */
    public function setNotificationReaders($notification_readers)
    {
        $this->notification_readers = $notification_readers;
    }

    public function addNotificationReaders(Collection $notifications)
    {
        /**
         * @var $notification NotificationReader
         */
        foreach ($notifications as $notification) {
            $notification->setRecipients($this);
            $this->notification_readers->add($notification);
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