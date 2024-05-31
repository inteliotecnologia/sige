<?
require_once("conexao.php");
if (pode("ey", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	
	if ($_GET["cont"]!="") $cont= $_GET["cont"];
	if ($_POST["cont"]!="") $cont= $_POST["cont"];
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	
	if ($acao=='e') {
		$result= mysql_query("select * from tr_vistorias
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_vistoria = '". $_GET["id_vistoria"] ."'
								");
		$rs= mysql_fetch_object($result);
	}
?>

<form action="<?= AJAX_FORM; ?>formAdValida&amp;acao=<?= $acao; ?>" method="post" name="formAdValida" id="formAdValida" onsubmit="return ajaxForm('assinatura_digital_conteudo', 'formAdValida', 'validacoes_ad');">
                
    <input type="hidden" name="acao" id="acao" value="1" class="escondido" />
    <input type="hidden" name="cont" id="cont" value="<?=$cont;?>" class="escondido" />
    <input type="hidden" name="id_cliente" id="id_cliente" value="<?=$id_cliente;?>" class="escondido" />
    
    <input type="hidden" name="validacoes" id="validacoes_ad" value="usuario@vazio|senha_ad@vazio" class="escondido" />
    
    <label>Empresa:</label>
    <?= pega_pessoa($id_cliente); ?>
    <br />
    
    <label for="usuario">Usuário:</label>
    <input name="usuario" id="usuario" title="Usuário" />
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
    if ($erro_valida=="1") echo "Usuário e/ou senha inválidos!";
    ?>
    </span>
</form>

<script language="javascript">
	daFoco("usuario");
</script>
<? } ?>