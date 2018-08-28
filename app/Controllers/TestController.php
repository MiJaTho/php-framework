<?php declare(strict_types=1);

require_once PATH . 'Core/Controller.php';
require_once PATH . 'Models/TestModel.php';

class TestController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->model = new TestModel($this->db);
    }

    public function index($id = null)
    {
        $this->data['tests'] = $this->model->getTestData();
        $this->data['messages']['notices'][] = "Test Notice";
        $this->data['messages']['errors'][] = "Test Error";
        $this->data['messages']['oks'][] = "Test Success";
        $this->data['id'] = $id;
        return $this->render('test.twig');
    }

    public function cookie() {
        var_dump($_POST);
        
        return '';
    }
}
