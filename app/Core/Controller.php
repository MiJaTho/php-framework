<?php declare(strict_types=1);

require_once PATH . 'Core/Db.php';
require_once PATH . 'Core/Request.php';

abstract class Controller
{
    /**
     * The current Request object
     *
     * @var Request
     */
    protected $request = null;

    /**
     * The database connection (in our case: mysqli)
     *
     * @var mixed
     */
    protected $db = null;

    /**
     * The primary Model object of the controller
     *
     * @var Model
     */
    protected $model = null;

    /**
     * The view. This is an instance of the template engine.
     *
     * @var mixed
     */
    protected $view = null;


    /**
     * The id of the logged in user
     *
     * @var string
     */
    protected $csrfToken = '';


    /**
     * The data array for the template engine
     *
     * @var array
     */
    protected $data = [];

    /**
     * The id of the logged in user
     *
     * @var int
     */
    protected $user = null;

    /**
     * Create a new Controller object
     *
     * @param Request The Request the controller is supposed to act on
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

        // getInstance umbenennen zu getConnection
        $this->db = Db::getInstance();

        $this->initializeTemplateEngine();

        $this->handleCsrfToken();

        $this->prepareViewData();
    }


    /**
     * Store the current CSRF-Token in the Session
     * before destroying the Controller object
     */
    public function __destruct() {
        if (isset($this->csrf_token)) {
            $this->request->session()->set('csrf_token', $this->csrf_token);
        }
    }


    /**
     * Set the currently logged in user's id
     */
    public function setUser(int $id)
    {
        $this->user = $id;
        $this->data['logged_in'] = true;
    }


    /**
     * A wrapper for the template engine's render function
     *
     * @return string The rendered HTML
     */
    public function render(string $template) : string
    {
        return $this->view->render($template, $this->data);
    }

    
    /**
     * Returns a hidden input field with the current CSRF Token
     *
     * @return string CSRF hidden input field
     */
    public function csrfField() : string
    {
        return '<input type="hidden" name="csrf_token" value="' . $this->csrfToken . '">';
    }

    
    /**
     * Initialize the Twig Loader and Environment
     */
    protected function initializeTemplateEngine()
    {
        $loader = new Twig_Loader_Filesystem(
            PATH . 'Templates'
        );

        $this->view = new Twig_Environment($loader);
    }


    /**
     * For POST-Requests, validate the CSRF-Token and generate a new Token
     *
     * @throws Exception CSRF Token error
     */
    protected function handleCsrfToken() {
        // TODO: Maybe this should be:
        // if (in_array($this->request->method(), ['POST', 'PUT', 'DELETE'])
        // ALSO maybe ['POST', 'PUT', 'DELETE'] should be a protected property
        // that we can overide in child classes
        if ($this->request->is('POST') && !$this->validateCsrfToken()) {
            throw new Exception("CSRF Token error.");
        }

        $this->csrfToken = $this->generateAndSaveCsrfToken();
    }


    /**
     * Validates the CSRF-Token in the form against the one
     * which is stored in the session
     *
     * @return bool Did the token validate?
     */
    protected function validateCsrfToken() : bool
    {
        $sessionToken = $this->request->session()->get('csrf_token');
        $requestToken = $this->request->params()->get('csrf_token');

        if ($sessionToken && $requestToken && $sessionToken === $requestToken) {
            return true;
        }

        return false;
    }

    /**
     * Generates a new CSRF-Token and stores it in the session
     *
     * @return string The newly created CSRF-Token
     */
    protected function generateAndSaveCsrfToken()
    {
        $token = md5(uniqid(microtime(), true));
        $this->request->session()->set('csrf_token', $token);
        return $token;
    }

    /**
     * Adds the basePath and csrf_field to the view's data array
     * TODO: Mixed studlyCase and snake_case. Make this consistent!
     *
     * @return string The newly created CSRF-Token
     */
    protected function prepareViewData() {
        $this->data['basePath'] = $this->request->basePath();
        $this->data['csrf_field'] = $this->csrfField($this->csrfToken);
    }
}