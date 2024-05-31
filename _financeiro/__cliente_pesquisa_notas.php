<?
require_once("conexao.php");
if (pode_algum("i12", $_SESSION["permissao"])) {
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	
	if ($_GET["id_pesquisa"]!="") $id_pesquisa= $_GET["id_pesquisa"];
	if ($_POST["id_pesquisa"]!="") $id_pesquisa= $_POST["id_pesquisa"];
	
	if ($_GET["id_pesquisa_nota"]!="") $id_pesquisa_nota= $_GET["id_pesquisa_nota"];
	if ($_POST["id_pesquisa_nota"]!="") $id_pesquisa_nota= $_POST["id_pesquisa_nota"];
	
	$result_pesquisa= mysql_query("select * from qual_pesquisa
									where id_cliente = '". $id_cliente ."'
									and   id_pesquisa = '". $id_pesquisa ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									") or die(mysql_error());
	$linhas_pesquisa= mysql_num_rows($result_pesquisa);
	$rs_pesquisa= mysql_fetch_object($result_pesquisa);
	
	$acao="i";
?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<div id="conteudo_interno">

    <form action="<?= AJAX_FORM; ?>formPesquisaNotas&amp;acao=<?= $acao; ?>" method="post" name="formPesquisaNotas" id="formPesquisaNotas" onsubmit="return ajaxForm('conteudo_interno', 'formPesquisaNotas', 'validacoes');">
    	
        <input class="escondido" type="hidden" id="validacoes" value="id_cliente@vazio" />
        
        <input type="hidden" class="escondido" name="id_cliente" id="id_cliente" value="<?=$id_cliente;?>" />
        
        <input type="hidden" class="escondido" name="id_pesquisa" id="id_pesquisa" value="<?=$id_pesquisa;?>" />
        
        <? if ($acao=="e") { ?>
        <input type="hidden" class="escondido" name="id_pesquisa_nota" id="id_pesquisa_nota" value="<?=$rs->id_pesquisa_nota;?>" />
        <? } ?>
        
        <fieldset>
            <legend>Dados da visita</legend>
            
            <div class="parte50">
            	<label>Cliente:</label>
				<?= pega_pessoa($rs_pesquisa->id_cliente); ?>
                <br />
                
                <label>Data:</label>
				<?= desformata_data($rs_pesquisa->data_pesquisa); ?>
                <br />
                
                <label>Responsável:</label>
				<?= $rs_pesquisa->responsavel; ?>
                <br />
                
                <label>Setor:</label>
				<?= pega_setor_cliente($rs_pesquisa->id_cliente_setor); ?>
                <br />
            
            </div>
            <div class="parte50">
            	<label>Observações:</label>
				<?= $rs_pesquisa->obs; ?>
                <br />
            </div>
            
        </fieldset>
        
        <fieldset>
            <legend>Pesquisa de satisfação</legend>
            
            <?
			$result_categoria= mysql_query("select * from qual_pesquisa_categorias
											where id_empresa = '". $_SESSION["id_empresa"] ."' 
											order by id_pesquisa_categoria asc
											") or die(mysql_error());
			
			while ($rs_categoria= mysql_fetch_object($result_categoria)) {
			?>
            
            <table cellspacing="0" width="100%">
            	<tr>
                	<th width="50%" align="left"><?= $rs_categoria->pesquisa_categoria; ?></th>
                    <th width="50%" align="left">Nota</th>
                </tr>
                <?
				$result_teste= mysql_query("select * from qual_pesquisa_notas
										   	where id_pesquisa = '". $id_pesquisa ."'
											");
				$linhas_teste= mysql_num_rows($result_teste);
				
				if ($linhas_teste>0)
					$sql_item= "select distinct(qual_pesquisa_itens.id_pesquisa_item), qual_pesquisa_itens.id_empresa, qual_pesquisa_itens.id_pesquisa_categoria,
												qual_pesquisa_itens.pesquisa_item
												from qual_pesquisa_itens, qual_pesquisa_notas
												where qual_pesquisa_itens.id_empresa = '". $_SESSION["id_empresa"] ."' 
												and   qual_pesquisa_itens.id_pesquisa_categoria = '". $rs_categoria->id_pesquisa_categoria ."'
												/* and   qual_pesquisa_itens.status_item = '1' */
												and   qual_pesquisa_itens.id_pesquisa_item = qual_pesquisa_notas.id_pesquisa_item
												order by qual_pesquisa_itens.id_pesquisa_item asc
												";
				else
					$sql_item= "select * from qual_pesquisa_itens
												where qual_pesquisa_itens.id_empresa = '". $_SESSION["id_empresa"] ."' 
												and   qual_pesquisa_itens.id_pesquisa_categoria = '". $rs_categoria->id_pesquisa_categoria ."'
												and   qual_pesquisa_itens.status_item = '1'
												order by qual_pesquisa_itens.id_pesquisa_item asc
												";
												
				$result_item= mysql_query($sql_item) or die(mysql_error());
				
				while ($rs_item= mysql_fetch_object($result_item)) {
					$result= mysql_query("select * from qual_pesquisa_notas
											where id_cliente = '". $id_cliente ."'
											and   id_pesquisa = '". $id_pesquisa ."'
											and   id_pesquisa_item = '". $rs_item->id_pesquisa_item ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					$linhas= mysql_num_rows($result);
					$rs= mysql_fetch_object($result);
				?>
                <tr>
                	<td><?= $rs_item->pesquisa_item;?></td>
                    <td>
                    	<input type="hidden" class="escondido" name="nada[]" value="1" />
                        
                    	<input type="hidden" class="escondido" name="id_pesquisa_item[]" value="<?=$rs_item->id_pesquisa_item;?>" />
                        
                    	<select class="tamanho35p" name="nota[]" >
                        <option value="">-</option>
                        <?
                        for ($i=-1; $i<11; $i++) {
                            $descricao_nota= pega_descricao_nota2($i);
                            $descricao_nota= explode("@", $descricao_nota);
							
							if ($i==-1) $nota_mostra= $descricao_nota[0];
							else $nota_mostra= $i . "- ". $descricao_nota[0];
                        ?>
                        <option class="<?= $descricao_nota[1]; ?>" <? if ($rs->nota==$i) echo "selected=\"selected\""; ?> value="<?=$i;?>"><?= $nota_mostra; ?></option>
                        <? } ?>
                    </select>
                    </td>
                </tr>
                <? } ?>
            </table>
            <br /><br />
            
            <? } ?>
            
        </fieldset>
        
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
        
    </form>
</div>

<? } ?>