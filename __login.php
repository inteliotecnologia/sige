<?
if (isset($_POST["acao"])) {
	require_once("conexao.php");
	require_once("funcoes.php");
	
	$usuario= str_replace("'", "xxx", str_replace('"', 'xxx', $_POST["usuario"]));
	$senha= str_replace("'", "xxx", str_replace('"', 'xxx', $_POST["senha"]));
	
	$result= mysql_query("select * from usuarios
							where usuario= '$usuario'
							and   senha= '". md5($senha) ."'
							and   status_usuario = '1'
							
							and   situacao = '1'
							") or die(mysql_error());
	
	if (mysql_num_rows($result)==0)
		header("location: ./index2.php?pagina=login&erro=s1");
	else {
		$rs= mysql_fetch_object($result);
		
		if ($rs->status_usuario==0)
			header("location: ./index2.php?pagina=login&erro=s2");
		else {
			session_start();
			
			if ($rs->tipo_usuario=='a') $permissao= "www";
			else $permissao= $rs->permissao;
			
			$_SESSION["id_empresa"]= $rs->id_empresa;
			$_SESSION["id_usuario"]= $rs->id_usuario;
			$_SESSION["tipo_usuario"]= $rs->tipo_usuario;
			
			if ($rs->id_funcionario!="0") {
				$_SESSION["nome"]= pega_nome_pelo_id_usuario($rs->id_usuario);
				$_SESSION["id_funcionario_sessao"]= $rs->id_funcionario;
				$redir= "./" ;
			}
			else {
				$_SESSION["nome"]= pega_departamento($rs->id_departamento);
				$_SESSION["id_departamento_sessao"]= $rs->id_departamento;
				$redir= "index2.php?pagina=login_turno";
			}
			
			$_SESSION["permissao"]= $permissao;
			$_SESSION["id_acesso"]= grava_acesso($rs->id_usuario, $rs->id_empresa, 'e', $_SERVER["REMOTE_ADDR"], gethostbyaddr($_SERVER["REMOTE_ADDR"]));
			
			setcookie ("usuario", $usuario, ((time()+3600)*24)*1000);
			
			alerta_documentos($_SESSION["id_empresa"]);
			alerta_aniversariantes($_SESSION["id_empresa"]);
			alerta_aniversariantes_clientes($_SESSION["id_empresa"]);
			
			header("location: ". $redir);
			//verifica_backup();
		}
		//header("location: ./");
	}//fim else
}
else {
	session_start();
	
	@session_unregister("id_usuario");
	@session_unregister("tipo_usuario");
	@session_unregister("nome");
	@session_unregister("permissao");
	
	if (isset($redireciona))
		echo
		"
		<script language='javascript' type='text/javascript'>
			window.top.location.href='index2.php?pagina=login';
		</script>
		";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title><? include("titulo.php"); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    
    <link href="estilo.css" rel="stylesheet" type="text/css" media="all" />
    <link rel="shortcut icon" href="images/icone.png" />
    
    <!--[if IE 6]>
		<link rel="stylesheet" type="text/css" media="screen" href="estilo_ie6.css" />
	<![endif]-->
    
    <script language="javascript" type="text/javascript" src="js/validacoes.js"></script>
</head>

<body onLoad="daFoco('usuario');">

	<noscript>
	  <meta http-equiv="Refresh" content="1; url=index2.php?pagina=erro" />
	</noscript>
	
	<div id="pagina_login">
		<div id="logo_login">
        </div>
		<div id="formulario_login">
			<form action="index2.php?pagina=login" method="post" name="formLogin" id="formLogin" onSubmit="return validaFormNormal('validacoes', 0, 1);">
				<h2>Fa&ccedil;a o login:</h2>
				<br />
                
				<input type="hidden" name="acao" id="acao" value="1" class="escondido" />
                <input type="hidden" name="validacoes" id="validacoes" value="usuario@vazio|senha@vazio" class="escondido" />
				
				<label for="usuario">Usu&aacute;rio:</label>
				<input name="usuario" id="usuario" title="Usuário" value="<?= $_COOKIE["usuario"]; ?>" />
				<br />
		
				<label for="senha">Senha:</label>
				<input  type="password" name="senha" id="senha" title="Senha" />
				<br /><br />
		
				<label for="enviar">&nbsp;</label>
				<button id="enviar" type="submit">Enviar</button>
				<br /><br />
				
				<label>&nbsp;</label>
                <span class="vermelho">
				<?
                if ($_GET["erro"]=="s1") echo "Usuário e/ou senha inválidos!";
				if ($_GET["erro"]=="s2") echo "Acesso desativado!";
				?>
				</span>
			</form>
		</div>
	</div>
	
	<? /*<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
	</script>
	<script type="text/javascript">
	_uacct = "UA-801754-7";
	urchinTracker();
	</script> */ ?>
</body>
</html>
<? } ?>