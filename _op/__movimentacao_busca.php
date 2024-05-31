<?
require_once("conexao.php");
if (pode_algum("pl", $_SESSION["permissao"])) {
?>

<h2>Movimentação do quantitativo de roupa - Modelo faturamento</h2>

<div class="parte50">
    <fieldset>
        <legend>Por contrato</legend>
        
        <form action="index2.php?pagina=op/movimentacao_relatorio" target="_blank" method="post" name="formMovimentacao" id="formMovimentacao" onsubmit="return validaFormNormal('validacoes');">
                
            <input class="escondido" type="hidden" id="validacoes" value="" />
            <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
            
            <input class="escondido" type="hidden" name="tipo_relatorio" value="e" />
            
            <label for="id_contrato">Contrato:</label>
            <select name="id_contrato" id="id_contrato" title="Contrato">
                <? /*<option value="">- TODOS - </option>*/ ?>
                <?
                $result_contrato= mysql_query("select * from fi_contratos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by id_contrato asc
												");
                $i=0;
                while ($rs_contrato = mysql_fetch_object($result_contrato)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_contrato->id_contrato; ?>"<? if ($rs_contrato->id_contrato==$rs->id_contrato) echo "selected=\"selected\""; ?>><?= $rs_contrato->contrato; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="periodo">Período:</label>
            <select name="periodo" id="periodo" title="Período">	  		
                <?
                $i=0;
                $result_per= mysql_query("select distinct(DATE_FORMAT(data_percurso, '%m/%Y')) as data_percurso2
                                            from  tr_percursos_passos
                                            where DATE_FORMAT(data_percurso, '%m/%Y') <> '00/0000'
                                            order by data_percurso desc ");
                while ($rs_per= mysql_fetch_object($result_per)) {
                    $data_percurso= explode('/', $rs_per->data_percurso2);
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_percurso2; ?>"><?= traduz_mes($data_percurso[0]) .'/'. $data_percurso[1]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label>&nbsp;</label>
            ou
            <br />
            
            <label for="data1">Datas:</label>
            <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 1" />
            <div class="flutuar_esquerda espaco_dir">à</div>
            <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 2" />
            <br /><br />
            
            <label for="identifica">Identificar PNR*:</label>
            <input name="identifica" id="identifica" type="checkbox" class="tamanho20" value="1" />
            <br /><br />
            
            <label for="discriminar_relave">Discriminar relave*:</label>
            <input name="discriminar_relave" id="discriminar_relave" type="checkbox" class="tamanho20" value="1" />
            <br /><br />
            
            <label for="obs">OBS:</label>
            <input name="obs" id="obs" />
            <br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </form>
        
    </fieldset>
</div>
<div class="parte50">
    <fieldset>
        <legend>Por cliente</legend>
        
        <form action="index2.php?pagina=op/movimentacao_relatorio" target="_blank" method="post" name="formMovimentacao" id="formMovimentacao" onsubmit="return validaFormNormal('validacoes');">
                
            <input class="escondido" type="hidden" id="validacoes" value="" />
            <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
            
            <input class="escondido" type="hidden" name="tipo_relatorio" value="e" />
            
            <label for="lista">Lista:</label>
            <select name="lista" id="lista" onchange="alteraClientesAtivosInativos(this.value, 'id_cliente_atualiza');">
                <option value="1">Ativos</option>
                <option value="0" class="cor_sim">Inativos</option>
            </select>
            <br />
            
            <label for="id_cliente">Cliente:</label>
            <div id="id_cliente_atualiza">
                <select name="id_cliente" id="id_cliente" title="Cliente">
                    <option value="">- SELECIONE -</option>
                    <?
                    $result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
                                                where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                and   pessoas_tipos.tipo_pessoa = 'c'
                                                and   pessoas.id_cliente_tipo = '1'
												and   pessoas.status_pessoa = '1'
                                                order by 
                                                pessoas.apelido_fantasia asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_cli = mysql_fetch_object($result_cli)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cli->id_cliente; ?>"><?= $rs_cli->apelido_fantasia; ?></option>
                    <? $i++; } ?>
                </select>
            </div>
            <br />
            
            <? /* 
            <?
			$result_clientes= mysql_query("select * from pessoas, pessoas_tipos
											where pessoas.id_pessoa = pessoas_tipos.id_pessoa
											and   pessoas_tipos.tipo_pessoa = 'c'
											and   pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   pessoas.status_pessoa = '1'
											and   pessoas.id_cliente_tipo = '1'
											order by 
											pessoas.apelido_fantasia asc
											") or die(mysql_error());
			?>
			<label>Clientes:</label>
			<div id="clientes_lista">
				<?
				$i=1;
				while ($rs_clientes= mysql_fetch_object($result_clientes)) {
				?>
				
				<input <? if ($linhas_permissao>0) echo "checked=\"checked\""; ?> class="tamanho15 espaco_dir" type="checkbox" name="id_cliente[]" id="id_cliente_<?= $rs_clientes->id_pessoa;?>" value="<?= $rs_clientes->id_pessoa;?>" />
				<label for="id_cliente_<?= $rs_clientes->id_pessoa;?>" class="alinhar_esquerda menor2 nao_negrito tamanho70"><?= $rs_clientes->codigo .". ". $rs_clientes->sigla;?></label>
				
				<?
					if (($i%3)==0) echo "<br /> <label>&nbsp;</label>";
					
					$i++;
				}
				?>
			</div>
			<br />
			<label>&nbsp;</label>
			<a href="javascript:void(0);" class="menor" onclick="checarTudo('clientes_lista');">inverter seleção</a>
			<br /><br />
            */ ?>
            
            <label for="periodo">Período:</label>
            <select name="periodo" id="periodo" title="Período">	  		
                <?
                $i=0;
                $result_per= mysql_query("select distinct(DATE_FORMAT(data_percurso, '%m/%Y')) as data_percurso2
                                            from  tr_percursos_passos
                                            where DATE_FORMAT(data_percurso, '%m/%Y') <> '00/0000'
                                            order by data_percurso desc ");
                while ($rs_per= mysql_fetch_object($result_per)) {
                    $data_percurso= explode('/', $rs_per->data_percurso2);
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_percurso2; ?>"><?= traduz_mes($data_percurso[0]) .'/'. $data_percurso[1]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label>&nbsp;</label>
            ou
            <br />
            
            <label for="data1">Entre datas:</label>
            <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 1" />
            <div class="flutuar_esquerda espaco_dir">à</div>
            <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 2" />
            <br /><br />
            
            <label for="identifica">Identificar PNR*:</label>
            <input name="identifica" id="identifica" type="checkbox" class="tamanho20" value="1" />
            <br /><br />
            
            <label for="discriminar_relave">Discriminar relave*:</label>
            <input name="discriminar_relave" id="discriminar_relave" type="checkbox" class="tamanho20" value="1" />
            <br /><br />
            
            <label for="obs">OBS:</label>
            <input name="obs" id="obs" />
            <br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </form>
        
    </fieldset>
</div>
<br />

<h2>Quantitativo de pacotes</h2>

<div class="parte50">
    <fieldset>
        <legend>Por contrato</legend>
        
        <form action="index2.php?pagina=op/movimentacao_pacotes_relatorio" target="_blank" method="post" name="formMovimentacao" id="formMovimentacao" onsubmit="return validaFormNormal('validacoes_pacotes');">
                
            <input class="escondido" type="hidden" id="validacoes_pacotes" value="" />
            <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
            
            <input class="escondido" type="hidden" name="tipo_relatorio" value="e" />
            
            <label for="id_contrato">Contrato:</label>
            <select name="id_contrato" id="id_contrato" title="Contrato">
                <option value="">- TODOS - </option>
                <?
                $result_contrato= mysql_query("select * from fi_contratos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by id_contrato asc
												");
                $i=0;
                while ($rs_contrato = mysql_fetch_object($result_contrato)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_contrato->id_contrato; ?>"<? if ($rs_contrato->id_contrato==$rs->id_contrato) echo "selected=\"selected\""; ?>><?= $rs_contrato->contrato; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="periodo">Período:</label>
            <select name="periodo" id="periodo" title="Período">	  		
                <?
                $i=0;
                $result_per= mysql_query("select distinct(DATE_FORMAT(data_percurso, '%m/%Y')) as data_percurso2
                                            from  tr_percursos_passos
                                            where DATE_FORMAT(data_percurso, '%m/%Y') <> '00/0000'
                                            order by data_percurso desc ");
                while ($rs_per= mysql_fetch_object($result_per)) {
                    $data_percurso= explode('/', $rs_per->data_percurso2);
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_percurso2; ?>"><?= traduz_mes($data_percurso[0]) .'/'. $data_percurso[1]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </form>
        
    </fieldset>
</div>


<? } ?>