<html>
<head>
	<title>Список всех задач</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script type="text/javascript">
		$(document).ready( function () {
			$('select').on('change', function () {
				selected = $("#pager option:selected").text();
				window.location.href = "/beejee/?page="+selected+"&sort_by=<?= $sortBy ?>";
			})
			$('.sort_link').on('click', function() {
				clicked = $(this).attr('id');
				window.location.href = "/beejee/?page=<?= $page ?>&sort_by="+clicked;
			}) 
		})


	</script>
</head>
<body>
	<h1>Список всех задач</h1>
	<div class="login_block">
		<?php if ($userLoggedIn)  { ?>
			<a href="/beejee/logout">Разлогиниться</a>
		<?php } else {  ?>
			<a href="/beejee/login">Залогиниться</a>
		<?php } ?>
	</div>
	<div class="page_selector">
		<select id="pager">
			<?php 
			for ($i=1;$i<=$pagesCount;$i++) {
				?>
				<option <?php if ($i == $page) {echo "selected";} ?>>
					<?= $i ?>
				</option>
			<?php } ?>
		</select>
	</div>
	<div class="error_code">
		<?php 
		Helper::errorMessage();
		?>
	</div>
	<div class="notification">
		<?php 
		Helper::notificationMessage();
		?>
	</div>
	<table>
		<b><tr>
			<td class="sort_link" id="id">ID задачи</td>
			<td class="sort_link" id="user_name">Имя пользователя</td>
			<td class="sort_link" id="user_email">Email</вtd>
			<td id="text">Текст задачи</td>
			<td class="sort_link" id="completed">Выполнено?</td>
			<td class="" id="edited">Редактировано админом?</td>
		</tr></b>
		<?php foreach ($tasksToShow as $task) {?>
			

			<tr>
				<td><?= $task['id'] ?><a href="edit?id=<?=$task['id']?>"  class="small_link">Редактировать</a></td>
				<td><?= $task['user_name'] ?></td>
				<td><?= $task['user_email'] ?></td>
				<td><?= $task['text'] ?></td>
				<td><input type="checkbox" name='completed' disabled <?php if ($task['completed']) {echo "checked";}?> > </td>
				<td><input type="checkbox" disabled name='edited' <?php if ($task['edited']) {echo "checked";} ?> > </td>
			</tr>

		<?php } ?>

	</table>

	<form action="add" method="POST" >
		<input type="text" name="user_name" placeholder="Имя пользователя">
		<input type="text" name="user_email" placeholder="E-mail пользователя">
		<input type="text" name="text" placeholder="Текст задачи">
		<input type="submit" name="submit" value="Добавить задачу">
	</form>
</body>
</html>