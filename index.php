<?php
session_start();

require 'config.php';

$title = "Accueil - Biblioweb";
?>

<?php 
include BASE_URL.'/inc/header.inc.php';

?>
<div class="row">
	<h1 class="title">Présentation</h1>
	<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec vulputate sem velit, a dictum magna vehicula ut. In vitae porta leo, pretium tempor risus. Quisque tempus, felis eget luctus viverra, libero leo consectetur velit, ac venenatis risus arcu et dolor. Nam mi libero, maximus in metus quis, tempor sollicitudin leo. Nam condimentum orci vel risus consequat, eget bibendum felis dictum. Nam sit amet iaculis magna. Suspendisse purus ex, porta sit amet eros ut, luctus suscipit libero. In hac habitasse platea dictumst. Aliquam in dignissim nunc. In hac habitasse platea dictumst. Maecenas volutpat auctor ligula in pellentesque.</p>

	<p>Sed egestas lacinia nisl eu tincidunt. Phasellus vitae neque vehicula, fringilla augue sed, cursus urna. Mauris nunc nulla, ultrices eget neque et, consectetur posuere tortor. Morbi eget aliquet erat. Nunc sit amet consequat metus. Donec non nulla commodo, tempor velit at, interdum dolor. Nunc dictum orci metus, in lobortis nulla pulvinar at. Vivamus mattis auctor nunc, id sagittis nibh placerat cursus. Praesent tempus velit nec sapien tempor, at bibendum lacus tincidunt. Integer commodo vulputate nisi, sit amet tempor ante aliquam sit amet.</p>
</div>
<?php 
include BASE_URL.'/inc/footer.inc.php'; 
?>