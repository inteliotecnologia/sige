<?
require_once("conexao.php");
if (pode("rhv4", $_SESSION["permissao"])) {
	$acao= 'i';
	
	if ($_GET["acao"]!="")
		$acao= $_GET["acao"];
	
	$tipo_afastamento= $_GET["tipo_afastamento"];
	
	$id_funcionario= $_GET["id_funcionario"];
	
	if ($acao=='e') {
		$result= mysql_query("select rh_afastamentos.*, DATE_FORMAT(rh_afastamentos.data_emissao, '%d/%m/%Y') as data_emissao2
								from  rh_afastamentos, rh_funcionarios
								where rh_afastamentos.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_afastamentos.id_afastamento = '". $_GET["id_afastamento"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		$tipo_afastamento= $rs->tipo_afastamento;
		$id_funcionario= $rs->id_funcionario;
	}
?>

<? if ($_GET["esquema"]!='1') { ?>
<h2>Afastamento - <?= pega_tipo_afastamento($tipo_afastamento); ?></h2>
<? } else { ?>
<ul class="recuo1">
	<li><a href="./?pagina=rh/afastamento_listar&amp;tipo_afastamento=<?= $tipo_afastamento; ?>&amp;id_funcionario=<?= $id_funcionario;?>">listar</a></li>
</ul>
<? } ?>

<fieldset>
    <legend>Afastamento - <?= pega_tipo_afastamento($tipo_afastamento); ?></legend>
    
    <form action="<?= AJAX_FORM; ?>formAfastamento&amp;acao=<?= $acao; ?>" method="post" name="formAfastamento" id="formAfastamento" onsubmit="return ajaxForm('conteudo', 'formAfastamento', 'validacoes', true);">
    
		<?
        if ($tipo_afastamento=='a')
            $validacoes_adicionais= "";
        if ($tipo_afastamento=='o')
            $validacoes_adicionais2= "|id_motivo@vazio";
        ?>
        
        <? if ($tipo_afastamento!='d') { ?>
        <input class="escondido" type="hidden" id="validacoes" value="id_empresa@vazio|id_funcionario@vazio|data_emissao@data|qtde_dias@numeros<?=$validacoes_adicionais2;?>|data_inicial_abono@data<?=$validacoes_adicionais;?>" />
        <? } else { ?>
        <input class="escondido" type="hidden" id="validacoes" value="id_empresa@vazio|id_funcionario@vazio|data_emissao@data" />
        <? } ?>
        
        <? if ($acao=='e') { ?>
        <input name="id_afastamento" class="escondido" type="hidden" id="id_afastamento" value="<?= $rs->id_afastamento; ?>" />
        <? } ?>
        
        <input name="tipo_afastamento" class="escondido" type="hidden" id="tipo_afastamento" value="<?= $tipo_afastamento; ?>" />
        
        <div class="parte50">
            
            <label for="id_funcionario">* Funcionário:</label>
            <?
            if ($acao=='e') {
				echo pega_funcionario($rs->id_funcionario);
			?>
            <input type="hidden" class="escondido" name="id_funcionario" id="id_funcionario" value="<?= $rs->id_funcionario; ?>" title="Funcionário">
            <? } else { ?>
            <select name="id_funcionario" id="id_funcionario" title="Funcionário">
                <option value="">- SELECIONE -</option>
                <?
				$result_fun= mysql_query("select *
											from  pessoas, rh_funcionarios, rh_carreiras
											where pessoas.id_pessoa = rh_funcionarios.id_pessoa
											and   pessoas.tipo = 'f'
											and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
											and   rh_carreiras.atual = '1'
											and   rh_funcionarios.status_funcionario <> '0'
											and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
											order by pessoas.nome_rz asc
											") or die(mysql_error());
                $i=0;
                while ($rs_fun= mysql_fetch_object($result_fun)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>"<? if ($rs_fun->id_funcionario==$_GET["id_funcionario"]) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                <? $i++; } ?>
            </select>
            <? } ?>
            <br />
            
            <label for="data_emissao">* Data de emissão:</label>
            <input title="Data de emissão" name="data_emissao" id="data_emissao"  onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="<?= $rs->data_emissao2; ?>" maxlength="10" />
            <br />
            
            <?
            if (($tipo_afastamento=='o') || ($tipo_afastamento=='d') || ($tipo_afastamento=='s')) {
			?>
            <label for="id_motivo">Motivo:</label>
            <select name="id_motivo" id="id_motivo" title="Motivo" <? if ($tipo_afastamento=='o') { ?> onchange="pegaQtdeDiasPeloMotivo();" <? } ?> >
                <option value="">- SELECIONE -</option>
                <?
				$result_mot= mysql_query("select * from  rh_motivos
											where rh_motivos.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   rh_motivos.tipo_motivo = '". $tipo_afastamento ."'
											order by rh_motivos.motivo asc
											") or die(mysql_error());
                $i=0;
                while ($rs_mot= mysql_fetch_object($result_mot)) {
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_mot->id_motivo .'@'. $rs_mot->qtde_dias; ?>" <? if ($rs_mot->id_motivo==$rs->id_motivo) echo "selected=\"selected\""; ?>><?= $rs_mot->motivo; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            <? } ?>
            
            <? if ($tipo_afastamento!='d') { ?>
            <label for="data_inicial_abono">* Data inicial:</label>
            <input title="Dia inicial do abono" name="data_inicial_abono" id="data_inicial_abono"  onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="<?= pega_data_inicial_afastamento($tipo_afastamento, $rs->id_afastamento); ?>" maxlength="10" />
            <br />
            
            <? if ($tipo_afastamento!='b') { ?>
            <label for="qtde_dias">* Quantidade de dias:</label>
				<? if ($tipo_afastamento=='o') { ?>
                <div id="qtde_dias_atualiza">selecione um motivo</div>
                <? } ?>
            <input <? if ($tipo_afastamento=='o') { ?> type="hidden" class="escondido" <? } else { ?> class="tamanho25p espaco_dir" <? } ?> onblur="retornaDataFinal('data_inicial_abono', 'qtde_dias');" title="Quantidade de dias" name="qtde_dias" id="qtde_dias" value="<?= $rs->qtde_dias; ?>" >
            <div id="resultado_data"></div>
            <br />
            
            <? } else { ?>
            <input type="hidden" class="escondido" title="Quantidade de dias" name="qtde_dias" id="qtde_dias" value="1" >
            <? } ?>
            
            <? } ?>
            
            <?
			if ($acao=='i') {
				if ($tipo_afastamento=='d')
					$obs= "";
				else {
					if ($tipo_afastamento=='s')
						$obs= "";
				}
			}
			else
				$obs= $rs->obs;
			?>
            <label for="obs">Observações:</label>
            <input name="obs" id="obs" title="Observação" value="<?=$obs;?>" />
            <br />
            
        </div>
        <div class="parte50">
            
            <? if ($tipo_afastamento=='f') { ?>
            <fieldset>
                <legend>Período aquisitivo</legend>
                
                <label for="data_inicial_aquisitivo">* Data inicial:</label>
                <input title="Dia inicial do período aquisitivo" name="data_inicial_aquisitivo" id="data_inicial_aquisitivo"  onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="<?= desformata_data($rs->data_inicial_aquisitivo); ?>" maxlength="10" />
                <br />
                
                <label for="data_final_aquisitivo">* Data final:</label>
                <input title="Dia final do período aquisitivo" name="data_final_aquisitivo" id="data_final_aquisitivo"  onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="<?= desformata_data($rs->data_final_aquisitivo); ?>" maxlength="10" />
                <br />
            </fieldset>
            <? } ?>
            
            <? if ($tipo_afastamento=='p') { ?>
            <label for="num_requerimento">* Número do requerimento:</label>
            <input title="Número do requerimento" name="num_requerimento" id="num_requerimento" value="<?= $rs->num_requerimento; ?>" />
            <br />
            <? } ?>
            
            <? if ($tipo_afastamento=='a') { ?>
            <label for="pesquisa_cid">* Pesquisa CID:</label>
            <input name="pesquisa_cid" id="pesquisa_cid" value="<?= $rs->num_cid10; ?>" onblur="retornaCid();" />
            <br />
            
            <? if ($acao=='e') { ?>
            <script language="javascript">retornaCid();</script>
			<? } ?>
            
            <label>&nbsp;</label>
            <div id="cid_atualiza">
            	Faça a busca.
            </div>
            <br />
            
            <label for="crm">* CRM:</label>
            <input <? if ($acao=='i') { ?> onblur="verificaCRM(this.value);" <? } ?> title="CRM" name="crm" id="crm" value="<?= $rs->crm; ?>" />
            <br />
            
            <label for="nome_medico">* Médico:</label>
            <div id="nome_medico_area">
	            <input title="Nome do médico" name="nome_medico" id="nome_medico" value="<?= $rs->nome_medico; ?>" />
            </div>
            <br />
            
            <label for="modo_afastamento">Modo:</label>
            <select name="modo_afastamento" id="modo_afastamento" title="Modo" >
                <option class="cor_nao" value="1" <? if (($rs->modo_afastamento=="p") || ($acao=='i')) echo "selected=\"selected\""; ?>>Próprio</option>
                <option class="cor_sim" value="2" <? if ($rs->modo_afastamento=="p") echo "selected=\"selected\""; ?>>Acompanhamento</option>
            </select>
            <br />
            
            <? } ?>
        </div>

        <br /><br />
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </form>
</fieldset>

<? } ?>