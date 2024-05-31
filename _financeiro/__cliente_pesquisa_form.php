<?
require_once("conexao.php");
if (pode("i12", $_SESSION["permissao"])) {
	if ($_GET["id_pesquisa"]!="") $id_pesquisa= $_GET["id_pesquisa"];
	if ($_POST["id_pesquisa"]!="") $id_pesquisa= $_POST["id_pesquisa"];
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	
	if ($acao=="") $acao= $_GET["acao"];

	if ($acao=="e") {
		$result= mysql_query("select * from qual_pesquisa
							 	where id_pesquisa= '". $id_pesquisa ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$id_cliente= $rs->id_cliente;
	}
?>
<form action="<?= AJAX_FORM; ?>formVisitaPesquisa&amp;acao=<?= $acao; ?>" method="post" name="formVisitaPesquisa" id="formVisitaPesquisa" onsubmit="return ajaxForm('conteudo_interno', 'formVisitaPesquisa', 'validacoes_pesquisa', true);">

    <input class="escondido" type="hidden" id="validacoes_pesquisa" value="id_cliente@vazio" />
    
    <input name="id_cliente" class="escondido" type="hidden" id="id_cliente" value="<?= $id_cliente; ?>" />
    
    <? if ($acao=="e") { ?>
    <input name="id_pesquisa" class="escondido" type="hidden" id="id_pesquisa" value="<?= $rs->id_pesquisa; ?>" />
    <? } ?>
    
    <div class="parte50">
        <label for="id_cliente">Cliente:</label>
        <select name="id_cliente" id="id_cliente" title="Cliente">
            <? if ($acao=='i') { ?>
            <option value="">-</option>
            <? } ?>
            <?
            $result_ced= mysql_query("select *, pessoas.id_pessoa as id_cedente from pessoas, pessoas_tipos
                                        where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                        and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                        and   pessoas_tipos.tipo_pessoa = 'c'
                                        order by 
                                        pessoas.apelido_fantasia asc
                                        ") or die(mysql_error());
            $k=0;
            while ($rs_ced = mysql_fetch_object($result_ced)) {
            ?>
            <option  <? if ($k%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_ced->id_cedente; ?>" <? if (($rs_ced->id_cedente==$rs->reclamacao_id_cliente) || ($rs_ced->id_cedente==$id_cliente)) echo "selected=\"selected\""; ?>><?= $rs_ced->apelido_fantasia; ?></option>
            <? $k++; } ?>
        </select>
        <br />
    	
        <?
		if ($acao=="i") {
			$data_pesquisa= date("d/m/Y");
			$hora_pesquisa= date("H:i:s");
		}
		else {
			$data_pesquisa= desformata_data($rs->data_pesquisa);
			$hora_pesquisa= $rs->hora_pesquisa;
		}
		?>
        
        <label for="data_pesquisa">Data:</label>
        <input id="data_pesquisa" name="data_pesquisa" class="tamanho15p espaco_dir" value="<?= $data_pesquisa; ?>" title="Data" onkeyup="formataData(this);" maxlength="10" />
        <br />
        
        <label for="duracao">Duração:</label>
        <input id="duracao" name="duracao" class="tamanho15p espaco_dir" value="<?= substr($rs->duracao, 0, 5); ?>" title="Duração" onkeyup="formataHora(this);" maxlength="5" />
        <br /><br />
        
        <label for="responsavel">Responsável:</label>
        <input id="responsavel" name="responsavel" value="<?= $rs->responsavel; ?>" title="Responsável" />
        <br />
        
        <label for="id_cliente_setor">Setor:</label>
        <select name="id_cliente_setor" id="id_cliente_setor" title="Setor">
            <? if ($acao=='i') { ?>
            <option value="">-</option>
            <? } ?>
            <?
            $result_setor= mysql_query("select * from fi_clientes_setores
                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   id_cliente = '". $id_cliente ."'
                                            order by setor asc
                                            ") or die(mysql_error());
            $k=0;
            while ($rs_setor = mysql_fetch_object($result_setor)) {
            ?>
            <option  <? if ($k%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_setor->id_cliente_setor; ?>" <? if ($rs_setor->id_cliente_setor==$rs->id_cliente_setor) echo "selected=\"selected\""; ?>><?= $rs_setor->setor; ?></option>
            <? $k++; } ?>
        </select>
        <br /><br />
        
        <label for="obs">Observações:</label>
        <textarea name="obs" id="obs" class="altura80"><?=$rs->obs;?></textarea>
        <br />
	</div>
    <div class="parte50">
        
        <label for="pontos_positivos">Pontos positivos:</label>
        <textarea name="pontos_positivos" id="pontos_positivos" class="altura80"><?=$rs->pontos_positivos;?></textarea>
        <br />
        
        <label for="pontos_negativos">Pontos negativos:</label>
        <textarea name="pontos_negativos" id="pontos_negativos" class="altura80"><?=$rs->pontos_negativos;?></textarea>
        <br />
        
        <label for="criticas">Críticas:</label>
        <textarea name="criticas" id="criticas" class="altura80"><?=$rs->criticas;?></textarea>
        <br />
	</div>
    <br /><br />
    
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
</form>
<? } ?>