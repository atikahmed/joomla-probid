<?php
/**
**/
?>


<?php
if (($this->error->getCode()) == '404') {
	
	header('Location: /page-not-found');
	exit;
}

elseif (($this->error->getCode()) == '403') {
	
	header('Location: /restricted-access');
	exit;
}
else {
	
	header('Location: /general-error');
	exit;
}
	
	
?>