<?php
    if (!isset($nodirect)) die('nope');
?>

<form class="page-small">
    <h2>
        <img src="<?php echo PATH_IMG; ?>key.svg" alt="">
        Registration Page
    </h2>
    <input type="text" name="user" placeholder="Username / Email">
    <input type="password" name="pass" placeholder="Password">
    <button type="submit">
        Login
    </button>
</form>
