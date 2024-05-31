<?
if (isset($_GET["pagina"])) $pagina= $_GET["pagina"];
else $pagina= $pagina;

if (strpos($pagina, "/")) {
	$parte_pagina= explode("/", $pagina);
	
	//if (file_exists("_". $parte[0] ."/". "__". $parte[1] .".php"))
		include("_". $parte_pagina[0] ."/". "__". $parte_pagina[1] .".php");
	//else
	//	echo "<h2>Erro</h2><p>Página não encontrada!</p>";
}
else {
	//if (file_exists("__". $paginar .".php"))
		include("__". $pagina .".php");
	//else
	//	echo "<h2>Erro</h2><p>Página não encontrada!</p>";
}
?>