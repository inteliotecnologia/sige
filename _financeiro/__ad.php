<?
require_once("conexao.php");
if (pode("aiz12", $_SESSION["permissao"])) {
	if ($_GET["esquema"]!="") $esquema= $_GET["esquema"];
	if ($_POST["esquema"]!="") $esquema= $_POST["esquema"];
	
	if ($_GET["tipo_pessoa"]!="") $tipo_pessoa= $_GET["tipo_pessoa"];
	if ($_POST["tipo_pessoa"]!="") $tipo_pessoa= $_POST["tipo_pessoa"];
	if ($tipo_pessoa=="") $tipo_pessoa="c";
	
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select * from  tr_clientes_ad, pessoas, pessoas_tipos
								where tr_clientes_ad.id_ad = '". $_GET["id_ad"] ."'
								and   tr_clientes_ad.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   tr_clientes_ad.id_cliente = pessoas.id_pessoa
								and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$tipo_pessoa= $rs->tipo_pessoa;
	}
?>

<? if ($esquema=="") { $div_alvo= "conteudo"; ?>
<h2>Contatos - <?= pega_tipo_pessoa($tipo_pessoa); ?></h2>
<? } else $div_alvo= "conteudo_interno"; ?>

<form action="<?= AJAX_FORM; ?>formAd&amp;acao=<?= $acao; ?>" method="post" name="formAd" id="formAd" onsubmit="return ajaxForm('<?=$div_alvo;?>', 'formAd', 'validacoes');">
    
    <? if ($acao=='e') { ?>
    <input name="id_ad" class="escondido" type="hidden" id="id_ad" value="<?= $rs->id_ad; ?>" />
    <? } ?>
    
    <input class="escondido" type="hidden" id="validacoes" value="id_cliente@vazio|nome@vazio|usuario@vazio" />
    <input class="escondido" type="hidden" name="esquema" value="<?=$esquema;?>" />
    <input class="escondido" type="hidden" name="tipo_pessoa" value="<?=$tipo_pessoa;?>" />
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            
            <label for="id_cliente"><?= pega_tipo_pessoa($tipo_pessoa); ?>:</label>
            <select name="id_cliente" id="id_cliente" title="Cliente">
				<? if ($acao=='i') { ?>
                <option value="">-</option>
                <? } ?>
				<?
                $result_ced= mysql_query("select *, pessoas.id_pessoa as id_cedente from pessoas, pessoas_tipos
                                            where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                            and   pessoas_tipos.tipo_pessoa = '". $tipo_pessoa ."'
                                            order by 
                                            pessoas.nome_rz asc
                                            ") or die(mysql_error());
                $k=0;
                while ($rs_ced = mysql_fetch_object($result_ced)) {
                ?>
                <option  <? if ($k%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_ced->id_cedente; ?>" <? if (($rs_ced->id_cedente==$rs->id_cliente) || ($rs_ced->id_cedente==$_GET["id_cliente"])) echo "selected=\"selected\""; ?>><?= $rs_ced->nome_rz; ?></option>
                <? $k++; } ?>
            </select>
            <br />
            
            <label for="nome">Nome:</label>
            <input title="Nome" name="nome" value="<?= $rs->nome; ?>" id="nome" />
            <br />
            
            <label for="cargo">Cargo:</label>
            <input title="Cargo" name="cargo" value="<?= $rs->cargo; ?>" id="cargo" />
            <br />
            
            <? /*
            <label for="usuario">Usuário:</label>
            <input title="Usuário" class="tamanho25p" name="usuario" value="<?= $rs->usuario; ?>" id="ad" />
            <br />
            */ ?>
        </div>
        <div class="parte50">
        	
            <label for="setor">Setor:</label>
            <input title="Setor" name="setor" value="<?= $rs->setor; ?>" id="setor" />
            <br />
            
            <label for="data_nasc">Data de nascimento:</label>
            <input title="Data de nascimento" name="data_nasc" value="<?= desformata_data($rs->data_nasc); ?>" id="data_nasc" onkeyup="formataData(this);" maxlength="10" />
            <br />
            
            <label for="email">E-mail:</label>
            <input title="E-mail" name="email" value="<?= $rs->email; ?>" id="email" />
            <br />
            
        	<? /*<? if ($acao=='e') { ?>
            <label class="tamanho300 alinhar_esquerda">Link para ativação (internet):</label><br />
            <input name="link" id="link" value="http://187.5.152.72/sige/index2.php?pagina=ad_ativa&amp;auth=<?= $rs->auth; ?>" onfocus="this.select();" />
            <br />
            
            <label class="tamanho300 alinhar_esquerda">Link para ativação (rede local):</label><br />
            <input name="link" id="link" value="http://192.168.1.20/sige/index2.php?pagina=ad_ativa&amp;auth=<?= $rs->auth; ?>" onfocus="this.select();" />
            <br /><br />
            
            <label class="alinhar_esquerda">Senha:</label><br />
            <? if ($rs->senha!="") echo "Senha cadastrada pelo IP <strong>". $rs->ip ."</strong> e pronta para uso."; else echo "Aguardando cadastramento da senha pelo cliente."; ?>
            <br /><br />
            
            <input type="checkbox" class="tamanho30" id="zerar_senha" name="zerar_senha" value="1" />
            <label for="zerar_senha" class="alinhar_esquerda nao_negrito tamanho300">Zerar a senha deste usuário.<br /> (libera o link acima para cadastro de nova senha).</label>
            <br />
            <? } */ ?>
        </div>
    </fieldset>
                
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>