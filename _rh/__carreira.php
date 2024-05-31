<?
require_once("conexao.php");
if (pode("rv", $_SESSION["permissao"])) {
	$acao= 'i'; //$_GET["acao"];
	
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
?>
<div class="parte50">
    <fieldset>
        <legend>Alteração de carreira</legend>
        <div id="conteudo_form">
			<? require_once("_rh/__carreira_form.php"); ?>
        </div>
    </fieldset>
</div>
<div class="parte50">
    <?
    $result_atual= mysql_query("select *, DATE_FORMAT(data, '%d/%m/%Y') as data2 from rh_carreiras, rh_funcionarios
                                where rh_carreiras.id_funcionario = '". $id_funcionario ."'
                                and   rh_carreiras.atual = '1'
								and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                ");
    $rs_atual= mysql_fetch_object($result_atual);
    ?>
    <fieldset>
        <legend>Situação atual</legend>
        
        <label>Data da situação:</label>
        <?= $rs_atual->data2; ?>
        <br />
        
        <label>Última ação:</label>
        <?= pega_acao_carreira($rs_atual->id_acao_carreira); ?>
        <br />
        
        <label>Departamento:</label>
        <?= pega_departamento($rs_atual->id_departamento); ?>
        <br />
        
        <label>Cargo:</label>
        <?= pega_cargo($rs_atual->id_cargo); ?>
        <br />
        
        <label>Turno:</label>
        <?= pega_turno($rs_atual->id_turno); ?>
        <br />
        
        <label>Intervalo:</label>
        <?= pega_intervalo($rs_atual->id_intervalo); ?>
        <br />
            
        <label>Turnante:</label>
        <?= sim_nao($rs_atual->turnante); ?>
        <br />

		<label>Insalubridade:</label>
        <?= $rs_atual->insalubridade; ?>%
        <br />
        
    </fieldset>
    
    <fieldset>
    	<legend>Marcar afastamento manualmente</legend>
        
        <?
		if ($rs_atual->afastado==1) $botao_texto= "não";
		else $botao_texto= "";
		?>
        
        <label>Situação atual:</label>
        Afastado: <?= sim_nao($rs_atual->afastado); ?>
        <br /><br />
        
        <label>&nbsp;</label>
        <button onclick="ajaxLink('conteudo_interno', 'afastamentoManual&amp;id_funcionario=<?= $rs_atual->id_funcionario; ?>&amp;afastado=<?=inverte_0_1($rs_atual->afastado);?>');">Marcar como <?=$botao_texto;?> afastado</button>
    </fieldset>
</div>

<br />

<fieldset>
    <legend>Carreira do funcionário na empresa</legend>
    	
        <table cellspacing="0" width="100%">
        <tr>
          <th width="10%" align="left" valign="bottom">Data</th>
          <th width="14%" align="left" valign="bottom">A&ccedil;&atilde;o</th>
          <th width="16%" align="left" valign="bottom">Cargo</th>
          <th width="15%" align="left" valign="bottom">Departamento</th>
          <th width="12%" align="left" valign="bottom">Turno</th>
          <th width="10%" align="left" valign="bottom">Insalubridade</th>
          <th width="8%" align="left" valign="bottom">Turnante</th>
          <th width="15%" align="left" valign="bottom">A&ccedil;&atilde;o</th>
        </tr>
        <?
		$result= mysql_query("select rh_carreiras.*, DATE_FORMAT(data, '%d/%m/%Y') as data2
								from  rh_carreiras, rh_funcionarios
								where rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_funcionarios.id_funcionario = '". $id_funcionario ."'
								order by rh_carreiras.data asc, rh_carreiras.id_carreira asc
								") or die(mysql_error());
		
		$linhas_carreira= mysql_num_rows($result);
		
		while ($rs= mysql_fetch_object($result)) {
			if ($rs->atual==1)
				$alerta= "\\n\\nESTA É A SITUAÇÃO ATUAL DO FUNCIONÁRIO NA EMPRESA,\\nA SITUAÇÃO ANTERIOR ASSUMIRÁ ESTA POSIÇÃO!";
        ?>
        <tr <? if ($rs->atual==1) echo "class=\"cor_sim\""; ?>>
            <td valign="top"><?= $rs->data2; ?></td>
            <td valign="top">
			<?
            echo pega_acao_carreira($rs->id_acao_carreira);
			
			if (($rs->id_acao_carreira==1) || ($rs->id_acao_carreira==2)) echo " <a class=\"menor\" href=\"javascript:void(0);\" onclick=\"ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/carreira_checklist&amp;id_carreira=". $rs->id_carreira ."');\">(checklist)</a>";
			
			if ($rs->id_acao_carreira==2) echo "<br /><span class=\"menor\">". pega_detalhe_carreira_desligamento($rs->id_detalhe_carreira) ."</span>";
			?>
            </td>
            <td valign="top"><?= pega_cargo($rs->id_cargo); ?></td>
            <td valign="top"><?= pega_departamento($rs->id_departamento); ?></td>
            <td valign="top"><?= pega_turno($rs->id_turno); ?></td>
            <td valign="top"><?= $rs->insalubridade; ?>%</td>
            <td valign="top"><?= sim_nao($rs->turnante); ?></td>
            <td valign="top">
            	<? if ($rs->id_acao_carreira==1) { ?>
                <a onmouseover="Tip('Dados para admissão');" href="index2.php?pagina=rh/documento&amp;id_funcionario=<?=$rs->id_funcionario;?>&amp;tipo=14&amp;id_carreira=<?= $rs->id_carreira; ?>" target="_blank">
                    <img border="0" src="images/ico_pdf.png" alt="Relatório" />
                </a>
                <? } ?>
				<? if ($rs->id_acao_carreira==2) { ?>
                <a onmouseover="Tip('Devolução da carteira de trabalho');" href="index2.php?pagina=rh/documento&amp;id_funcionario=<?=$rs->id_funcionario;?>&amp;tipo=13&amp;id_carreira=<?= $rs->id_carreira; ?>" target="_blank">
                    <img border="0" src="images/ico_pdf.png" alt="Relatório" />
                </a>
                <? } ?>
				<? if ($rs->id_acao_carreira==3) { ?>
                <a onmouseover="Tip('Mudança de departamento/cargo');" href="index2.php?pagina=rh/documento&amp;id_funcionario=<?=$rs->id_funcionario;?>&amp;tipo=3&amp;id_carreira=<?= $rs->id_carreira; ?>" target="_blank">
                    <img border="0" src="images/ico_pdf.png" alt="Relatório" />
                </a>
                <? } ?>
                <? if ($rs->id_acao_carreira==4) { ?>
                <a onmouseover="Tip('Mudança de turno');" href="index2.php?pagina=rh/documento&amp;id_funcionario=<?=$rs->id_funcionario;?>&amp;tipo=16&amp;id_carreira=<?= $rs->id_carreira; ?>" target="_blank">
                    <img border="0" src="images/ico_pdf.png" alt="Relatório" />
                </a>
                <? } ?>
                
                <a href="javascript:void(0);" onclick="ajaxLink('conteudo_form', 'carregaPagina&amp;pagina=rh/carreira_form&amp;acao=e&amp;id_funcionario=<?=$rs->id_funcionario;?>&amp;id_carreira=<?= $rs->id_carreira; ?>');">
                    <img border="0" src="images/ico_lapis.png" alt="Editar" />
                </a>
            	<? if ($linhas_carreira>1) { ?>
                <a href="javascript:ajaxLink('conteudo_interno', 'carreiraExcluir&amp;id_carreira=<?= $rs->id_carreira; ?>&amp;id_funcionario=<?=$rs->id_funcionario;?>');" onclick="return confirm('Tem certeza que deseja excluir?<?=$alerta;?>');">
                    <img border="0" src="images/ico_lixeira.png" alt="Status" />
                </a>
                <? } else { ?>
                <a href="javascript:void(0);" onclick="alert('Não é possível excluir esta situação do funcionário\npois ela é a única do mesmo na empresa!');">
                    <img border="0" src="images/ico_lixeira.png" alt="Status" />
                </a>
                <? } ?>
            </td>
        </tr>
        <?
			if ($rs->id_acao_carreira==1) {
				for ($e=0; $e<2; $e++) {
					if ($e==0) $dias=30;
					else $dias=90;
					
					$data= soma_data($rs->data2, $dias, 0, 0);
					
					$data= soma_data($data, -1, 0, 0);
				?>
				<tr>
					<td class="cinza menor"><?= $data; ?></td>
					<td class="cinza menor">Experiência <?= $dias; ?> dias</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				    <td>
                    	<? if ($dias==30) { ?>
                        <a onmouseover="Tip('Contrato de trabalho por experiência');" href="index2.php?pagina=rh/documento&amp;id_funcionario=<?=$rs->id_funcionario;?>&amp;tipo=8&amp;id_carreira=<?= $rs->id_carreira; ?>" target="_blank">
                            <img border="0" src="images/ico_pdf.png" alt="Relatório" />
                        </a>
                        <a onmouseover="Tip('Recibo de entrega de uniforme');" href="index2.php?pagina=rh/documento&amp;id_funcionario=<?=$rs->id_funcionario;?>&amp;tipo=9&amp;id_carreira=<?= $rs->id_carreira; ?>" target="_blank">
                            <img border="0" src="images/ico_pdf.png" alt="Relatório" />
                        </a>
                        <a onmouseover="Tip('Cadastramento de vale transporte');" href="index2.php?pagina=rh/documento&amp;id_funcionario=<?=$rs->id_funcionario;?>&amp;tipo=10&amp;id_carreira=<?= $rs->id_carreira; ?>" target="_blank">
                            <img border="0" src="images/ico_pdf.png" alt="Relatório" />
                        </a>
                        <a onmouseover="Tip('Cartão de identificação');" href="index2.php?pagina=rh/documento&amp;id_funcionario=<?=$rs->id_funcionario;?>&amp;tipo=11&amp;id_carreira=<?= $rs->id_carreira; ?>" target="_blank">
                            <img border="0" src="images/ico_pdf.png" alt="Relatório" />
                        </a>
                        <a onmouseover="Tip('Devolução da carteira de trabalho');" href="index2.php?pagina=rh/documento&amp;id_funcionario=<?=$rs->id_funcionario;?>&amp;tipo=13&amp;id_carreira=<?= $rs->id_carreira; ?>" target="_blank">
                            <img border="0" src="images/ico_pdf.png" alt="Relatório" />
                        </a>
                        <? } ?>
                        
						<? if ($dias==90) { ?>
                        <a onmouseover="Tip('Prorrogação do contrato de experiência (90 dias)');" href="index2.php?pagina=rh/documento&amp;id_funcionario=<?=$rs->id_funcionario;?>&amp;tipo=2&amp;id_carreira=<?= $rs->id_carreira; ?>" target="_blank">
                            <img border="0" src="images/ico_pdf.png" alt="Relatório" />
                        </a>
                        <? } ?>
                    </td>
				</tr>
				<?
				}//fim for
			}
        }
		?>
    </table>
    
</fieldset>

<fieldset>
	<legend>Excluir funcionário</legend>
    
    <center>
    	<a class="botao" onclick="return confirm('Tem certeza que deseja excluir este funcionário?\n\nA operação não pode ser desfeita!');" href="index2.php?pagina=rh/funcionario_excluir&amp;id_funcionario=<?=$_GET["id_funcionario"];?>">excluir</a>
       <br /><br />
    </center>
    
</fieldset>

<? } ?>