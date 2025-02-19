<?php
class Controller
{
    protected $_model;
    protected $_controller;
    protected $_action;
    protected $_template;

    public function __construct(string $model, string $controller, string $action)
    {
        $this->_controller = $controller;
        $this->_action = $action;
        $this->_model = new $model(); // Directly instantiate and store it
        $this->_template = new Template($controller, $action); //Item items
    }

    public function set(string $name, mixed $value): void
    {
        $this->_template->set($name, $value);
    }

    public function __destruct()
    {
        $this->_template->render();
    }
}
