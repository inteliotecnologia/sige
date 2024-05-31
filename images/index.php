<?
require_once("conexao.php");
define(CAMINHO2, "./fotos/");

if (!isset($_GET["pagina"]))
	$pagina= "principal";
else
	$pagina= $_GET["pagina"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>::: Corbetta Constru&ccedil;&otilde;es :::</title>
<link rel="stylesheet" type="text/css" media="screen" href="estilo.css" />

 	<!--[if IE 6]>
		<link rel="stylesheet" type="text/css" media="screen" href="estilo_ie6.css" />
	<![endif]-->
    
    <!--[if IE 7]>
		<link rel="stylesheet" type="text/css" media="screen" href="estilo_ie7.css" />
	<![endif]-->

   	<link rel="stylesheet" href="lightbox.css" type="text/css" media="screen" />

    <script src="js/prototype.js" type="text/javascript"></script>
    <script src="js/scriptaculous.js?load=effects" type="text/javascript"></script>
    <script src="js/lightbox.js" type="text/javascript"></script>

    <script src="Scripts/AC_RunActiveContent.js" type="text/javascript"></script>


<body>

<div id="corpo">
<! ------------------------------------------- TOPO  ------------- !>
	<div id="topo">
   	  <div id="menu">
			<ul id="menu_horizontal">
		 	<li><a href=#>CORBETTA</a></li>
          	<li><a href=#>VENDAS</a></li>
            <li><a href="?pagina=itens&t=3">LANÇAMENTOS</a></li>
          	<li><a href="?pagina=itens&amp;t=2">OBRAS</a></li>
         	<li><a href="?pagina=itens&amp;t=1">REALIZADOS</a></li>
          	<li><a href=?pagina=contato>CONTATO</a></li>
	  </ul>	</div>
        <div id="logo"><a href="index.php"><img src="site/index_css/logo.jpg" border="0" /></a></div>
<div id="lado_direito_topo">
        		
                <div id="icones">
        			<div id="home_icone"><a href="?pagina=principal"><img src="site/index_css/icone_home.jpg" border="0" /></a></div>
        
       	  			<div id="email_icone"><a href="javascript:history.back(-1)"><img src="site/index_css/icone_email.jpg" border="0"/></a></div>
            
       		  </div>
                
                <div id="busca_rapida">
                	
                    <form method="get" action="./">
                    	<input type="hidden" name="pagina" value="itens" />
                        
                        <div id="tipo"><label for="t">Tipo:</label>
                          <select name="t" id="t">
                            <option value=""><h4>--- selecione ---</h4></option>
                            <?
							$vetor= pega_tipo_obra('l');
							$i=1;
							while ($vetor[$i]) {
							?>
                            <option value="<?= $i; ?>" <? if ($_GET["t"]==$i) echo "selected=\"selected\""; ?>>
                              <?= $vetor[$i]; ?>
                            </option>
                            <?
							$i++;
							}
							?>
                          </select>
                        </div>
                        <div id="cidade">
                        <label for="id_cidade">Cidade:</label>
                        <select name="id_cidade" id="id_cidade">
                          <option value="">--- selecione ---</option>
                          <?
                            $result_cid= mysql_query("select id_cidade, cidade, uf from cidades, ufs
                                                        where cidades.id_uf = ufs.id_uf
                                                        order by cidade");
                            $i= 0;
                            while ($rs_cid= mysql_fetch_object($result_cid)) {
                          ?>
                          <option <? if (($i%2)==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cid->id_cidade; ?>"  <? if ($_GET["id_cidade"]==$rs_cid->id_cidade) echo "selected=\"selected\""; ?>><?= $rs_cid->cidade ." - ". $rs_cid->uf; ?></option>
                          <? $i++; } ?>
                        </select></div>
                        
                        
                        <div id="ok"><input type="image" src="site/index_css/botao_buscar.gif" /></div>
                    </form>
                </div> 
        	</div>
    </div>
<! ------------------------------------------- TOPO  FIM ------------- !> 


<! ------------------------------------------- CONTEUDO  ------------- !>
    <div id="meio">
    <? include($pagina .".php"); ?>
    
   
    </div>

<! ------------------------------------------- CONTEUDO  FIM ------------- !>

</div>
<! ------------------------------------------- RODAPE  ------------- !>
    <div id="rodape">
    	<div id="menu_rodape">
    		<ul id="menu_horizontal">
          	<li><a href=#><h3>corbetta</h3></a></li>
          	<li><a href=#><h3>vendas</h3></a></li>
          	<li><a href=#><h3>obras</h3></a></li>
         	<li><a href=#><h3>realizados</h3></a></li>
          	<li><a href=#><h3>contato</h3></a></li>
	  		</ul>
    	</div>
        <div id="autoridade"> <h4>Direitos Reservados - Corbetta Construções 2007</h4> </div>
      	 <div id="lgweb"><a href="http://www.lgweb.com.br/"><img src="site/index_css/logo_lgweb.jpg" border="0" /></a></div>
    </div>
<! ------------------------------------------- RODAPE  FIM ------------- !>


</div>
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-2478879-1";
urchinTracker();
</script>

</body>
</html>
