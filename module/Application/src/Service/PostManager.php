<?php
/**
 * Created by PhpStorm.
 * User: CHI-DT
 * Date: 7/11/2018
 * Time: 10:13 AM
 */

namespace Application\Service;


use Application\Entity\Comment;
use Application\Entity\Post;
use Application\Entity\Tag;
use Doctrine\ORM\EntityManager;
use Zend\Filter\StaticFilter;

class PostManager
{
    /**
     * Entity manager.
     * @var $entityManager  EntityManager
     */
    private $entityManager;

    /**
     * PostManager constructor.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /*
     * This method adds a new post.
     */
    public function addNewPost($data)
    {
        // Create new Post entity.
        $post = new Post();
        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        $post->setStatus($data['status']);
        $currentDate = date('Y-m-d H:i:s');
        $post->setDateCreated($currentDate);

        // Add the entity to entity manager.
        $this->entityManager->persist($post);

        // Add tags to post
        $this->addTagsToPost($data['tags'], $post);

        // Apply changes to database.
        $this->entityManager->flush();
    }

    /**
     * This method allows to update data of a single post.
     * @var $post Post
     */
    public function updatePost($post, $data)
    {
        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        $post->setStatus($data['status']);

        // Add tags to post
        $this->addTagsToPost($data['tags'], $post);

        // Apply changes to database
        $this->entityManager->flush();
    }

    /**
     * @param $tagsStr
     * @param $post Post
     */
    private function addTagsToPost($tagsStr, $post)
    {
        // Remove tag associations
        $tags = $post->getTags();
        foreach ($tags as $tag) {
            $post->removeTagAssociation($tag);
        }

        // Add tags to post
        $tags = explode(',', $tagsStr);

        foreach ($tags as $tagName) {
            $tagName = StaticFilter::execute($tagName, 'StringTrim');
            if (empty($tagName)) {
                continue;
            }

            $tag = $this->entityManager->getRepository(Tag::class)
                ->findOneByName($tagName);
            if ($tag == null) {
                $tag = new Tag();
            }

            $tag->setName($tagName);
            $tag->addPost($post);
            $this->entityManager->persist($tag);
            $post->addTag($tag);
        }
    }

    /**
     * Returns status as a string
     * @var $post Post
     */
    public function getPostStatusAsString($post)
    {
        switch ($post->getStatus()) {
            case Post::STATUS_DRAFT :
                return 'Draft';
            case Post::STATUS_PUBLISHED :
                return 'Published';
        }

        return 'Unknown';
    }

    /**
     * @param $post Post
     */
    public function convertTagsToString($post)
    {
        $tags = $post->getTags();
        $tagCount = count($tags);
        $tagsStr = '';
        $i = 0;
        /**
         * @var $tag Tag
         */
        foreach ($tags as $tag) {
            $i++;
            $tagsStr .= $tag->getName();
            if ($i < $tagCount)
                $tagsStr .= ', ';
        }

        return $tagsStr;
    }

    /**
     * @param $post Post
     */
    public function getCommentCountStr($post)
    {
        $commentCount = count($post->getComments());
        if ($commentCount == 0)
            return 'No comments';
        else if ($commentCount == 1)
            return '1 comment';
        else
            return $commentCount . ' comments';
    }

    /**
     * @param $post Post
     * @param $data
     */
    public function addCommentToPost($post, $data,$author)
    {
        // Create new Comment entity.
        $comment = new Comment();
        $comment->setPost($post);
        $comment->setAuthor($author);
        $comment->setContent($data['comment']);
        $currentDate = date('Y-m-d H:i:s');
        $comment->setDateCreated($currentDate);

        // Add the entity to entity manager
        $this->entityManager->persist($comment);

        //Apply change
        $this->entityManager->flush();
    }

    /**
     * @param $post Post
     */
    public function removePost($post)
    {
        // Remove associated comments
        $comments = $post->getComments();
        foreach ($comments as $comment) {
            $this->entityManager->remove($comment);
        }

        // Remove tag associations (if any)
        $tags = $post->getTags();
        foreach ($tags as $tag) {
            $post->removeTagAssociation($tag);
        }

        $this->entityManager->remove($post);

        $this->entityManager->flush();
    }

    /**
     *  Calculates frequencies of tag usage.
     */
    public function getTagCloud()
    {
        $tagCloud = [];

        $posts = $this->entityManager->getRepository(Post::class)->findPostHavingAnyTag();
        $totalPostCount = count($posts);

        $tags = $this->entityManager->getRepository(Tag::class)->findAll();

        /**
         * @var $tag Tag
         */
        foreach ($tags as $tag) {
            $postByTag = $this->entityManager->getRepository(Post::class)
                ->findPostsByTag($tag->getName());
            $postCount = count($postByTag);
            if ($postCount > 0) {
                $tagCloud[$tag->getName()] = $postCount;
            }
        }

        $normalizedTagCould = [];

        // Normalize
        foreach ($tagCloud as $name => $postCount) {
            $normalizedTagCould[$name] = $postCount / $totalPostCount;
        }

        return $normalizedTagCould;
    }

}