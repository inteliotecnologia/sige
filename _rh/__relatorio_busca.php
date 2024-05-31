<?
require_once("conexao.php");
if (pode("rhv", $_SESSION["permissao"])) {
?>

<h2>Central de relat&oacute;rios</h2>

<div class="parte50">
    <fieldset>
        <legend>Turnover</legend>
            
        <form action="index2.php?pagina=rh/ta_relatorio" target="_blank" method="post">
            
            <input class="escondido" type="hidden" id="validacoes" value="" />
            <input class="escondido" type="hidden" name="tipo_relatorio" value="t" />
            
            <label for="periodo">* Período:</label>
            <select name="periodo" id="periodo" title="Período">	  		
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
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_batida2; ?>"><?= traduz_mes($data_batida[0]) .'/'. $data_batida[1]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="id_departamento">Departamento:</label>
            <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '1');">
                <option value="">- TODOS -</option>
                <?
                $result_dep= mysql_query("select * from rh_departamentos
                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
											/* and   bate_ponto = '1' */
											order by departamento asc
                                             ");
                $i=0;
                while ($rs_dep = mysql_fetch_object($result_dep)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="id_turno">Turno:</label>
            <div id="id_turno_atualiza_1">
                <select name="id_turno" id="id_turno" title="Turno">	  		
                    <option value="">- TURNO -</option>
                    <?
                    $result_tur= mysql_query("select * from rh_turnos
                                                where id_departamento = '". $rs->id_departamento ."'
                                                 ");
                    $i=0;
                    while ($rs_tur = mysql_fetch_object($result_tur)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                    <? $i++; } ?>
                </select>
            </div>
            <br />
            <br />
            
            <label>&nbsp;</label>
            <button type="submit" id="enviar">Enviar &raquo;</button>
            <br />
        
        </form>
    </fieldset>
</div>
<div class="parte50">
    <fieldset>
        <legend>Absenteísmo</legend>
            
        <form action="index2.php?pagina=rh/ta_relatorio" target="_blank" method="post">
            
            <input class="escondido" type="hidden" id="validacoes" value="" />
            <input class="escondido" type="hidden" name="tipo_relatorio" value="a" />
            
            <label for="periodo">* Período:</label>
            <select name="periodo" id="periodo" title="Período">	  		
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
            <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '2');">
                <option value="">- TODOS -</option>
                <?
                $result_dep= mysql_query("select * from rh_departamentos
                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
											/* and   bate_ponto = '1' */
											order by departamento asc
                                             ");
                $i=0;
                while ($rs_dep = mysql_fetch_object($result_dep)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="id_turno">Turno:</label>
            <div id="id_turno_atualiza_2">
                <select name="id_turno" id="id_turno" title="Turno">	  		
                    <option value="">- TURNO -</option>
                    <?
                    $result_tur= mysql_query("select * from rh_turnos
                                                where id_departamento = '". $rs->id_departamento ."'
                                                 ");
                    $i=0;
                    while ($rs_tur = mysql_fetch_object($result_tur)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                    <? $i++; } ?>
                </select>
            </div>
            <br />
            <br />
            
            <label>&nbsp;</label>
            <button type="submit" id="enviar">Enviar &raquo;</button>
            <br />
        
        </form>
    </fieldset>
</div>
<br />
<div class="parte50">
    <fieldset>
        <legend>Faltas justificadas/não justificadas</legend>
            
        <form action="index2.php?pagina=rh/ta_relatorio" target="_blank" method="post">
            
            <input class="escondido" type="hidden" id="validacoes" value="" />
            <input class="escondido" type="hidden" name="tipo_relatorio" value="j" />
            
            <label for="periodo">* Período:</label>
            <select name="periodo" id="periodo" title="Período">	  		
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
            <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '3');">
                <option value="">- TODOS -</option>
                <?
                $result_dep= mysql_query("select * from rh_departamentos
                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
											/* and   bate_ponto = '1' */
											order by departamento asc
                                             ");
                $i=0;
                while ($rs_dep = mysql_fetch_object($result_dep)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="id_turno">Turno:</label>
            <div id="id_turno_atualiza_3">
                <select name="id_turno" id="id_turno" title="Turno">	  		
                    <option value="">- TURNO -</option>
                    <?
                    $result_tur= mysql_query("select * from rh_turnos
                                                where id_departamento = '". $rs->id_departamento ."'
                                                 ");
                    $i=0;
                    while ($rs_tur = mysql_fetch_object($result_tur)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                    <? $i++; } ?>
                </select>
            </div>
            <br />
            <br />
            
            <label>&nbsp;</label>
            <button type="submit" id="enviar">Enviar &raquo;</button>
            <br />
        
        </form>
    </fieldset>
</div>
<div class="parte50">
	<fieldset>
        <legend>Quantitativo geral</legend>
            
            <form action="index2.php?pagina=rh/quantitativo_relatorio" target="_blank" method="post" name="formAtestadoBuscar" id="formAtestadoBuscar">
                
                <input class="escondido" type="hidden" id="validacoes" value="" />
                
                <label for="periodo">* Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
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
                <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '4');">
                    <option value="">- TODOS -</option>
                    <?
                    $result_dep= mysql_query("select * from rh_departamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
												/* and   bate_ponto = '1' */
												order by departamento asc
                                                 ");
                    $i=0;
                    while ($rs_dep = mysql_fetch_object($result_dep)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza_4">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TURNO -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '". $rs->id_departamento ."'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br />
            
            </form>
    </fieldset>
</div>
<br />

<div class="parte50">
	<fieldset>
        <legend>Sem advertência/suspensão/atestado - Mensal</legend>
            
            <form action="index2.php?pagina=rh/ta_relatorio" target="_blank" method="post">
                
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="s" />
                
                <input class="escondido" type="hidden" name="periodicidade_relatorio" value="1" />
                
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
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
                
                <label>&nbsp;</label>
                ou
                <br />
                
                <label for="data1">Entre datas:</label>
                <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 1" />
                <div class="flutuar_esquerda espaco_dir">à</div>
                <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 2" />
                <br /><br />
                
                <label for="id_departamento">Departamento:</label>
                <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '5');">
                    <option value="">- TODOS -</option>
                    <?
                    $result_dep= mysql_query("select * from rh_departamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
												/* and   bate_ponto = '1' */
												order by departamento asc
                                                 ");
                    $i=0;
                    while ($rs_dep = mysql_fetch_object($result_dep)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza_5">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TURNO -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '". $rs->id_departamento ."'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                
                <fieldset>
                	<legend>Operações:</legend>
                    
                    <label for="advertencia">Advertência:</label>
                    <input type="checkbox"  class="tamanho15" name="advertencia" id="advertencia" value="1" />
                    <br /><br />
                    
                    <label for="suspensao">Suspensão:</label>
                    <input type="checkbox"  class="tamanho15" name="suspensao" id="suspensao" value="1" />
                    <br /><br />
                    
                    <label for="atestado">Atestado:</label>
                    <input type="checkbox"  class="tamanho15" name="atestado" id="atestado" value="1" />
                    <br /><br />
                    
                    <label for="pericia">Perícia:</label>
                    <input type="checkbox"  class="tamanho15" name="pericia" id="pericia" value="1" />
                    <br /><br />
                    
                    <label for="ferias">Férias:</label>
                    <input type="checkbox"  class="tamanho15" name="ferias" id="ferias" value="1" />
                    <br /><br />
                    
                    <label for="atestado">Faltas/atrasos:</label>
                    <input type="checkbox"  class="tamanho15" name="faltas" id="faltas" value="1" />
                    <span class="menor">(somente para opção "quem não tem")</span>
                    <br /><br />  
                    
                    
                </fieldset>
                
                <label for="modo">Modo:</label>
                <select name="modo" id="modo">
                	<option value="1">Quem não tem</option>
                    <option class="cor_sim" value="2">Quem tem</option>
                </select>
                <br /><br />

                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br />
            
            </form>
    </fieldset>
</div>
<div class="parte50">
	<fieldset>
        <legend>Sem advertência/suspensão/atestado - Anual</legend>
            
            <form action="index2.php?pagina=rh/ta_relatorio" target="_blank" method="post">
                
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="s" />
                
                <input class="escondido" type="hidden" name="periodicidade_relatorio" value="2" />
                
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_batida, '%Y')) as ano
                                                from rh_ponto order by data_batida desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                    ?>
                    <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->ano; ?>"><?= $rs_per->ano; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="periodo">Periodicidade:</label>
                <input type="radio" name="periodicidade" id="periodicidade_1" value="1" class="tamanho20" checked="checked" /> <label class="tamanho20 nao_negrito" for="periodicidade_1">1ºtri</label>
                <input type="radio" name="periodicidade" id="periodicidade_2" value="2" class="tamanho20" /> <label class="tamanho20 nao_negrito" for="periodicidade_2">2ºtri</label>
                <input type="radio" name="periodicidade" id="periodicidade_3" value="3" class="tamanho20" /> <label class="tamanho20 nao_negrito" for="periodicidade_3">3ºtri</label>
                <input type="radio" name="periodicidade" id="periodicidade_4" value="4" class="tamanho20" /> <label class="tamanho20 nao_negrito" for="periodicidade_4">4ºtri</label>	
                <input type="radio" name="periodicidade" id="periodicidade_a" value="a" class="tamanho20" /> <label class="tamanho20 nao_negrito" for="periodicidade_a">anual</label>	
				<br /><br />
                
                <label for="id_departamento">Departamento:</label>
                <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '6');">
                    <option value="">- TODOS -</option>
                    <?
                    $result_dep= mysql_query("select * from rh_departamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
												/* and   bate_ponto = '1' */
												order by departamento asc
                                                 ");
                    $i=0;
                    while ($rs_dep = mysql_fetch_object($result_dep)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza_6">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TURNO -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '". $rs->id_departamento ."'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                
                <fieldset>
                	<legend>Operações:</legend>
                    
                    <label for="advertencia">Advertência:</label>
                    <input type="checkbox"  class="tamanho15" name="advertencia" id="advertencia" value="1" />
                    <br /><br />
                    
                    <label for="suspensao">Suspensão:</label>
                    <input type="checkbox"  class="tamanho15" name="suspensao" id="suspensao" value="1" />
                    <br /><br />
                    
                    <label for="atestado">Atestado:</label>
                    <input type="checkbox"  class="tamanho15" name="atestado" id="atestado" value="1" />
                    <br /><br />
                    
                    <label for="pericia">Perícia:</label>
                    <input type="checkbox"  class="tamanho15" name="pericia" id="pericia" value="1" />
                    <br /><br />
                    
                    <label for="ferias">Férias:</label>
                    <input type="checkbox"  class="tamanho15" name="ferias" id="ferias" value="1" />
                    <br /><br />
                    
                    <label for="atestado">Faltas/atrasos:</label>
                    <input type="checkbox"  class="tamanho15" name="faltas" id="faltas" value="1" />
                    <span class="menor">(somente para opção "quem não tem")</span>
                    <br /><br />  
                    
                </fieldset>
                
                <label for="modo">Modo:</label>
                <select name="modo" id="modo">
                	<option value="1">Quem não tem</option>
                    <option class="cor_sim" value="2">Quem tem</option>
                </select>
                <br /><br />

                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br />
            
            </form>
    </fieldset>
</div>
<br />

<div class="parte50">
	<fieldset>
        <legend>Faltas por dia</legend>
            
            <form action="index2.php?pagina=rh/ta_relatorio" target="_blank" method="post" name="formAtestadoBuscar" id="formAtestadoBuscar">
                
                <input class="escondido" type="hidden" id="validacoes" value="data@vazio" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="fd" />
                
                <label for="data">* Data:</label>
                <input title="Data" name="data" id="data" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="" maxlength="10" />
                <br />
                
                <label for="id_departamento">Departamento:</label>
                <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '7');">
                    <option value="">- TODOS -</option>
                    <?
                    $result_dep= mysql_query("select * from rh_departamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
												/* and   bate_ponto = '1' */
												order by departamento asc
                                                 ");
                    $i=0;
                    while ($rs_dep = mysql_fetch_object($result_dep)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza_7">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TURNO -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '". $rs->id_departamento ."'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br />
            
            </form>
    </fieldset>
</div>

<div class="parte50">
	<fieldset>
        <legend>Atrasos por dia</legend>
            
            <form action="index2.php?pagina=rh/ta_relatorio" target="_blank" method="post" name="formAtestadoBuscar" id="formAtestadoBuscar">
                
                <input class="escondido" type="hidden" id="validacoes" value="data@vazio" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="ad" />
                
                <label for="data">* Data:</label>
                <input title="Data" name="data" id="data" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="" maxlength="10" />
                <br />
                
                <label for="id_departamento">Departamento:</label>
                <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '8');">
                    <option value="">- TODOS -</option>
                    <?
                    $result_dep= mysql_query("select * from rh_departamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
												/* and   bate_ponto = '1' */
												order by departamento asc
                                                 ");
                    $i=0;
                    while ($rs_dep = mysql_fetch_object($result_dep)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza_8">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TURNO -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '". $rs->id_departamento ."'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br />
            
            </form>
    </fieldset>
</div>
<br />

<div class="parte50">
	<fieldset>
        <legend>Horas extras por dia</legend>
            
            <form action="index2.php?pagina=rh/ta_relatorio" target="_blank" method="post" name="formAtestadoBuscar" id="formAtestadoBuscar">
                
                <input class="escondido" type="hidden" id="validacoes" value="data@vazio" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="hd" />
                
                <label for="data">* Data:</label>
                <input title="Data" name="data" id="data" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="" maxlength="10" />
                <br />
                
                <label for="id_departamento">Departamento:</label>
                <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '9');">
                    <option value="">- TODOS -</option>
                    <?
                    $result_dep= mysql_query("select * from rh_departamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
												/* and   bate_ponto = '1' */
												order by departamento asc
                                                 ");
                    $i=0;
                    while ($rs_dep = mysql_fetch_object($result_dep)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza_9">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TURNO -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '". $rs->id_departamento ."'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br />
            
            </form>
    </fieldset>
</div>
<div class="parte50">
	<fieldset>
        <legend>Horas extras por mês</legend>
            
            <form action="index2.php?pagina=rh/ta_relatorio" target="_blank" method="post" name="formAtestadoBuscar" id="formAtestadoBuscar">
                
                <input class="escondido" type="hidden" id="validacoes" value="data@vazio" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="hm" />
                
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
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
                
                <label>&nbsp;</label>
                ou
                <br />
                
                <label for="data1">Entre datas:</label>
                <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 1" />
                <div class="flutuar_esquerda espaco_dir">à</div>
                <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 2" />
                <br /><br />
                
                <label for="id_departamento">Departamento:</label>
                <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '10');">
                    <option value="">- TODOS -</option>
                    <?
                    $result_dep= mysql_query("select * from rh_departamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
												/* and   bate_ponto = '1' */
												order by departamento asc
                                                 ");
                    $i=0;
                    while ($rs_dep = mysql_fetch_object($result_dep)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza_10">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TURNO -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '". $rs->id_departamento ."'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br />
            
            </form>
    </fieldset>
</div>

<fieldset>
	<legend>Mês a mês</legend>
        
    <div class="parte33">
        <fieldset>
            <legend>Turnover</legend>
                
            <form action="index2.php?pagina=rh/ta_relatorio" target="_blank" method="post">
                
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="mmt" />
                
                <label for="ano">Ano:</label>
                <select name="ano" id="ano" title="Ano">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_batida, '%Y')) as ano
                                                from rh_ponto order by data_batida desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                    ?>
                    <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->ano; ?>"><?= $rs_per->ano; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_departamento">Departamento:</label>
                <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '11');">
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
                <br />
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza_11">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TURNO -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '". $rs->id_departamento ."'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br />
            
            </form>
        </fieldset>
    </div>
    <div class="parte33">
        <fieldset>
            <legend>Absenteísmo</legend>
                
            <form action="index2.php?pagina=rh/ta_relatorio" target="_blank" method="post">
                
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="mma" />
                
                <label for="ano">Ano:</label>
                <select name="ano" id="ano" title="Ano">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_batida, '%Y')) as ano
                                                from rh_ponto order by data_batida desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                    ?>
                    <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->ano; ?>"><?= $rs_per->ano; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_departamento">Departamento:</label>
                <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '12');">
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
                <br />
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza_12">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TURNO -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '". $rs->id_departamento ."'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br />
            
            </form>
        </fieldset>
    </div>
    <div class="parte33">
        <fieldset>
            <legend>Faltas justificadas/não justificadas</legend>
                
            <form action="index2.php?pagina=rh/ta_relatorio" target="_blank" method="post">
                
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="mmj" />
                
                <label for="ano">Ano:</label>
                <select name="ano" id="ano" title="Ano">  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_batida, '%Y')) as ano
                                                from rh_ponto order by data_batida desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                    ?>
                    <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->ano; ?>"><?= $rs_per->ano; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_departamento">Departamento:</label>
                <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '13');">
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
                <br />
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza_13">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TURNO -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '". $rs->id_departamento ."'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br />
            
            </form>
        </fieldset>
    </div>

</fieldset>

<div class="parte50">
	<fieldset>
        <legend>Faltas discriminadas</legend>
            
            <form action="index2.php?pagina=rh/ta_relatorio" target="_blank" method="post">
                
                <input class="escondido" type="hidden" id="validacoes" value="data@vazio" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="fdis" />
                
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
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
                
                <label>&nbsp;</label>
                ou
                <br />
                
                <label for="data1">Entre datas:</label>
                <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 1" />
                <div class="flutuar_esquerda espaco_dir">à</div>
                <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 2" />
                <br /><br />
                
                <label for="id_departamento">Departamento:</label>
                <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '14');">
                    <option value="">- TODOS -</option>
                    <?
                    $result_dep= mysql_query("select * from rh_departamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
												/* and   bate_ponto = '1' */
												order by departamento asc
                                                 ");
                    $i=0;
                    while ($rs_dep = mysql_fetch_object($result_dep)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza_14">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TURNO -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '". $rs->id_departamento ."'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br />
            
            </form>
    </fieldset>
</div>

<? } ?>