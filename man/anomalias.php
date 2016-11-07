<?php



// Busca todos os eventos que tem data de envio válido mas tem pedidos sem estado
$sql_evento = "SELECT ig_evento.idEvento,idPedidoContratacao FROM ig_evento,igsis_pedido_contratacao WHERE 
	ig_evento.dataEnvio IS NOT NULL 
	AND ig_evento.publicado ='1' 
	AND igsis_pedido_contratacao.idEvento = ig_evento.idEvento
	AND igsis_pedido_contratacao.publicado = 1
	AND (igsis_pedido_contratacao.estado IS NULL OR igsis_pedido_contratacao.estado = '')";
$query_evento = mysqli_query($con,$sql_evento);

$relatorio .= "<h2>Eventos que possuem data de envio válida mas tem pedidos sem status definido</h2>
<p>$sql_evento</p>


";
while($pedido = mysqli_fetch_array($query_evento)){
	$relatorio .= "Evento: ".$pedido['idEvento']."<br />";	
}

//Busca todos os pedidos que tem data de envio válido mas tem eventos como não enviados
$sql_evento = "SELECT ig_evento.idEvento,idPedidoContratacao FROM ig_evento,igsis_pedido_contratacao WHERE 
	 ig_evento.publicado ='1' 
	 AND igsis_pedido_contratacao.estado IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14)
	AND igsis_pedido_contratacao.idEvento = ig_evento.idEvento
	AND igsis_pedido_contratacao.publicado = '1'
	AND igsis_pedido_contratacao.tipoPessoa <> '4'
	AND (ig_evento.dataEnvio IS NULL OR ig_evento.dataEnvio = '')";
$query_evento = mysqli_query($con,$sql_evento);

$relatorio .= "<h2>Pedidos que possuem status válido mas tem eventos como não enviados</h2>
<p>$sql_evento</p>
";
while($pedido = mysqli_fetch_array($query_evento)){
	$relatorio .= "Pedido: ".$pedido['idPedidoContratacao']."<br />";	
}


//Busca eventos que estão na agenda mas não são válidos
$sql_agenda = "SELECT DISTINCT idEvento FROM igsis_agenda WHERE idEvento NOT IN
( SELECT idEvento FROM ig_evento WHERE ( dataEnvio IS NOT NULL ) OR 
( dataEnvio IS NULL AND ocupacao = 1))";
$query_agenda = mysqli_query($con,$sql_agenda);
$relatorio .= "<h2>Eventos que estão na Agenda mas não são válidos </h2>
<p>$sql_agenda</p>
";
while($agenda = mysqli_fetch_array($query_agenda)){
	$relatorio .= "Evento: ".$agenda['idEvento']."<br />";	
}

//Busca eventos que não estão na agenda 
$sql_agenda = "SELECT idEvento FROM ig_evento WHERE idEvento NOT IN ( SELECT DISTINCT idEvento FROM igsis_agenda ) AND dataEnvio IS NOT NULL AND (ocupacao IS NULL OR ocupacao = '') AND publicado = 1"; 
$query_agenda = mysqli_query($con,$sql_agenda);
$relatorio .= "<h2>Eventos que não estão na agenda</h2>
<p>$sql_agenda</p>
";
while($agenda = mysqli_fetch_array($query_agenda)){
	$relatorio .= "Evento: ".$agenda['idEvento']."<br />";	
}

//Pedidos de Contratação Aprovados por Finanças, mas não tiveram seu status alterado
$sql_financa = "SELECT * FROM `igsis_pedido_contratacao` WHERE `aprovacaoFinanca`= 1 AND `estado` = 1 AND `publicado`= 1 ORDER BY `idPedidoContratacao` DESC" ;
$query_financa = mysqli_query($con,$sql_financa);
$relatorio .= "<h2>Pedidos de Contratação aprovados por finança, mas não tiveram seu status alterado </h2>
<p>$sql_financa </p>
";
while($pedido = mysqli_fetch_array($query_evento)){
	$relatorio .= "Pedido: ".$pedido['idPedidoContratacao']."<br />";	
}


?>







