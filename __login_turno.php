<?
session_start();
require_once("conexao.php");
require_once("funcoes.php");

if (isset($_POST["acao"])) {
	
	if ($_POST["id_turno"]!="") {
		$_SESSION["id_turno_sessao"]= $_POST["id_turno"];
		header("location: ./");
	}
	else header("location: index2.php?pagina=login_turno&erro=s1");
}
else {
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
			<form action="index2.php?pagina=login_turno" method="post" name="formLogin" id="formLogin" onSubmit="return validaFormNormal('validacoes');">
				<h2>Escolha o turno:</h2>
				<br />
                
				<input type="hidden" name="acao" id="acao" value="1" class="escondido" />
                <input type="hidden" name="validacoes" id="validacoes" value="id_turno@vazio" class="escondido" />
				
				<label>Usuário:</label>
				<?= pega_departamento($_SESSION["id_departamento_sessao"]); ?>
				<br /><br />
                
                <?
                //área suja
				if ($_SESSION["id_departamento_sessao"]=="2") {
				?>
                <label for="id_turno">Turno:</label>
                <select name="id_turno" id="id_turno" title="Turno">
                    <option value="">---</option>
                    <? 
                    $result= mysql_query("select * from rh_turnos
                                            where id_departamento = '". $_SESSION["id_departamento_sessao"] ."'
											and   status_turno = '1'
											and   fixo = '1'
                                            order by id_turno_index asc ");
                    $i=0;
                    while ($rs= mysql_fetch_object($result)) {
                        if (($i%2)==1) $classe= " class=\"cor_sim\"";
                        else $classe= " ";
                    ?>
                    <option <?=$classe;?> value="<?=$rs->id_turno;?>"><?= str_replace(" 1", "", $rs->turno);?></option>
                    <?
                        $i++;
                    }
					
					if (($i%2)==1) {
						$classe1= " class=\"cor_sim\"";
						$classe2= " ";
					}
                    else {
						$classe1= " ";
						$classe2= " class=\"cor_sim\"";
					}
                    ?>
                     
                    <option <?=$classe1;?> value="-1">PLANTÃO SÁBADO</option>
                    <option <?=$classe2;?> value="-2">PLANTÃO DOMINGO</option>
                    
                    <? if ($_SESSION["id_departamento_sessao"]=="1") { ?>
                    <option <?=$classe1;?> value="-3">COSTURA</option>
                    <? } ?>
                </select>
				<br /><br />
                
                <? } else { ?>
                
                <label for="id_turno">Modo:</label>
                <select name="id_turno" id="id_turno" title="Turno">
                    <option value="">---</option>
                    <option class="cor_sim" value="-4">PRODUÇÃO NORMAL</option>
                    <option value="-3">COSTURA</option>
                </select>
                <br /><br />
                <? } ?>
		
				<label for="enviar">&nbsp;</label>
				<button id="enviar" type="submit">Enviar</button>
				<br /><br />
				
				<label>&nbsp;</label>
                <span class="vermelho">
				<?
                if ($_GET["erro"]=="s1") echo "Selecione um turno!";
				?>
				</span>
			</form>
		</div>
	</div>
	
	<script type="text/javascript">
		daFoco("id_turno");
	</script>
</body>
</html>
<? } ?>