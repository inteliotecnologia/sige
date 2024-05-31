<?
session_start();
//$_SESSION["id_usuario"]= -1;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title><? include("titulo.php"); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilo_ponto.css" rel="stylesheet" type="text/css" media="screen" />
    <link rel="shortcut icon" href="images/icone.png" />
    
    <!--[if IE 6]>
		<link rel="stylesheet" type="text/css" media="screen" href="estilo_ie6.css" />
	<![endif]-->
    
    <script language="javascript" type="text/javascript" src="js/validacoes.js"></script>
    <script language="javascript" type="text/javascript" src="js/ajax.js"></script>
    <script language="javascript" type="text/javascript" src="js/relogio.js"></script>
</head>
<body>
	<script type="text/javascript" language="javascript">
		//setInterval("buscaTempo()", 1000);
		resetaTelaPonto();
	</script>
    
	<div id="corpo" onClick="daFoco('cartao');">
		<div id="logo"></div>
		<div id="relogio"></div>
        
        <div id="corpo_ponto"></div>
	</div>
    <script type="text/javascript" language="javascript">
		buscaTempo();
		setInterval("buscaTempo()", 10000);
		//iniciaRelogio();
	</script>
</body>
</html>
