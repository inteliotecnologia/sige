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
<h2>Transporte - Conferência de peso</h2>

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
	    <br />
        
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
											order by  pessoas.nome_rz asc
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
	    <br /><br />
    </div>
    
    
</fieldset>

<form action="<?= AJAX_FORM; ?>formPercursoPesagem" method="post" name="formPercursoPesagem" id="formPercursoPesagem" onsubmit="return validaFormNormal('validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="" />
    <input name="id_percurso" class="escondido" type="hidden" id="id_percurso" value="<?= $_GET["id_percurso"]; ?>" />
    
    <fieldset>
        <legend>Dados da pesagem</legend>
        
        <table cellpadding="0" cellspacing="0" width="100%" id="tabela" class="sortable">
        	<tr>
            	<th width="50%" align="left">Cliente</th>
                <th width="8%" align="left" class="unsortable">PNR*</th>
                <th width="42%" align="left" class="unsortable">Peso total</th>
            </tr>
        
        
			<?
            $result_clientes= mysql_query("select * from pessoas, pessoas_tipos, tr_percursos_clientes
                                            where pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                            and   pessoas_tipos.tipo_pessoa = 'c'
                                            and   pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   pessoas.status_pessoa = '1'
											and   pessoas.id_cliente_tipo = '1'
                                            and   pessoas.id_pessoa = tr_percursos_clientes.id_cliente
                                            and   tr_percursos_clientes.id_percurso = '". $_GET["id_percurso"] ."'
                                            order by pessoas.nome_rz asc
                                            ") or die(mysql_error());
            
            $linhas_clientes= mysql_num_rows($result_clientes);
            
            $k=1;
            while ($rs_clientes= mysql_fetch_object($result_clientes)) {
                
                $result_pesagem= mysql_query("select * from tr_percursos_pesagem
                                                where id_percurso = '". $_GET["id_percurso"] ."'
                                                and   id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   id_cliente = '". $rs_clientes->id_pessoa ."'
                                            ");
                $linhas_pesagem= mysql_num_rows($result_pesagem);
                $rs_pesagem= mysql_fetch_object($result_pesagem);
				
				if (($k%2)==0) $classe= "odd";
				else $classe= "even";
				
				$l= $k-1;
            ?>
            <tr class="<?=$classe;?>">
                <td>
                    <?= $rs_clientes->nome_rz; ?>
                    <input type="hidden" id="id_cliente_<?=$k?>" name="id_cliente[<?=$l;?>]" class="escondido" value="<?= $rs_clientes->id_pessoa; ?>" title="Cliente" />
                </td>
                <td>
              	    <input type="checkbox" class="tamanho20" id="pnr_<?=$k?>" <? if ($rs_pesagem->pnr==1) echo "checked=\"checked\""; ?> onclick="atribuiValor('peso_<?=$k?>', '');" name="pnr[<?=$l;?>]" value="1" title="PNR" />
                </td>
                <td>
                    <? if (($linhas_pesagem==0) || ($rs_pesagem->pnr==1)) $peso= ""; else $peso= fnum($rs_pesagem->peso); ?>
                    <input id="peso_<?=$k?>" name="peso[<?=$l;?>]" class="espaco_dir tamanho25p" value="<?= $peso; ?>" onkeydown="formataValor(this,event);" title="Pesagem" /> kg
                </td>
            </tr>
        <? $k++; } ?>
        </table>
        <br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </fieldset>
    
    <p>* PNR = Peso não registrado.</p>
</form>

<script language="javascript" type="text/javascript">
	daFoco("veiculo");
</script>
<? } ?>