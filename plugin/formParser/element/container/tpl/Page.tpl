<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=no;"/>
		<title><?php $tpl->TITLE; ?></title>
		
		<!-- CSS -->
		
<!--		<link rel="stylesheet" type="text/css" media="screen" href="css/reset.css" />-->
		<link rel="stylesheet" type="text/css" media="screen" href="/form/css/flex.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="/form/css/theme/default.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="/form/css/theme/custom.css" />
	</head>
	<body>
		
		<!-- Form -->
		
		<form id="fb-form" method="post" action="">
			
			<!-- Page Container -->
			
			<?php $tpl->CHILDREN; ?>
			
			<!-- Other Dialogs -->
			
			<?php $tpl->DIALOGS; ?>
			
		</form>
		
		<!-- Libraries -->
		
		<script type="text/javascript" src="/form/js/thirdparty/PWT/PWT-lite-mini.js"></script>
		<script type="text/javascript" src="/form/js/thirdparty/PWT/PWT-When.js"></script>
		<script type="text/javascript" src="/form/js/thirdparty/jquery/jquery.1.6.1.min.js"></script>
		
		<!-- Main Script -->
		
		<script type="text/javascript" src="form/js/fb/Application.js"></script>
	</body>
</html>