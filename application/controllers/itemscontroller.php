<?php


class ItemsController extends Controller
{

    public function view($id = null, $name = null)
    {
        $this->set('title', $name . ' - My Todo List App');
        $this->set('todo', $this->_model->select($id));
    }

    function viewall() {
	$this->set('title', 'All Items - My Todo List App');
	$items = $this->_model->selectAll();
	$this->set('todo', $items);
    }
    

    public function add()
    {
            $todo = $_POST['todo'] ?? '';

	    $query = "INSERT INTO items (item_name) VALUES (?)";
	    $this->_model->query($query, [$todo], 0);
            $this->set('title', 'Success - My Todo List App');
            $this->set('todo', 'Item added successfully');
    }

    public function delete($id = null)
    {
	$query = "DELETE FROM items WHERE id = ?";
	$this->_model->query($query,[$id],0);

        $this->set('title', 'Success - My Todo List App');
        $this->set('todo', 'Item deleted successfully');
    }
}