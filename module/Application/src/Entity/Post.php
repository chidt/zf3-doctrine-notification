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

/**
 * This class represents a single post in a blog
 * @ORM\Entity(repositoryClass= "\Application\Repository\PostRepository")
 * @ORM\Table(name="post")
 */

class Post
{
    //Post status constants.
    const STATUS_DRAFT = 1; //Draft.
    const STATUS_PUBLISHED = 2; //Published.

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id")
     */
    protected $id;

    /**
     * @ORM\Column(name="title")
     */
    protected $title;

    /**
     * @ORM\Column(name="content")
     */
    protected $content;

    /**
     * @ORM\Column(name="status")
     */
    protected $status;

    /**
     * @ORM\Column(name="date_created")
     */
    protected $dateCreated;

    /**
     * @ORM\OneToMany(targetEntity="\Application\Entity\Comment", mappedBy="post")
     * @ORM\JoinColumn(name="id", referencedColumnName="post_id")
     */
    protected $comments;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    protected $author;

    /**
     * @ORM\ManyToMany(targetEntity="\Application\Entity\Tag", inversedBy="posts")
     * @ORM\JoinTable(name="post_tag",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     */
    protected $tags;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * Returns comments for this post.
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Adds a new comment to this post.
     * @param $comment
     */
    public function addComment($comment)
    {
        $this->comments[] = $comment;
    }

    // Returns tags for this post.
    public function getTags()
    {
        return $this->tags;
    }

    // Adds a new tag to this post.
    public function addTag($tag)
    {
        $this->tags[] = $tag;
    }

    // Removes association between this post and the given tag.
    public function removeTagAssociation($tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @var $author User
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        $author->addPost($this);
    }


}