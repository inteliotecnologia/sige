<?
require_once("conexao.php");
if (pode("psey", $_SESSION["permissao"])) {
		
	$result_percurso= mysql_query("select *
									from  tr_percursos, tr_percursos_passos
									where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
									and   tr_percursos_passos.passo = '1'
									and   tr_percursos.id_percurso = '". $_GET["id_percurso"] ."'
									") or die(mysql_error());
	$rs_percurso= mysql_fetch_object($result_percurso);
	
	
?>
<h2>Transporte - Completar informações</h2>

<fieldset>
    <legend>Dados do percurso</legend>
    
    <div class="parte50">
        <label>Tipo:</label>
        <?= pega_coleta_entrega($rs_percurso->tipo); ?>
        <br />
        
        <label>Veículo:</label>
        <?= pega_veiculo($rs_percurso->id_veiculo); ?>
        <br />
        
        <label>Motorista:</label>
        <?= pega_funcionario($rs_percurso->id_motorista); ?>
        <br />
    </div>
    <div class="parte50">
    	<label>Data/hora:</label>
    	<?= desformata_data($rs_percurso->data_percurso) ." ". substr($rs_percurso->hora_percurso, 0, 5); ?>
	    <br /><br />
        
        <label>Clientes:</label>
    	<div class="menor">
			<?
			$result_clientes= mysql_query("select * from pessoas, pessoas_tipos, tr_percursos_clientes
											where pessoas.id_pessoa = pessoas_tipos.id_pessoa
											and   pessoas_tipos.tipo_pessoa = 'c'
											and   pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   pessoas.status_pessoa = '1'
											and   pessoas.id_cliente_tipo = '1'
											and   pessoas.id_pessoa = tr_percursos_clientes.id_cliente
											and   tr_percursos_clientes.id_percurso = '". $_GET["id_percurso"] ."'
											order by  pessoas.apelido_fantasia asc
											") or die(mysql_error());
			
			$linhas_clientes= mysql_num_rows($result_clientes);
			
			$k=1;
			while ($rs_clientes= mysql_fetch_object($result_clientes)) {
				echo $rs_clientes->sigla;
				
				if ($k!=$linhas_clientes) echo ", ";
				
				$k++;
			}
			?>
        </div>
        <a href="./?pagina=transporte/percurso&amp;acao=e&amp;id_percurso=<?= $rs_percurso->id_percurso; ?>">editar</a>
	    <br /><br />
    </div>
    
    
</fieldset>

<fieldset>
	<legend><a href="javascript:void(0);" onclick="abreFechaDiv('log');">Alterações realizadas neste percurso</a></legend>
    
    <div id="log" class="nao_mostra">
		<?
        $result_log= mysql_query("select * from logs
                                    where id_acao_log= '1'
                                    and   id_referencia = '". $_GET["id_percurso"] ."'
                                    order by id desc
                                    ") or die(mysql_error());
        $linhas_log= mysql_num_rows($result_log);
        
        if ($linhas_log>0) {
        ?>
        <table cellspacing="0" cellpadding="2">
            <tr>
                <th align="left" width="15%">Data/hora</th>
                <th align="left" width="25%">Usuário</th>
                <th align="left">Texto</th>
            </tr>
            <?
            $i=0;
			while ($rs_log= mysql_fetch_object($result_log)) {
				if (($i%2)==0) $classe="cor_sim";
				else $classe="cor_nao";
			?>
                <tr class="<?=$classe;?>">
                    <td><?= formata_data_timestamp($rs_log->data); ?></td>
                    <td><?= pega_nome_pelo_id_usuario($rs_log->id_usuario); ?></td>
                    <td><?= $rs_log->texto; ?></td>
                </tr>
            <? $i++; } ?>
        </table>
        <? } ?>
    </div>
    
</fieldset>

<form action="<?= AJAX_FORM; ?>formPercursoDados" method="post" name="formPercursoDados" id="formPercursoDados" onsubmit="return validaFormNormal('validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="" />
    <input name="id_percurso" class="escondido" type="hidden" id="id_percurso" value="<?= $rs_percurso->id_percurso; ?>" />
    
    <input name="tipo" class="escondido" type="hidden" id="tipo" value="<?= $rs_percurso->tipo; ?>" />

    <fieldset>
        <legend>Saída da empresa</legend>
        
        <table cellpadding="0" cellspacing="0" width="100%">
              <tr>
                  <th width="32%" align="left">&nbsp;</th>
                    <th width="14%" align="left" class="unsortable">Data</th>
                    <th width="12%" align="left" class="unsortable">Hora</th>
                    <th width="12%" align="left" class="unsortable">Km</th>
                  <th width="7%" align="left" class="unsortable">&nbsp;</th>
                  <th width="23%" align="left" class="unsortable">&nbsp;</th>
              </tr>

              <?
				$result_passo= mysql_query("select * from tr_percursos_passos
											where id_percurso = '". $rs_percurso->id_percurso ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   passo = '1' 
											");
				$l=0;
				$k=1;
                while ($rs_passo= mysql_fetch_object($result_passo)) {
                    if (($k%2)!=0) $classe= "odd";
                    else $classe= "even";
					
					$data_saida= $rs_passo->data_percurso;
					
					if ($rs_passo->km==0) $km_aqui= "";
					else $km_aqui= fnumf($rs_passo->km);
                ?>
              <tr class="<?=$classe;?>">
                  <td>
                      Saída da empresa
                      <input type="hidden" id="passo_<?=$l?>" name="passo[<?=$l;?>]" class="escondido" value="1" />
                      
                      <input type="hidden" id="id_cliente_<?=$l?>" name="id_cliente[<?=$l;?>]" class="escondido" value="0" title="Cliente" />
                  </td>
                    <td><input id="data_percurso_<?=$l?>" name="data_percurso[<?=$l;?>]" class="tamanho80" onkeyup="formataData(this);" maxlength="10" value="<?= desformata_data($rs_passo->data_percurso); ?>" title="Data" /></td>
                    <td><input id="hora_percurso_<?=$l?>" name="hora_percurso[<?=$l;?>]" class="tamanho80" onkeyup="formataHora(this);" maxlength="5" value="<?= substr($rs_passo->hora_percurso, 0, 5); ?>" title="Hora" /></td>
                    <td><input id="km_<?=$l?>" name="km[<?=$l;?>]" class="tamanho80" value="<?= $km_aqui; ?>" title="Km" /></td>
                  <td>
                      <input type="hidden" id="pnr_<?=$l?>" name="pnr[<?=$l;?>]" value="" title="PNR" class="escondido" />
                  </td>
                  <td>
                      <input type="hidden" id="peso_<?=$l?>" name="peso[<?=$l;?>]" class="escondido" value="" />
                  </td>
              </tr>
            <? $k++; $l++; } ?>

          </table>
        
    </fieldset>
    
    <? if ($rs_percurso->tipo!=3) { ?>
    
    <fieldset>
        <legend>Clientes</legend>
    
          <table cellpadding="0" cellspacing="0" width="100%" id="tabela" class="sortable">
              <tr>
                  <th width="32%" align="left">Cliente</th>
                    <th width="14%" align="left" class="unsortable">Data</th>
                    <th width="12%" align="left" class="unsortable">Hora</th>
                    <th width="12%" align="left" class="unsortable">Km</th>
                  <th width="7%" align="left" class="unsortable">PNR*</th>
                  <th width="23%" align="left" class="unsortable">Peso total</th>
              </tr>

              <?
                $result_clientes= mysql_query("select * from pessoas, pessoas_tipos, tr_percursos_clientes
                                                where pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                and   pessoas_tipos.tipo_pessoa = 'c'
                                                and   pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   pessoas.status_pessoa = '1'
                                                and   pessoas.id_pessoa = tr_percursos_clientes.id_cliente
                                                and   tr_percursos_clientes.id_percurso = '". $_GET["id_percurso"] ."'
                                                order by pessoas.apelido_fantasia asc
                                                ") or die(mysql_error());
                
                $linhas_clientes= mysql_num_rows($result_clientes);
                
                $k=1;
                while ($rs_clientes= mysql_fetch_object($result_clientes)) {
                    
                    $result_passo= mysql_query("select * from tr_percursos_passos
                                                    where id_percurso = '". $_GET["id_percurso"] ."'
                                                    and   id_empresa = '". $_SESSION["id_empresa"] ."'
                                                    and   id_cliente = '". $rs_clientes->id_pessoa ."'
													and   passo = '2' 
                                                ");
					
                    $linhas_passo= mysql_num_rows($result_passo);
                    $rs_passo= mysql_fetch_object($result_passo);
                    
					if (($rs_passo->data_percurso=="0000-00-00") || ($rs_passo->data_percurso=="")) $data_percurso_aqui= desformata_data($data_saida);
					else $data_percurso_aqui= desformata_data($rs_passo->data_percurso);
					
					if (($rs_passo->hora_percurso=="00:00:00") || ($rs_passo->hora_percurso=="")) $hora_percurso_aqui= "";
					else $hora_percurso_aqui= $rs_passo->hora_percurso;
					
					if ($rs_passo->km==0) $km_aqui= "";
					else $km_aqui= fnumf($rs_passo->km);
					
                    if (($k%2)==0) $classe= "odd";
                    else $classe= "even";
					
					switch($rs_percurso->tipo) {
						case 1:
						case 4:
							$balanca= $rs_clientes->balanca_coleta;
							break;
						case 2:
						case 5:
							$balanca= $rs_clientes->balanca_entrega;
							break;
						default: $balanca= 0; break;
					}
                ?>
              <tr class="<?=$classe;?>">
                  <td>
                      <?= $rs_clientes->apelido_fantasia; ?>
                      
                      <input type="hidden" id="passo_<?=$l?>" name="passo[<?=$l;?>]" class="escondido" value="2" />
                      <input type="hidden" id="id_cliente_<?=$l?>" name="id_cliente[<?=$l;?>]" class="escondido" value="<?= $rs_clientes->id_pessoa; ?>" title="Cliente" />
                  </td>
                    <td><input id="data_percurso_<?=$l?>" name="data_percurso[<?=$l;?>]" class="tamanho80" onkeyup="formataData(this);" maxlength="10" value="<?= $data_percurso_aqui; ?>" title="Data" /></td>
                    <td><input id="hora_percurso_<?=$l?>" name="hora_percurso[<?=$l;?>]" class="tamanho80" onkeyup="formataHora(this);" maxlength="5" value="<?= substr($hora_percurso_aqui, 0, 5); ?>" title="Hora" /></td>
                    <td><input id="km_<?=$l?>" name="km[<?=$l;?>]" value="<?= $km_aqui; ?>" title="Km" /></td>
                  <td>
                      <?
					  //$pesagem_cliente= pega_pesagem_cliente_contrato($rs_clientes->id_contrato);
					  
					  if ($balanca==1) {
					  ?>
                      <input type="checkbox" class="tamanho20" id="pnr_<?=$l?>" <? if ($rs_passo->pnr==1) echo "checked=\"checked\""; ?> onclick="atribuiValor('peso_<?=$l?>', '');" name="pnr[<?=$l;?>]" value="1" title="PNR" />
                      <? } else { ?>
                      -
                      <input type="hidden" class="escondido" id="pnr_<?=$l?>" name="pnr[<?=$l;?>]" value="1" title="PNR" />
                      <? } ?>
                  </td>
                  <td>
                      <?
					  if ($balanca==1) {
                      	if (($linhas_passo==0) || ($rs_passo->pnr==1)) $peso= ""; else $peso= fnum($rs_passo->peso);
					  ?>
                      <input id="peso_<?=$l?>" name="peso[<?=$l;?>]" class="espaco_dir tamanho25p" value="<?= $peso; ?>" onkeydown="formataValor(this,event);" title="Pesagem" /> kg
                      <? } else { ?>
                      -
                      <input type="hidden" id="peso_<?=$l?>" name="peso[<?=$l;?>]" class="escondido" value="0" title="Pesagem" />
                      <? } ?>
                  </td>
              </tr>
            <? $k++; $l++; } ?>
          </table>
          <br />
          
		  <p>* PNR = Peso não registrado.</p>
	</fieldset>
	
    <? } ?>
    
	<fieldset>
	    <legend>Retorno à empresa</legend>
	    
        <table cellpadding="0" cellspacing="0" width="100%">
              <tr>
                  <th width="32%" align="left">&nbsp;</th>
                    <th width="14%" align="left" class="unsortable">Data</th>
                    <th width="12%" align="left" class="unsortable">Hora</th>
                    <th width="12%" align="left" class="unsortable">Km</th>
                  <th width="7%" align="left" class="unsortable">&nbsp;</th>
                  <th width="23%" align="left" class="unsortable">&nbsp;</th>
              </tr>

              <?
				$result_passo= mysql_query("select * from tr_percursos_passos
											where id_percurso = '". $rs_percurso->id_percurso ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   passo = '3' 
											");
			  	
				$k=1;
                $rs_passo= mysql_fetch_object($result_passo);

				if (($k%2)!=0) $classe= "odd";
				else $classe= "even";
				
				if ($rs_passo->km==0) $km_aqui= "";
				else $km_aqui= fnumf($rs_passo->km);
				
				if (($rs_passo->data_percurso=="0000-00-00") || ($rs_passo->data_percurso=="")) $data_percurso_fim_aqui= ($data_saida);
				else $data_percurso_fim_aqui= ($rs_passo->data_percurso);
                ?>
              <tr class="<?=$classe;?>">
                  <td>
                      Retorno à empresa
                      
                      <input type="hidden" id="passo_<?=$l?>" name="passo[<?=$l;?>]" class="escondido" value="3" />
                      <input type="hidden" id="id_cliente_<?=$l?>" name="id_cliente[<?=$l;?>]" class="escondido" value="0" title="Cliente" />
                  </td>
                    <td><input id="data_percurso_<?=$l?>" name="data_percurso[<?=$l;?>]" class="tamanho80" onkeyup="formataData(this);" maxlength="10" value="<?= desformata_data($data_percurso_fim_aqui); ?>" title="Data" /></td>
                    <td><input id="hora_percurso_<?=$l?>" name="hora_percurso[<?=$l;?>]" class="tamanho80" onkeyup="formataHora(this);" maxlength="5" value="<?= substr($rs_passo->hora_percurso, 0, 5); ?>" title="Hora" /></td>
                    <td><input id="km_<?=$l?>" name="km[<?=$l;?>]" class="tamanho80" value="<?= $km_aqui; ?>" title="Km" /></td>
                  <td>
                      <input type="hidden" id="pnr_<?=$l?>" name="pnr[<?=$l;?>]" value="" title="PNR" class="escondido" />
                  </td>
                  <td>
                      <input type="hidden" id="peso_<?=$l?>" name="peso[<?=$l;?>]" class="escondido" value="" />
                  </td>
              </tr>
            <? $k++; $l++; ?>
          </table>
        
	</fieldset>

    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>

<script language="javascript" type="text/javascript">
	//daFoco("veiculo");
</script>
<? } ?>