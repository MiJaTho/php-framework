<?php declare(strict_types=1);

require_once PATH . 'Core/Controller.php';
require_once PATH . 'Models/PostModel.php';

class PostController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->model = new PostModel($this->db);
    }

    public function index() :string
    {
       $this->data['posts'] = $this->model->getPosts($this->user);
        return $this->render('location.twig');
    }

    public function store() : string
    {
        $params = $this->request->params();

       

        $success = $this->model->createPost(
            $this->user,
            $params->get('title'),
            $params->get('content')
        );

        if ($success) {
            $this->data['messages']['oks'][] = "Your post has been published.";
        }
        else {
            $this->data['messages']['errors'][] = "Sorry, we couldn't publish your post.";
        }

        return $this->index();
    }



    
}
