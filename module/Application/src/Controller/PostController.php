<?php
/**
 * Created by PhpStorm.
 * User: CHI-DT
 * Date: 17/07/2018
 * Time: 16:41
 */

namespace Application\Controller;


use Application\Entity\Notification;
use Application\Entity\Post;
use Application\Entity\User;
use Application\Form\CommentForm;
use Application\Form\PostForm;
use Application\Service\PostManager;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use \ZMQContext;
use \ZMQ;
use Zend\View\View;

class PostController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Post manager.
     * @var PostManager
     */
    private $postManager;

    /**
     * PostController constructor.
     * @param EntityManager $entityManager
     * @param PostManager $postManager
     */
    public function __construct(EntityManager $entityManager, PostManager $postManager)
    {
        $this->entityManager = $entityManager;
        $this->postManager = $postManager;
    }

    /**
     *  This action displays the "View Post" page allowing to see the post title
     *  and content. The page also contains a form allowing
     *  to add a comment to post.
     */
    public function viewAction()
    {

        $postId = $this->params()->fromRoute('id', -1);
        /**
         * @var $post Post
         */
        $post = $this->entityManager->getRepository(Post::class)
            ->findOneById($postId);

        if ($post == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $commentCount = $this->postManager->getCommentCountStr($post);

        // Create the comment form.
        $form = new CommentForm();

        // Check whether this post is a POST request.
        if ($this->getRequest()->isPost()) {

            // Get POST data.
            $data = $this->params()->fromPost();

            // Fill form with data.
            $form->setData($data);
            if ($form->isValid()) {

                // Get validated form data.
                $data = $form->getData();

                // Use post manager service to add new comment to post.
                /**
                 * @var $author User
                 */
                $author = $this->identity();

                $this->postManager->addCommentToPost($post, $data, $author);

                $notification = new Notification();
                $notification->setType(Notification::TYPE_POST_COMMENT);
                $post_author = $post->getAuthor();
                $notification->setNotificationReaders($post_author);

                $entryData = [
                    'author' => $author->getName(),
                    'comment' => $data['comment'],
                    'post_id' => $post->getId(),
                    'recipients' => $post_author->getId()
                ];

                $notification->setParameters($entryData);

                $this->entityManager->persist($notification);
                $this->entityManager->flush();

                $context = new \ZMQContext();
                $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
                $socket->connect('tcp://localhost:5555');
                $socket->send(json_encode($entryData));
                // Redirect to user again to "view" page
                return $this->redirect()->toRoute('posts', ['action' => 'view', 'id' => $postId]);
            }
        }

        // Render the view template.
        return new ViewModel([
            'post' => $post,
            'commentCount' => $commentCount,
            'form' => $form,
            'postManager' => $this->postManager
        ]);
    }

    /**
     *  This action display the "New Post" page. The page contains
     * a form allowing to enter post title, content and tags. When
     * the user clicks the Submit button, a new Post entity
     * will be created.
     */
    public function addAction()
    {

        // Create the form.
        $form = new PostForm();

        // Check whether this post is a POST request.
        if ($this->getRequest()->isPost()) {

            // Get POST data.
            $data = $this->params()->fromPost();

            // Fill form with data
            $form->setData($data);
            if ($form->isValid()) {

                // Get validated form data.
                $data = $form->getData();

                // Use post manager service to add new post to database
                $this->postManager->addNewPost($data);

                // Redirect the user to "index" page.
                return $this->redirect()->toRoute('application');
            }
        }

        // Render the view template
        return new ViewModel([
            'form' => $form
        ]);
    }

    /*
     * This action displays the page allowing to edit a post.
     */
    public function editAction()
    {
        // Create the form.
        $form = new PostForm();

        // Get post ID
        $postId = $this->params()->fromRoute('id', -1);

        // Find existing post in the database.
        /**
         * @var Post $post
         */
        $post = $this->entityManager->getRepository(Post::class)->findOneById($postId);

        if ($post == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Check whether this post is a POST request.
        if ($this->getRequest()->isPost()) {

            // Get POST data.
            $data = $this->params()->fromPost();

            //Fill form with data.
            $form->setData($data);
            if ($form->isValid()) {

                // Get validated form data.
                $data = $form->getData();

                // Use post manager service to add new post to database.
                $this->postManager->updatePost($post, $data);

                // Redirect to user to "index" page.
                return $this->redirect()->toRoute('application');
            }
        } else {
            $data = [
                'title' => $post->getTitle(),
                'content' => $post->getContent(),
                'tags' => $this->postManager->convertTagsToString($post),
                'status' => $post->getStatus()
            ];

            $form->setData($data);
        }

        return new ViewModel([
            'form' => $form,
            'post' => $post
        ]);
    }

    /**
     *  This "delete" action displays the Delete Post page
     */
    public function deleteAction()
    {
        $postId = $this->params()->fromRoute('id', -1);
        $post = $this->entityManager->getRepository(Post::class)->findOneById($postId);
        if ($post == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        $this->postManager->removePost($post);

        // Redirect user to index page
        return $this->redirect()->toRoute('posts', ['action' => 'admin']);
    }

    /**
     * This "admin" action displays the manager posts page. This page contains
     * the list of posts with an ability to edit/delete any post.
     */
    public function adminAction()
    {
        // Get posts
        $posts = $this->entityManager->getRepository(Post::class)->findBy([], ['dateCreated' => 'DESC']);

        // Render the view template
        return new ViewModel([
            'posts' => $posts,
            'postManager' => $this->postManager
        ]);
    }
}