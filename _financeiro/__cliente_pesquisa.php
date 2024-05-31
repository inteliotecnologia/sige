<?
require_once("conexao.php");
if (pode("i12", $_SESSION["permissao"])) {
	$acao= 'i'; //$_GET["acao"];
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<div id="conteudo_interno">

    <fieldset>
    	<legend>Pesquisa de satisfação</legend>
        
        <fieldset>
            <legend>Formulário de cadastro</legend>
            <div id="conteudo_form">
                <? require_once("_financeiro/__cliente_pesquisa_form.php"); ?>
            </div>
        </fieldset>
        <br />
        
        <fieldset>
            <legend>Pesquisas realizadas</legend>
                
                <table cellspacing="0" width="100%">
                <tr>
                  <th width="12%" align="left" valign="bottom">Data da pesquisa</th>
                  <th width="12%" align="left" valign="bottom">Cliente</th>
                  <th width="24%" align="left" valign="bottom">Responsável</th>
                  <th width="10%" align="left" valign="bottom">Pesquisa</th>
                  <th width="17%" align="left" valign="bottom">M&eacute;dia</th>
                  <th width="14%" valign="bottom">Lan&ccedil;ado por</th>
                  <th width="11%" align="left" valign="bottom">A&ccedil;&otilde;es</th>
                </tr>
                <?
                $result_pesquisa= mysql_query("select * from qual_pesquisa
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_cliente = '". $id_cliente ."'
												and   status_pesquisa <> '2'
												order by data_pesquisa desc
												") or die(mysql_error());
                $linhas_pesquisa= mysql_num_rows($result_pesquisa);
                
                $i=0;
                while ($rs_pesquisa= mysql_fetch_object($result_pesquisa)) {
                ?>
                <tr id="linha_<?=$i;?>" <? if ($i%2==0) echo "class=\"cor_sim\""; ?>>
                    <td valign="top"><?= desformata_data($rs_pesquisa->data_pesquisa); ?></td>
                    <td valign="top"><?= pega_sigla_pessoa($rs_pesquisa->id_cliente); ?></td>
                    <td valign="top"><?= $rs_pesquisa->responsavel; ?></td>
                    <td valign="top">
                    	<?
						
						?>
                        <a class="menor vermelho" href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_pesquisa_notas&amp;acao=e&amp;id_pesquisa=<?= $rs_pesquisa->id_pesquisa; ?>&amp;id_cliente=<?= $rs_pesquisa->id_cliente; ?>');"><strong>ACESSAR</strong></a>
                        <? ?>
                    </td>
                    <td valign="top" class="menor">
                    	<?
                        $result_categoria= mysql_query("select * from qual_pesquisa_categorias
														where id_empresa = '". $_SESSION["id_empresa"] ."' 
														order by id_pesquisa_categoria asc
														") or die(mysql_error());
						
						while ($rs_categoria= mysql_fetch_object($result_categoria)) {
					
							$result_media= mysql_query("select AVG(qual_pesquisa_notas.nota) as media
														from qual_pesquisa_notas, qual_pesquisa_itens
														where qual_pesquisa_notas.id_pesquisa = '". $rs_pesquisa->id_pesquisa ."'
														and   qual_pesquisa_notas.id_pesquisa_item = qual_pesquisa_itens.id_pesquisa_item
														and   qual_pesquisa_itens.id_pesquisa_categoria = '". $rs_categoria->id_pesquisa_categoria ."'
														and   qual_pesquisa_notas.nota <> '-1'
														");
							$rs_media= mysql_fetch_object($result_media);
							
							echo $rs_categoria->pesquisa_categoria .": ". fnumf($rs_media->media) ."<br />";
						}
						
						$result_media_geral= mysql_query("select AVG(qual_pesquisa_notas.nota) as media
															from qual_pesquisa_notas, qual_pesquisa_itens
															where qual_pesquisa_notas.id_pesquisa = '". $rs_pesquisa->id_pesquisa ."'
															and   qual_pesquisa_notas.id_pesquisa_item = qual_pesquisa_itens.id_pesquisa_item
															and   qual_pesquisa_notas.nota <> '-1'
															");
						$rs_media_geral= mysql_fetch_object($result_media_geral);
						
						echo "<br /><strong>Geral: </strong>". fnumf($rs_media_geral->media);
						
						?>
                    </td>
                    <td valign="top" align="center" class="menor"><?= primeira_palavra(pega_nome_pelo_id_usuario($rs_pesquisa->id_usuario)); ?></td>
                    <td valign="top">
                        <? if ($rs_pesquisa->id_motivo=="") { ?>
                            <a href="javascript:void(0);" onclick="ajaxLink('conteudo_form', 'carregaPagina&amp;pagina=financeiro/cliente_pesquisa_form&amp;acao=e&amp;id_pesquisa=<?= $rs_pesquisa->id_pesquisa; ?>&amp;id_cliente=<?= $rs_pesquisa->id_cliente; ?>');">
                                <img border="0" src="images/ico_lapis.png" alt="Editar" />
                            </a>
                            <? //if ($_SESSION["tipo_usuario"]=="a") { ?>
                            |
                            <a href="javascript:ajaxLink('linha_<?=$i;?>', 'clientePesquisaExcluir&amp;id_pesquisa=<?= $rs_pesquisa->id_pesquisa; ?>&amp;id_cliente=<?= $rs_pesquisa->id_cliente; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
                                <img border="0" src="images/ico_lixeira.png" alt="Status" />
                            </a>
                            <? //} ?>
                        <? } else echo "-"; ?>
                    </td>
                </tr>
                <?
                    $i++;
                }
                ?>
            </table>
            
        </fieldset>
    </fieldset>
</div>

<br />
<? } ?>