	<form action="../items/add" method="post">
		<input type="text" value="I have to..." onclick="this.value=''" name="todo"> <input type="submit" value="add">
	</form>
	<br /><br />
	<?php $number = 0 ?>

	<?php if (!isset($todo) || !is_array($todo) || count($todo) === 0): ?>
    <h2>No Items</h2>
<?php else: ?>
    <?php foreach ($todo as $todoitem): ?>
        <a class="big" href="../items/view/<?php echo $todoitem['id']; ?>/<?php echo strtolower(str_replace(" ", "-", $todoitem['item_name'])) ?>">
            <span class="item">
                <?php echo ++$number ?>
                <?php echo htmlspecialchars($todoitem['item_name']) ?>
            </span>
        </a><br />
    <?php endforeach; ?>
<?php endif; ?>
