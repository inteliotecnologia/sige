<?
require_once("conexao.php");
if (pode("rhv", $_SESSION["permissao"])) {
?>

<h2>Relat�rio quantitativo</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formul�rio de busca do per�odo</legend>
            
            <div class="parte50">
                <form action="index2.php?pagina=rh/quantitativo_relatorio" target="_blank" method="post" name="formAtestadoBuscar" id="formAtestadoBuscar">
                    
                    <input class="escondido" type="hidden" id="validacoes" value="" />
                    
                    <label for="periodo">* Per�odo:</label>
                    <select name="periodo" id="periodo" title="Per�odo">	  		
                        <?
                        $i=0;
                        $result_per= mysql_query("select distinct(DATE_FORMAT(data_batida, '%m/%Y')) as data_batida2 from rh_ponto order by data_batida desc ");
                        while ($rs_per= mysql_fetch_object($result_per)) {
                            $data_batida= explode('/', $rs_per->data_batida2);
							if ((date("d")>=25) && ($i==0)) {
								$proximo_mes= date("m/Y", mktime(0, 0, 0, $data_batida[0]+1, 1, $data_batida[1]));
								$data_batida2= explode('/', $proximo_mes);
						?>
						<option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $proximo_mes; ?>"><?= traduz_mes($data_batida2[0]) .'/'. $data_batida2[1]; ?></option>
						<? } ?>
                        ?>
                        <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_batida2; ?>"><?= traduz_mes($data_batida[0]) .'/'. $data_batida[1]; ?></option>
                        <? $i++; } ?>
                    </select>
                    <br />
                    
                    <label for="id_departamento">Departamento:</label>
                    <select name="id_departamento" id="id_departamento" title="Departamento">
                        <option value="">- TODOS -</option>
						<?
                        $result_dep= mysql_query("select * from rh_departamentos
                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                     ");
                        $i=0;
                        while ($rs_dep = mysql_fetch_object($result_dep)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                        <? $i++; } ?>
                    </select>
                    <br /><br />
                    
                    <label>&nbsp;</label>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                    <br />
                
                </form>
            </div>
        
    </fieldset>
</div>

<? } ?>