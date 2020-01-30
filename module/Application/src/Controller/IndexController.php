<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Service\PostManager;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity\Post;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /**
     * Entity manager doctrine
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Post manager.
     * @var PostManager
     */
    private $postManager;

    /**
     * IndexController constructor.
     * @param EntityManager $entityManager
     * @param PostManager $postManager
     */
    public function __construct(EntityManager $entityManager, PostManager $postManager)
    {
        $this->entityManager = $entityManager;
        $this->postManager = $postManager;
    }


    public function indexAction()
    {
        $tagFiler = $this->params()->fromQuery('tag', null);

        if ($tagFiler) {

            // Filter posts by tag
            $posts = $this->entityManager->getRepository(Post::class)
                ->findBy(['status' => Post::STATUS_PUBLISHED],['dateCreated' => 'DESC']);
        } else {
            // Get recent posts
            $posts = $this->entityManager->getRepository(Post::class)
                ->findBy(['status'=>Post::STATUS_PUBLISHED],
                    ['dateCreated'=>'DESC']);
        }

        // Get popular tags.
        $tagCloud = $this->postManager->getTagCloud();

        // Render the view tags
        return new ViewModel([
            'posts' => $posts,
            'postManager' => $this->postManager,
            'tagCloud' => $tagCloud
        ]);
    }
}
