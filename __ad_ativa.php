<?
if (isset($_POST["acao"])) {
	require_once("conexao.php");
	require_once("funcoes.php");
	
	$senha= str_replace("'", "xxx", str_replace('"', 'xxx', $_POST["senha_ad"]));
	
	$result= mysql_query("update tr_clientes_ad
						 	set  senha= '". md5($senha) ."',
							ip= '". $_SERVER["REMOTE_ADDR"] ."'
							where id_ad= '". $_POST["id_ad"] ."'
							and   auth = '". $_POST["auth"] ."'
							and   status_usuario = '1'
							and   situacao = '1'
							") or die(mysql_error());
	
	if ($result) header("location: ./index2.php?pagina=ad_ativa&auth=". $_POST["auth"] ."");

}
else {
	session_start();
	require_once("conexao.php");
	require_once("funcoes.php");
	
	$result= mysql_query("select * from tr_clientes_ad
						 	where auth= '". $_GET["auth"] ."'
							and   situacao = '1'
							") or die(mysql_error());
	
	$rs= mysql_fetch_object($result);
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
			<? if (mysql_num_rows($result)==0) { ?>
            <h2>Erro:</h2>
            
            <p>Link inválido. Entre em contato com o administrador do sistema.</p>
            
            <?
            }
			else {
			?>
            
            <h2>Cadastre sua senha:</h2>
			<br />
            
            <?
            if ($rs->senha!="") echo "<p>Sua senha está cadastrada e ativa, qualquer problema, entre em contato com o administrador do sistema.</p>";
			else {
			?>
            
            <form action="index2.php?pagina=ad_ativa" method="post" name="formAdAtiva" id="formAdAtiva" onSubmit="return validaFormNormal('validacoes');">
                
				<input type="hidden" name="acao" id="acao" value="1" class="escondido" />
                <input type="hidden" name="id_ad" id="id_ad" value="<?=$rs->id_ad;?>" class="escondido" />
                <input type="hidden" name="auth" id="auth" value="<?=$rs->auth;?>" class="escondido" />
                
                <input type="hidden" name="validacoes" id="validacoes" value="senha_ad@vazio" class="escondido" />
				
				<label>Empresa:</label>
				<?= pega_pessoa($rs->id_cliente); ?>
				<br />
                
                <label>Usuário:</label>
				<?= $rs->usuario; ?>
				<br />
		
				<label for="senha_ad">Senha:</label>
				<input  type="password" name="senha_ad" id="senha_ad" title="Senha" />
				<br /><br />
		
				<label for="enviar">&nbsp;</label>
				<button id="enviar" type="submit">Enviar</button>
				<br /><br />
				
				<label>&nbsp;</label>
                <span class="vermelho">
				<?
                if ($erro=="s1") echo "Usuário e/ou senha inválidos!";
				if ($erro=="s2") echo "Acesso desativado!";
				?>
				</span>
			</form>
            <? } } ?>
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