<?php

if(isset($_GET['p'])){
	$p = $_GET['p'];	
}else{
	$p = 'inicial';
}


// Cria uma função que retorna o timestamp de uma data no formato DD/MM/AAAA
function geraTimestamp($data) {
$partes = explode('/', $data);
return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}

require "../funcoes/funcoesAdministrador.php"; //chamar funcoes do administrador
require "../funcoes/funcoesSiscontrat.php"; //chamar funcoes do administrador

?>
<!--    				<a href="?perfil=administrador&atualizar=agenda" class="btn btn-theme btn-lg btn-block">Atualizar agenda</a> -->
	<div class="menu-area">
			<div id="dl-menu" class="dl-menuwrapper">
						<button class="dl-trigger">Open Menu</button>
						<ul class="dl-menu">
							<li>
								<a href="?perfil=admin&p=visaogeral">Usuários ativos</a>
							</li>
   							<li><a href="?perfil=admin&p=estatistica"> Estatística</a></li>
                            <li><a href="?perfil=admin&p=reabertura"> Reabrir eventos enviados</a></li>
   							<li><a href="?perfil=admin&p=scripts"> Scripts</a></li>
							<li><a href="?perfil=admin&p=email">E-mail de reabertura</a></li>
   							<li><a href="?perfil=admin&p=contratos">Contratos</a></li>
							<li><a href="?secao=perfil">Carregar módulo</a></li>
							<li><a href="?secao=ajuda">Ajuda</a></li>
                            <li><a href="../include/logoff.php">Sair</a></li>
							<!--<li>
								<a href="#">Sub Menu</a>
								<ul class="dl-submenu">
									<li><a href="#">Sub menu</a></li>
									<li><a href="#">Sub menu</a></li>
								</ul>
							</li>-->
						</ul>
					</div><!-- /dl-menuwrapper -->
	</div>	
<?php
switch($p){
case "inicial":	
	
?>
<section id="contact" class="home-section bg-white">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-2 col-md-8">
                <div class="text-hide">
                <h2>Administrador do Sistema</h2>
	                <h5>Escolha uma opção</h5>
                </div>
            </div>
        <div class="form-group">
            <div class="col-md-offset-2 col-md-8">
	            <a href="?perfil=admin&p=visaogeral" class="btn btn-theme btn-lg btn-block">Usuários ativos</a>
   	            <a href="?perfil=admin&p=estatistica" class="btn btn-theme btn-lg btn-block">Estatísticas do Sistema</a>
                <a href="?perfil=admin&p=reabertura" class="btn btn-theme btn-lg btn-block">Reabrir eventos enviados</a>
                <a href="?perfil=admin&p=scripts" class="btn btn-theme btn-lg btn-block">Scripts</a>
				<a href="?perfil=admin&p=email" class="btn btn-theme btn-lg btn-block">E-mail para reabertura</a>
                <a href="?perfil=admin&p=sof" class="btn btn-theme btn-lg btn-block">Integração SOF / IGSIS</a>

	            <!--<a href="?perfil=busca&p=pedidos" class="btn btn-theme btn-lg btn-block">Pedidos de contratação</a>-->

            </div>
          </div>
        </div>
    </div>
</section>  

<?php break; 
case "email":
?>

<?php

$con = bancoMysqli();
$sql = "SELECT DISTINCT idEvento, ig_log_reabertura.data FROM igsis_agenda 
INNER JOIN ig_log_reabertura ON idEveForm = idEvento
WHERE idEvento NOT IN ( SELECT idEvento FROM ig_evento WHERE ( dataEnvio IS NOT NULL ) OR ( dataEnvio IS NULL AND ocupacao = 1))";
$query = mysqli_query($con,$sql);


?>
<section id="inserir" class="home-section bg-white">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-2 col-md-8">
			
				<?php
				$i = 0;
				while($email = mysqli_fetch_array($query))
				 {	
					$evento = recuperaDados("ig_evento",$email['idEvento'],"idEvento");
					$usuario = recuperaDados("ig_usuario", $evento['idResponsavel'],"idUsuario");
					$data = exibirDataHoraBr($email['data']);
					echo "<p align='left'>Bom dia!</p><p align='left'>Informamos que sua ig de número <font color='red'><strong>".$email['idEvento']."</strong></font> <strong>(".$evento['nomeEvento'].")</strong> foi reaberta no dia <font color='red'><strong>".$data."</strong></font> e não foi reenviada. Solicitamos que o reenvio seja feito, pois o evento sairá da agenda e só retornará após o mesmo.</p>
					<p align='left'>Att.</p>
					<p>Responsável: ".$usuario['email']."</p>";
					echo "<p>-----------------------------</p>";
					$i++;
				}	
				?>
            </div>
		</div>
	</div>
</section>  

<?php break; //FIM EMAIL
case "estatistica":
?>

<section id="list_items" class="home-section bg-white">
		 <div class="form-group">
            <div class="col-md-offset-2 col-md-8">		
			<h2>Estatística do Sistema</h2>
			
  	        </div>
				</div> 
		<div class="container">
      			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="section-heading">
					
					</div> <h5><?php if(isset($mensagem)){echo $mensagem;} ?></h5>
					</div>
				  </div>
				  
			<div class="table-responsive list_info">

           
           <table class='table table-condensed'>
					<thead>	
                    <!--				
					<tr class='list_menu'> 
							<td></td>
							<td></td>

						 </tr>	-->
					</thead>
					<tbody>
<tr>
<td>Usuários ativos no último mês</td>
<td>
<?php 
	$trinta_dias = date('Y-m-d H:i:s', strtotime("-30 days"));
	$sql_usuarios = "SELECT DISTINCT ig_usuario_idUsuario FROM ig_log WHERE dataLog > '$trinta_dias'";
	$query_usuarios = mysqli_query($con,$sql_usuarios);
	$num_usuarios = mysqli_num_rows($query_usuarios);

?> 
<?php echo $num_usuarios ?>
</td>
</tr>

<tr>
<td>Eventos enviados</td>
<td>
    <?php
	$con = bancoMysqli();
	$sql_eventos = "SELECT * FROM ig_evento WHERE publicado ='1' AND dataEnvio IS NOT NULL";
	$query_eventos = mysqli_query($con,$sql_eventos);
	$num_eventos = mysqli_num_rows($query_eventos);

	$sql_pedidos = "SELECT * FROM igsis_pedido_contratacao WHERE publicado = '1' AND estado IS NOT NULL";
	$query_pedidos = mysqli_query($con,$sql_pedidos);
	$num_pedidos = mysqli_num_rows($query_pedidos);
	?>
<?php echo $num_eventos ?>
</td>
</tr>
<tr>
<td>Média de eventos enviados por dia</td>
<td>
 <?php
$data_inicial = '04/01/2016';
$data_final = date('d/m/Y');

// Usa a função criada e pega o timestamp das duas datas:
$time_inicial = geraTimestamp($data_inicial);
$time_final = geraTimestamp($data_final);
// Calcula a diferença de segundos entre as duas datas:
$diferenca = $time_final - $time_inicial; // 19522800 segundos
// Calcula a diferença de dias
$dias = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
// Exibe uma mensagem de resultado:
$media_eventos  = (int)($num_eventos/$dias);
?>
<?php echo $media_eventos ?>
</td>
</tr>

<tr>
<td>Pedidos de contratação </td>
<td>

<?php echo $num_pedidos ?></td>
</tr>
</tr>

<tr>
<td>Média de eventos enviados por dia</td>
<td>
 <?php
$data_inicial = '04/01/2016';
$data_final = date('d/m/Y');

// Usa a função criada e pega o timestamp das duas datas:
$time_inicial = geraTimestamp($data_inicial);
$time_final = geraTimestamp($data_final);
// Calcula a diferença de segundos entre as duas datas:
$diferenca = $time_final - $time_inicial; // 19522800 segundos
// Calcula a diferença de dias
$dias = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
// Exibe uma mensagem de resultado:
$media_pedidos  = (int)($num_pedidos/$dias);
?>
<?php echo $media_pedidos ?>
</td>
</tr>

<tr>
<td>Reaberturas de IG (Desde 13/04/2016)</td>
<td>
 <?php
 $sql_log = "SELECT * FROM ig_log WHERE  `descricao` LIKE  '%SET dataEnvio = NULL%'";
$query_log = mysqli_query($con,$sql_log);
$num_log = mysqli_num_rows($query_log);

$data_inicial = '13/04/2016';
$data_final = date('d/m/Y');

// Usa a função criada e pega o timestamp das duas datas:
$time_inicial = geraTimestamp($data_inicial);
$time_final = geraTimestamp($data_final);
// Calcula a diferença de segundos entre as duas datas:
$diferenca = $time_final - $time_inicial; // 19522800 segundos
// Calcula a diferença de dias
$dias = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
// Exibe uma mensagem de resultado:
$media  = (int)($num_log/$dias);
?>
<?php echo $num_log ?> 
</td>
</tr>

<tr>
<td> Média por dia de Reaberturas de IG</td>
<td>
 <?php
 $sql_log = "SELECT * FROM ig_log WHERE  `descricao` LIKE  '%SET dataEnvio = NULL%'";
$query_log = mysqli_query($con,$sql_log);
$num_log = mysqli_num_rows($query_log);

$data_inicial = '13/04/2016';
$data_final = date('d/m/Y');

// Usa a função criada e pega o timestamp das duas datas:
$time_inicial = geraTimestamp($data_inicial);
$time_final = geraTimestamp($data_final);
// Calcula a diferença de segundos entre as duas datas:
$diferenca = $time_final - $time_inicial; // 19522800 segundos
// Calcula a diferença de dias
$dias = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
// Exibe uma mensagem de resultado:
$media  = (int)($num_log/$dias);
?>
<?php echo $media?></td>
</tr>
<tr>
<td> Média de eventos enviados por usuário</td>
<td>
 <?php
$sql_usuarios = "SELECT DISTINCT idUsuario FROM ig_evento WHERE publicado = '1' AND (dataEnvio IS NOT NULL OR dataEnvio <> '')";
$query_usuarios = mysqli_query($con,$sql_usuarios);
$num_usuarios = mysqli_num_rows($query_usuarios);

$data_inicial = '13/04/2016';
$data_final = date('d/m/Y');

// Usa a função criada e pega o timestamp das duas datas:
$time_inicial = geraTimestamp($data_inicial);
$time_final = geraTimestamp($data_final);
// Calcula a diferença de segundos entre as duas datas:
$diferenca = $time_final - $time_inicial; // 19522800 segundos
// Calcula a diferença de dias
$dias = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
// Exibe uma mensagem de resultado:
$media  = (int)($num_eventos/$num_usuarios);
?>
<?php echo $media?></td>
</tr>
<tr>
<td>  Média de Ações/apresentações/acontecimentos<br /> agendados por dia até o final do ano</td>
<td>
 <?php
$sql_final = "SELECT data FROM igsis_agenda ORDER BY data DESC LIMIT 0,1";
$query_final = mysqli_query($con,$sql_final);
$data = mysqli_fetch_array($query_final);

$data_final = exibirDataBr($data['data']);
$data_inicial = "01/01/2016";

$sql_total = "SELECT data FROM igsis_agenda";
$query_total = mysqli_query($con,$sql_total);
$num_total = mysqli_num_rows($query_total);

// Usa a função criada e pega o timestamp das duas datas:
$time_inicial = geraTimestamp($data_inicial);
$time_final = geraTimestamp($data_final);
// Calcula a diferença de segundos entre as duas datas:
$diferenca = $time_final - $time_inicial; // 19522800 segundos
// Calcula a diferença de dias
$dias = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
// Exibe uma mensagem de resultado:
$media  = (int)($num_total/$dias);
?>
<?php echo $media?></td>
</tr>

<tr>
<td> Média de Ações/apresentações/acontecimentos realizados até hoje</td>
<td>
 <?php
 


$data_inicial = "01/01/2016";
$hoje = date('Y-m-d');
$sql_total = "SELECT data FROM igsis_agenda WHERE data <= '$hoje'";
$query_total = mysqli_query($con,$sql_total);
$num_total = mysqli_num_rows($query_total);

// Usa a função criada e pega o timestamp das duas datas:
$time_inicial = geraTimestamp($data_inicial);
$time_final = geraTimestamp(exibirDataBr($hoje));
// Calcula a diferença de segundos entre as duas datas:
$diferenca = $time_final - $time_inicial; // 19522800 segundos
// Calcula a diferença de dias
$dias = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
// Exibe uma mensagem de resultado:
$media  = (int)($num_total/$dias);
?>
<?php echo $media?></td>
</tr>
         
                        </tbody>
				</table>

			</div>
		</div>
</section>


<?php break; 
case "visaogeral":
?>
<section id="inserir" class="home-section bg-white">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-2 col-md-8">
                <div class="text-hide">
                    <h3>Administração Geral do Sistema</h3>
                </div>
            </div>
    </div>
    <div class="row">
        <div class="col-md-offset-1 col-md-10">
           					<h5>Usuários ativos</h5>
			                <div class="form-group">

                            <?php 
							// Defino a hora com a qual vou trabalhar
							$agora = date('Y-m-d H:i:s');
							// Somo 5 minutos (resultado em int)
							//$horaNova = strtotime("$hora + 30 minutes");
							// Formato o resultado
							//$horaNovaFormatada = date("H:i:s",$horaNova);
							// Mostro na tela
							$con = bancoMysqli();
							$sql_user = "SELECT DISTINCT idUsuario, ip, time FROM igsis_time";
							$query_user = mysqli_query($con,$sql_user);
							while($x = mysqli_fetch_array($query_user)){
								 
								$usuario = recuperaDados("ig_usuario",$x['idUsuario'],"idUsuario");
								$hora = strtotime($x['time']);
								$novaHora = strtotime('+30 minute',$hora);
								$agora = strtotime(date('H:m:i'));
								if($novaHora > $agora){				
								?>                            
									<p><?php echo $usuario['nomeCompleto'] ?> pelo IP: <?php echo $x['ip']; ?> às <?php echo $x['time']; ?> </p>
		
								<?php
									}
								}
								
							?>
                            </div>
                            		
    
    </div>
</section>  
<?php	
break; // FIM EVENTOS
case "reabertura": // VISUALIZAR REABERTURA DE IGSIS


if(isset($_POST['apagar'])){
	$idEvento = $_POST['apagar'];
	$sql_reabrir = "UPDATE ig_evento SET publicado = '0' WHERE idEvento = '$idEvento'";
	$query_reabrir = mysqli_query($con,$sql_reabrir);
	if($query_reabrir){
		$sql_pedido = "UPDATE igsis_pedido_contratacao SET publicado = '0' WHERE idEvento = '$idEvento'";
		$query_pedido = mysqli_query($con,$sql_pedido);
		if($query_pedido){
			$evento = recuperaDados("ig_evento",$idEvento,"idEvento");
			$mensagem = "Evento ".$evento['nomeEvento']."($idEvento) apagado com sucesso";	
		}
	} 
	
}



if(isset($_POST['reabertura'])){
	$idEvento = $_POST['reabertura'];
	$mensagem = "";
	$sql_reabrir = "UPDATE ig_evento SET dataEnvio = NULL WHERE idEvento = '$idEvento'";
	$query_reabrir = mysqli_query($con,$sql_reabrir);
	if($query_reabrir){
		$evento = recuperaDados("ig_evento",$idEvento,"idEvento");
		$mensagem = $mensagem."O evento ".$evento['nomeEvento']." foi reaberto.<br />";
		$sql_pedido = "UPDATE igsis_pedido_contratacao SET estado = NULL WHERE idEvento = '$idEvento'";
		$query_pedido = mysqli_query($con,$sql_pedido);
		if($query_pedido){
			$mensagem = $mensagem."Os pedidos foram reabertos.<br />";
			$sql_recupera_pedidos_abertos = "SELECT * FROM igsis_pedido_contratacao WHERE publicado = '1' AND idEvento = $idEvento AND estado IS NULL";
			$query_recupera_pedidos_abertos = mysqli_query($con,$sql_recupera_pedidos_abertos);
			$n_recupera = mysqli_num_rows($query_recupera_pedidos_abertos);
			if($n_recupera > 0){
				$mensagem = "O evento ".$evento['nomeEvento']."foi reaberto.";
				$pedido = "";
				while($x = mysqli_fetch_array($query_recupera_pedidos_abertos)){
					$pedidos = $pedidos." ".$x['idPedidoContratacao'].","; 	
				}
				$conteudo_email = "
				Olá,<br />
				Por solicitação, o(s) pedido(s) ".trim(substr($pedidos,0,-1))." foi(foram) reaberto(s) e não aparecerá(ão) em suas listas no Módulo Contratação até que seja(m) reenviado(s).<br /><br />
				Att,<br />
				Equipe IGSIS<br />
				";
				$instituicao = 4;
				$subject = "O evento '".$evento['nomeEvento']."' foi reaberto";
				$email = "sistema.igsis@gmail.com";
				$usuario = "IGSIS";
				
					
				$email_envia = enviarEmailContratos($conteudo_email, $instituicao, $subject, $email, $idEvento);
			}
			if($email_envia){
				$mensagem = $mensagem."<br />Foram enviadas notificações à área de Contratos.";	
			}	
			
		}
	} 
	
}

						if(isset($_GET['order'])){
							switch($_GET['order']){
						
							case "dataEnvio":
								$order = " ORDER BY dataEnvio DESC";
								$mensagem .= "<br /> Ordenados pelas últimas datas de envio.<br />(Reaberturas de IGs geram novas datas de envio mas não Números de Evento.)";	
							break;
						
							case "idEvento":
								$order = " ORDER BY idEvento DESC";	
								$mensagem .= "<br /> Ordenados pelo número de Evento";	
					
							
								
							}	
						}else{
							$order = " ORDER BY idEvento DESC ";	
							$mensagem .= "<br /> Ordenados pelo últimos números de Evento";	

						}


?>
<section id="list_items" class="home-section bg-white">
		 <div class="form-group">
            <div class="col-md-offset-2 col-md-8">		
			<h2>Lista de eventos</h2>
			
  	        </div>
				</div> 
		<div class="container">
      			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="section-heading">
					
					</div> <h5><?php if(isset($mensagem)){echo $mensagem;} ?></h5>
					</div>
				  </div>
				  
			<div class="table-responsive list_info">
<?php 

						$idInsituicao = $_SESSION['idInstituicao'];
						$sql_lista = "SELECT * FROM ig_evento WHERE publicado = '1' AND dataEnvio IS NOT NULL $order";
						$query_lista = mysqli_query($con,$sql_lista);
						$num = mysqli_num_rows($query_lista);
?>
			<h5><?php echo $num ?> eventos enviados.</h5>
            <p><a href="?perfil=admin&p=reabertura">Ordenar pelos últimos Números de Evento</a> | <a href="?perfil=admin&p=reabertura&order=dataEnvio">Ordenar pelas últimas datas de envio</a></p>
            <table class='table table-condensed'>
					<thead>					
					<tr class='list_menu'> 
							<td>ID</td>
							<td>Evento</td>
  							<td>Tipo</td>
                            <td>Instituição</td>
							<td>Data/Período</td>
                            <td>Pedido</td>
                            <td width="7%"></td>
                            <td width="7%"></td>
                            <td width="7%"></td>
						 </tr>	
					</thead>
					<tbody>
                        <?php 
						

						while($campo = mysqli_fetch_array($query_lista)){
		$protocolo = recuperaDados("ig_protocolo",$campo['idEvento'],"ig_evento_idEvento");
		$chamado = recuperaAlteracoesEvento($campo['idEvento']);
		$instituicao = recuperaDados("ig_instituicao",$campo['idInstituicao'],"idInstituicao");	
			echo "<tr>";
			echo "<td class='list_description'><a href='?perfil=detalhe&evento=".$campo['idEvento']."' target='_blank'>".$campo['idEvento']."</a>
			</td>";
			echo "<td class='list_description'>".$campo['nomeEvento']." ["; 
			if($chamado['numero'] == '0'){
				echo "0";
			}else{
			echo "<a href='?perfil=chamado&p=evento&id=".$campo['idEvento']."' target='_blank'>".$chamado['numero']."</a>";	
			}
				
			echo "]</td>";
			echo "<td class='list_description'>".retornaTipo($campo['ig_tipo_evento_idTipoEvento'])."</td>";
			echo "<td class='list_description'>".$instituicao['instituicao']."</td>";
			echo "<td class='list_description'>".retornaPeriodo($campo['idEvento'])."</td>";
			echo "<td class='list_description'>".substr(retornaPedidos($campo['idEvento']),7)."</td>";
			echo "<td class='list_description'>
			<form method='POST' action='?perfil=admin&p=reabertura'>
			<input type='hidden' name='reabertura' value='".$campo['idEvento']."' >
			<input type ='submit' class='btn btn-theme  btn-block' value='reabrir'></td></form>"	;
			echo "<td class='list_description'>
			<form method='POST' action='?perfil=administrador&p=reabertura'>
			<input type='hidden' name='apagar' value='".$campo['idEvento']."' >
			<input type ='submit' class='btn btn-theme  btn-block' value='Apagar'></td></form>"	;
			echo "<td class='list_description'>
			<form method='POST' action='?perfil=evento&p=basica' target='_blank'>
			<input type='hidden' name='carregar' value='".$campo['idEvento']."' >
			<input type ='submit' class='btn btn-theme  btn-block' value='Carregar'></td></form>"	;
			echo "</tr>";	
						}
?>
                        </tbody>
				</table>
			</div>
		</div>
</section>
<?php 
break;
case "contratos":

if(!isset($_POST['id_ped'])){
?>

	 <!-- Contact -->
	  <section id="contact" class="home-section bg-white">
	  	<div class="container">
			  <div class="form-group">
					<div class="sub-title"><h2>Digite o Número do Pedido</h2></div>
			  </div>

	  		<div class="row">
	  			<div class="col-md-offset-1 col-md-10">

				<form class="form-horizontal" role="form" action="?perfil=admin&p=contratos" method="post">
				 <div class="form-group">
					<div class="col-md-offset-2 col-md-8"><br/>
					  <input type="text" class="form-control" id="id_ped" name="id_ped">
					</div>
				  </div>
					
				  <div class="form-group">
					<div class="col-md-offset-2 col-md-8">
					 <input type="submit" value="GRAVAR" class="btn btn-theme btn-lg btn-block">
					</div>
				  </div>
                  
				</form>
	
	  			</div>
			
				
	  		</div>
			

	  	</div>
	  </section>  

<?php 	
}else{
	$id_ped = $_POST['id_ped'];	
	if(isset($_POST['atualizar'])){
		$processo = $_POST['NumeroProcesso'];
		$nota = $_POST['NumeroNotaEmpenho'];
		$data_emissao = $_POST['DataEmissaoNotaEmpenho'];
		$data_entrega = $_POST['DataEntregaNotaEmpenho'];
		$con = bancoMysqli();
		$sql_atualiza = "UPDATE igsis_pedido_contratacao SET
		NumeroProcesso = '$processo',
		NumeroNotaEmpenho = '$nota',
		DataEmissaoNotaEmpenho = '$data_emissao',
		DataEntregaNotaEmpenho = '$data_entrega'
		WHERE idPedidoContratacao = '$id_ped'";
	$query_atualiza = mysqli_query($con,$sql_atualiza);
		if($query_atualiza){
			gravarLog($sql_atualiza);
			$mensagem = "Pedido atualizado";
			
		}else{
			$mensagem = "Erro ao atualizar";
		}

	echo $mensagem = "Erro(2)";
	}

	
	

$pedido = recuperaDados("igsis_pedido_contratacao",$id_ped,"idPedidoContratacao");
$ped = siscontrat($id_ped);
?>
	 <!-- Contact -->
	  <section id="contact" class="home-section bg-white">
	  	<div class="container">
			  <div class="form-group">
					<div class="sub-title"><h2><?php echo $ped['Objeto']; ?></h2>
                    <h3><?php if(isset($mensagem)){ echo $mensagem;} ?></h3>
                    <h3><?php if(isset($sql_atualizsa)){echo $sql_atualiza;} ?></h3>
                    </div>
                    
			  </div>

	  		<div class="row">
	  			<div class="col-md-offset-1 col-md-10">

				<form class="form-horizontal" role="form" action="?perfil=admin&p=contratos" method="post">
				 <div class="form-group">
					<div class="col-md-offset-2 col-md-8"><strong>Código do Pedido de Contratação:</strong><br/>
					  <input type="text" readonly class="form-control" id="IdPedidoContratacaoPJ" name="IdPedidoContratacaoPJ" value="<?php echo $pedido['idPedidoContratacao']; ?>" >
					</div>
				  </div>
				  
                  <div class="form-group">
					<div class="col-md-offset-2 col-md-8"><strong>Processo SEI:</strong>
					  <input type="text" class="form-control" id="NumeroProcesso" name="NumeroProcesso" placeholder="Número do Processo"  value="<?php echo $pedido['NumeroProcesso']; ?>" /> 
					</div>
				  </div>
				 
                  <div class="form-group">
					<div class="col-md-offset-2 col-md-8"><strong>Número da Nota de Empenho:</strong>
					  <input type="text" class="form-control" id="NumeroNotaEmpenho" name="NumeroNotaEmpenho" placeholder="Número da Nota de Empenho" value="<?php echo $pedido['NumeroNotaEmpenho']; ?>">
					</div>
				  </div>
                  
                   <div class="form-group">
					<div class="col-md-offset-2 col-md-8"><strong>Data de Emissão da Nota de Empenho:</strong>
					  <input type="date" class="form-control" id="DataEmissaoNotaEmpenho" name="DataEmissaoNotaEmpenho" placeholder="Data de Emissao da Nota de Empenho" value="<?php echo $pedido['DataEmissaoNotaEmpenho']; ?>">
					</div>
				  </div>
                  
                  <div class="form-group">
					<div class="col-md-offset-2 col-md-8"><strong>Data de Entrega da Nota de Empenho:</strong>
					  <input type="date" class="form-control" id="DataEntregaNotaEmpenho" name="DataEntregaNotaEmpenho" placeholder="Data de Entrega da Nota de Empenho" value="<?php echo $pedido['DataEntregaNotaEmpenho']; ?>">
					</div>
				  </div>
					
				  <div class="form-group">
					<div class="col-md-offset-2 col-md-8">
                    <input type="hidden" name="id_ped" value="<?php echo $id_ped; ?>" /> 
                    <input type="hidden" name="atualizar" value="1" /> 
					 <input type="submit" value="Atualizar" class="btn btn-theme btn-lg btn-block">
				</form>

					</div>

				  </div>
				<a href="?perfil=admin&p=contratos" class="btn btn-theme btn-lg btn-block" >Outro pedido</a>                  
	
	  			</div>
			
				
	  		</div>
			

	  	</div>
	  </section>  




<?php 
}

break;
case "sof":
if(isset($_FILES['arquivo'])){
	$mensagem = "";
	// Pasta onde o arquivo vai ser salvo
	$_UP['pasta'] = '../uploads/';
	// Tamanho máximo do arquivo (em Bytes)
	$_UP['tamanho'] = 1024 * 1024 * 50; // 2Mb
	// Array com as extensões permitidas
	$_UP['extensoes'] = array('xls', 'xlsx');
	// Renomeia o arquivo? (Se true, o arquivo será salvo como .jpg e um nome único)
	$_UP['renomeia'] = true;
	// Array com os tipos de erros de upload do PHP
	$_UP['erros'][0] = 'Não houve erro';
	$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
	$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
	$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
	$_UP['erros'][4] = 'Não foi feito o upload do arquivo';
	// Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
	if ($_FILES['arquivo']['error'] != 0) {
	  die("Não foi possível fazer o upload, erro:" . $_UP['erros'][$_FILES['arquivo']['error']]);
	  $mensagem .= "Não foi possível fazer o upload, erro:" . $_UP['erros'][$_FILES['arquivo']['error']];
	  exit; // Para a execução do script
	}
	// Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar
	// Faz a verificação da extensão do arquivo

	// Faz a verificação do tamanho do arquivo
	if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
	  $mensagem .= "O arquivo enviado é muito grande, envie arquivos de até 50Mb.";
	  exit;
	}
	// O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
	// Primeiro verifica se deve trocar o nome do arquivo
	if ($_UP['renomeia'] == true) {
	  // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
	$dataUnique = date('YmdHis');
	$arquivo_final = $dataUnique."_".semAcento($_FILES['arquivo']['name']);
	} else {
	  // Mantém o nome original do arquivo
	  $nome_final = $_FILES['arquivo']['name'];
	}
	  
	// Depois verifica se é possível mover o arquivo para a pasta escolhida
	if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $arquivo_final)) {
	  // Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
	  $mensagem .=  "Upload efetuado com sucesso!<br />";
	  $mensagem .= '<a href="' . $_UP['pasta'] . $arquivo_final . '">Clique aqui para acessar o arquivo</a>';
      
	  require_once("../include/phpexcel/Classes/PHPExcel.php");
	  $inputFileName = $_UP['pasta'] . $arquivo_final;
		
		//  Read your Excel workbook
		try {
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFileName);
		} catch(Exception $e) {
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}
		
		//  Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); 
		$highestColumn = $sheet->getHighestColumn();
		
		//Apagamos a tabela igsis_6354
		$sql_limpa = "TRUNCATE TABLE igsis_6354";
		if(mysqli_query($con,$sql_limpa)){
				$mensagem .= "<br />Tabela igsis_6354 limpa <br />";	
			}else{
				$mensagem .= "Erro ao limpar a tabela igsis_6354 <br />";	
			}		
		
		//  Loop through each row of the worksheet in turn
		for ($row = 1; $row <= $highestRow; $row++){ 
			//  Read a row of data into an array
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
											NULL,
											TRUE,
											FALSE);
			//  Insert row data array into your database of choice here
		
			if($row == 1){ // Dados da Tabela
				$mensagem .= $rowData[0][0];
				$data = date("Y-m-d H:i:s");
				$mens_sof = $rowData[0][0].". Arquivo SOF atualizado no sistema IGSIS em ".$data;
				$sql_atualizacao = "INSERT INTO `igsis_atualizacao` (`id`, `data`, `texto`, `tipo`) VALUES (NULL, '$data', '$mens_sof', 'sof')";
				$query_atualizacao = mysqli_query($con,$sql_atualizacao);
				if($query_atualizacao){
					$mensagem .= "Atualização registrada.<br />";	
				}
				
			}
			
			/*
			if($row == 2){	
			echo "<br /><br /><br />";
			echo $rowData[0][0]."0<br />";  
			echo $rowData[0][1]."1<br />";  
			echo $rowData[0][2]."2<br />";  
			echo $rowData[0][3]."3<br />";  
			echo $rowData[0][4]."4<br />";  
			echo $rowData[0][5]."5<br />";  
			echo $rowData[0][6]."6<br />";  
			echo $rowData[0][7]."7<br />";  
			echo $rowData[0][8]."8<br />";  
			echo $rowData[0][9]."9<br />";  
			echo $rowData[0][10]."10<br />";  
			echo $rowData[0][11]."11<br />";  
			echo $rowData[0][12]."12<br />";  
			echo $rowData[0][13]."13<br />";  
			echo $rowData[0][14]."14<br />";  
			echo $rowData[0][15]."15<br />";  
 

			}
			*/
				
			if($row > 2){ // Insere na tabela igsis_6354
			$dataEmpenho = exibirDataMysql(substr($rowData[0][0],0,10));  
			$empenho = $rowData[0][1];  
			$ano = $rowData[0][2];  
			$processo = $rowData[0][3];  
			$descricao = $rowData[0][4];  
			$valor = $rowData[0][5];  
			$cancelamento = $rowData[0][6];  
			$liquidado = $rowData[0][7];  
			$pago = $rowData[0][8];  
			$valorLiquidar = $rowData[0][9];  
			$totalPagar = $rowData[0][10];  
			$razaoSocial = $rowData[0][11];  
			$cnpj = $rowData[0][12];  
			$unidade = $rowData[0][13];  
			$dotacao = $rowData[0][14];  
			$x =  $rowData[0][15]; 
			
			$sql_insere = "INSERT INTO  `igsis_6354` 
			(`id` , `data_empenho` , `empenho` , `ano` , `processo` , `descricao` , `valor` , `cancelamento` , `liquidado` , `pago` , `valor_a_liquidar` , `total` , `razao_social` , `cpf_cnpj` , `dotacao` )
VALUES 
			(NULL , '$dataEmpenho' , $empenho,  '$ano' , '$processo' , '$descricao' , '$valor' , '$cancelamento' , '$liquidado' , '$pago' , '$valorLiquidar' , '$totalPagar' , '$razaoSocial' , '$cnpj' , '$dotacao') ";
	$query_insere = mysqli_query($con,$sql_insere);
	if($query_insere){
		$mensagem .= "Processo $processo inserido na tabela igsis_6354. <br />";	
	}
			

				
			}
			

		}
	  
	  
	  
	} else {
	  // Não foi possível fazer o upload, provavelmente a pasta está incorreta
	  $mensagem =  "Não foi possível enviar o arquivo, tente novamente";
	}	
}
?>

	 <!-- Contact -->
	  <section id="contact" class="home-section bg-white">
	  	<div class="container">
			  <div class="form-group">
					<div class="sub-title"><h2>Integração SOF / IGSIS</h2>
                    <h3></h3>
                              
                    </div>
                    
			  </div>

	  		<div class="row">
	  			<div class="col-md-offset-1 col-md-10">
                
                <?php if(isset($rowData)){
					if(isset($mensagem)){ echo $mensagem;} 	
				}else{
				 ?>

				<form method="POST" action="?perfil=admin&p=sof" enctype="multipart/form-data">
				 <div class="form-group">
					<div class="col-md-offset-2 col-md-8"><strong>Arquivo em EXCEL (Máximo 50M)</strong><br/>
					  <input type="file" class="form-control" name="arquivo" /	>
					</div>
				  </div>
					
				  <div class="form-group">
					<div class="col-md-offset-2 col-md-8">
                    <input type="hidden" name="enviado" />
					 <input type="submit" value="Fazer upload" class="btn btn-theme btn-lg btn-block">
				</form>

					</div>

				  </div>
			<?php } ?>                  
	
	  			</div>
			
				
	  		</div>
			

	  	</div>
	  </section>  


<?php
break;
case "scripts":

if(isset($_GET['atualizar'])){
	if($_GET['atualizar'] == 'agenda'){
		if(reloadAgenda()){
			$texto = "Agenda atualizada.";	
		}	
	}
}

if(isset($_GET['status'])){
	$con = bancoMysqli();
	$sql_pedido = "SELECT * FROM igsis_pedido_contratacao WHERE publicado = '1'";
	$query_pedido = mysqli_query($con,$sql_pedido);
	$texto = "";
	$i = 0;
	

	while($pedido = mysqli_fetch_array($query_pedido)){
		$idPedido = $pedido['idPedidoContratacao'];

		$texto .= $pedido['estado']."<br />";	

		if($pedido['aprovacaoFinanca'] == NULL OR $pedido['aprovacaoFinanca'] == 1 ){
			
		}else{
		if(trim($pedido['NumeroProcesso']) != "" OR $pedido['NumeroProcesso'] != NULL){ // Se há número de processo
			if(trim($pedido['NumeroNotaEmpenho']) != "" OR $pedido['NumeroNotaEmpenho'] != NULL){ // Se há número de Nota de Empenho
				$idStatus = "10";
			$texto .= "O status do pedido $idPedido é 10.<br />";
			}else{
				$idStatus = "4"; //Só tem número de processo	
				$texto .= "O status do pedido $idPedido é 4.<br />";
			}
		}  		
		// switch
		$switchPedido = $pedido['estado'];
		switch($switchPedido){
		
		case "Proposta":
		$idStatus = "5";
		$texto .= "O status do pedido $idPedido é Proposta.<br />";
		break;

		case "Análise do Pedido":
		$idStatus = "3";
		$texto .= "O status do pedido $idPedido é Análise.<br />";
		break;

		case "Pedido":
		$idStatus = "1";
		$texto .= "O status do pedido $idPedido é Pedido.<br />";
		
		break;

		case "Concluído":
		$idStatus = "11";
		$texto .= "O status do pedido $idPedido é Concluído.<br />";
		break;
		}
		
		$sql_atualiza = "UPDATE igsis_pedido_contratacao SET estado = '$idStatus' WHERE idPedidoContratacao = '$idPedido'";
		$query_atualiza = mysqli_query($con, $sql_atualiza);
		if($query_atualiza){
			$texto .= "OK<br />";
			$i++;	
		}else{
			$texto .= "Erro<br />";	
		}
	}
	}
	
	$texto .= "<br /> $i pedidos atualizados.<br />";

}

if(isset($_GET['empenho'])){
	$sql_pedido = "SELECT * FROM igsis_pedido_contratacao WHERE publicado = '1' and valor > 0";
	$query_pedido = mysqli_query($con,$sql_pedido);
	$texto = "";
	while($pedido = mysqli_fetch_array($query_pedido)){
		if($pedido['NumeroNotaEmpenho'] != "" OR $pedido['NumeroNotaEmpenho'] != NULL){
			$con = bancoMysqli();
			$idPedido = $pedido['idPedidoContratacao'];
			$sql_atualiza_status = "UPDATE igsis_pedido_contratacao SET estado = '10' WHERE idPedidoContratacao = '$idPedido'"; 
			$query_atualiza_status = mysqli_query($con,$sql_atualiza_status);
			if($query_atualiza_status){
				$texto .= "Pedido $idPedido atualizado para Entrega N.E.";
			}	
		}
	}
}

if(isset($_GET['inst_agenda'])){
	$con = bancoMysqli();	
	$sql_data = "SELECT * FROM igsis_agenda";
	$query_data = mysqli_query($con,$sql_data);
	$i = 0;
	$num = mysqli_num_rows($query_data);
	while($agenda = mysqli_fetch_array($query_data)){
		
		$id = $agenda['idAgenda'];
		$inst = recuperaDados("ig_local",$agenda['idLocal'],"idLocal");
		$idInst = $inst['idInstituicao'];
		$sql_atualiza = "UPDATE igsis_agenda SET idInstituicao = '$idInst' WHERE idAgenda = '$id'";
		$query_atualiza = mysqli_query($con,$sql_atualiza);
		if($query_atualiza){
			$i++;	
		}
	}
	$mensagem = "Foram atualizados $i de $num registros.";
}


if(isset($_GET['limpar_base'])){
	$con = bancoMysqli();	
	$sql_data = "DELETE FROM ig_evento WHERE ig_tipo_evento_idTipoEvento = '0'";
	$query_data = mysqli_query($con,$sql_data);
	$num = mysqli_affected_rows($query_data);
	if($query_data){
		$mensagem = "Base de eventos limpa. Foram deletados $num registros.";
	}else{
		$mensagem = "Erro ao limpar a base";
	}
}

if(isset($_GET['formacao'])){ //script de importação da base de pessoas físicas formação
	$con = bancoMysqli();
	
	$texto = "";

	function mascara_cpf($cpf){ 
		$a= substr("$cpf", 0,3); 
		$b= substr("$cpf", 3,3); 
		$c= substr("$cpf",6,3); 
		$d= substr("$cpf",9,2); 
		$novo_cpf = $a.'.'.$b.'.'.$c.'-'.$d; 
	
	return $novo_cpf; 
	}
	
	function mask($val, $mask)
	{
	 $maskared = '';
	 $k = 0;
	 for($i = 0; $i<=strlen($mask)-1; $i++)
	 {
	 if($mask[$i] == '#')
	 {
	 if(isset($val[$k]))
	 $maskared .= $val[$k++];
	 }
	 else
	 {
	 if(isset($mask[$i]))
	 $maskared .= $mask[$i];
	 }
	 }
	 return $maskared;
	}

	function estadoCivil($estado){
		switch($estado){
			case "SOLTEIRA":
			$est = 3;
			break;

			case "DIVORCIADO(A)":
			$est = 2;
			break;
			
			case "SOLTEIRO(A)":
			$est = 3;
			break;
			
			case "DIVORCIADO":
			$est = 2;
			break;
			
			case "CASADA":
			$est = 1;
			break;
			
			case "CASADO":
			$est = 1;
			break;

			case "VIUVA":
			$est = 4;
			break;
			
			case "OUTRO":
			$est = 5;
			break;
			
			case "DIVORCIADA":
			$est = 2;
			break;
			
			default:
			$est = 5;
		}
		return $est;
	}
	
	$sql_verificia = "SELECT * FROM tbl_pf";
	$query_verifica = mysqli_query($con,$sql_verificia);
	while($verifica = mysqli_fetch_array($query_verifica)){
		$cpf_tbl = mascara_cpf($verifica['CPF']);
		$sql_compara = "SELECT * FROM sis_pessoa_fisica WHERE CPF LIKE '$cpf_tbl'";
		$query_compara = mysqli_query($con,$sql_compara);
		$num_compara = mysqli_num_rows($query_compara);

	if($num_compara > 0){ //verifica se há registro
		$texto .= "O CPF $cpf_tbl já existe no sistema.<br />";
	}else{ //
		$texto .= "O CPF $cpf_tbl não existe no sistema.<br />";

		$Nome = $verifica['Nome'];
		$NomeArtistico = $verifica['Nome_Art'];
		$RG = $verifica['RG'];
		$CPF = $cpf_tbl;
		$CCM = $verifica['CCM'];
		$IdEstadoCivil = estadoCivil($verifica['Est_Civ']); //funcao para verificar
		
		if($verifica['D_nasc'] == NULL){
		$DataNascimento == NULL;
		}else{
		$DataNascimento = exibirDataMysql($verifica['D_nasc']); //funcao para data em mysql
		}
		$LocalNascimento = $verifica['Loc_Nasc'];

		$Nacionalidade = "Brasileiro(a)"; 
		$CEP = mask($verifica['Cep'],'#####-###');
			
		
		
		$Telefone1 = $verifica['Telefone'];
		$Telefone2 = $verifica['Telefon2']; 
		$Telefone3 = $verifica['Telefon3']; 
		$Email = $verifica['Email']; 
		$DRT = $verifica['DRT'];
		$Pis = $verifica['Pis'];
		
		$DataAtualizacao = date('Y-m-d'); 
		$Observacao = $verifica['Endereco']."\n".$verifica['Regiao']."\n".$verifica['currric']."\n".$verifica['Grau_Ins']."\n";
		
		
		$tipoDocumento = "1";
		

		
		$sql_insere_cpf = "INSERT INTO `sis_pessoa_fisica` 
		(`Nome`, `NomeArtistico`, `RG`, `CPF`, `CCM`, `IdEstadoCivil`, `DataNascimento`, `LocalNascimento`, `Nacionalidade`, `CEP`,  `Telefone1`, `Telefone2`, `Telefone3`, `Email`, `DRT`,  `Pis`,  `DataAtualizacao`, `Observacao`) VALUES ('$Nome', '$NomeArtistico', '$RG', '$cpf_tbl', '$CCM', '$IdEstadoCivil' ,$DataNascimento, '$LocalNascimento', '$Nacionalidade','$CEP', '$Telefone1', '$Telefone2', '$Telefone3', '$Email', '$DRT', '$Pis',  '$DataAtualizacao', '$Observacao')";
		$query_insere_cpf = mysqli_query($con,$sql_insere_cpf);
		if($query_insere_cpf){
			$texto .= "O CPF $cpf_tbl foi inserido com sucesso no sistema.<br />";
		}else{
			$texto .= "Erro ao inserir CPF $cpf_tbl no sistema.<br />";
		}
	}

	}
	



	
}

if(isset($_GET['contabilidade'])){


	
	//Primeiro é preciso importar as tabelas no formado ods e colocar como a primeira linha como nome dos campos
	
	$con = bancoMysqli(); //conecta ao banco
	$mensagem = "<i>Atualizando tabela SOF com IGSIS</i><br /><br />";

	//verifica se há no banco de dados a tabela 6354
	$result_upload = mysqli_query($con,"SHOW TABLES LIKE '6354'");
	$tableExists_upload = mysqli_num_rows($result_upload);
	if($tableExists_upload > 0){
		
		
		
		
		
		//verifica se a tabela existe e apaga se for o caso
		$table = 'igsis_6354';
		$result = mysqli_query($con,"SHOW TABLES LIKE '$table'");
		$tableExists = mysqli_num_rows($result);
		if($tableExists > 0){
			$apagar_tabela = "DROP TABLE 'igsis_6354'";
			$query_apagar_tabela = mysqli_query($con,$apagar_tabela);
			if($query_apagar_tabela){
				$re_tabela = "RENAME TABLE `6354` TO `igsis_6354`";
				$query_re_tabela = mysqli_query($con,$re_tabela);
				if($query_re_tabela){
					

					

				$mensagem .= "Tabela igsis_6354 renomeada com sucesso!<br />";	





				}else{
					$mensagem .= "Erro ao renomear tabela igsis_6354 (1)<br />";	
				}	
			}else{
				$mensagem .= "Erro ao apagar tabela igsis_6354 (2)<br />";	
			}	
		}else{
				$re_tabela = "RENAME TABLE `igsis`.`6354` TO `igsis`.`igsis_6354`;";
				$query_re_tabela = mysqli_query($con,$re_tabela);
				if($query_re_tabela){
					$mensagem .= "Tabela igsis_6354 renomeada com sucesso!<br />";	
				}else{
					$mensagem .= "Erro ao renomear tabela igsis_6354 (3)<br />";	
				}	
				
		}

		if($query_re_tabela){
					$sql_id = "ALTER TABLE igsis_6354 ADD id INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
					$query_id = mysqli_query($con,$sql_id);
					if($query_id){
						$mensagem .= "Criação de campo id realizada com sucesso!<br />";
					}else{
						$mensagem .= "Falha na criação de campo id...<br />";	
					}
					
					$sql_re_campos[1] = "ALTER TABLE `igsis_6354` CHANGE `DATA EMPENHO` `data_empenho` VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
					$sql_re_campos[2] = "ALTER TABLE `igsis_6354` CHANGE `ANO_` `ano` INT(4) NULL DEFAULT NULL;";
					$sql_re_campos[3] = "ALTER TABLE `igsis_6354` CHANGE `EMPENHO` `empenho` INT(12) NULL DEFAULT NULL;";
					$sql_re_campos[4] = "ALTER TABLE `igsis_6354` CHANGE `DOTAÇÃO` `dotacao` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
					$sql_re_campos[5] =	"ALTER TABLE `igsis_6354` CHANGE `PROCESSO` `processo` BIGINT(16) NULL DEFAULT NULL;";
					$sql_re_campos[6] =	"ALTER TABLE `igsis_6354` CHANGE `DESCRIÇÃO` `descrica` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
					$sql_re_campos[7] =	"ALTER TABLE `igsis_6354` CHANGE `VALOR` `valor` VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
					$sql_re_campos[8] =	"ALTER TABLE `igsis_6354` CHANGE `CANCELAMENTO` `cancelamento` VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
					$sql_re_campos[9] =	"ALTER TABLE `igsis_6354` CHANGE `LIQUIDADO` `liquidado` VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
					$sql_re_campos[10] = "ALTER TABLE `igsis_6354` CHANGE `PAGO` `pago` VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
					$sql_re_campos[11] = "ALTER TABLE `igsis_6354` CHANGE `VALOR A LIQUIDAR` `valor_a_liquidar` VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
					$sql_re_campos[12] = "ALTER TABLE `igsis_6354` CHANGE `TOTAL A PAGAR` `total` VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
					$sql_re_campos[13] = "ALTER TABLE `igsis_6354` CHANGE `RAZÃO SOCIAL` `razao_social` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
					$sql_re_campos[14] = "ALTER TABLE `igsis_6354` CHANGE `CPF/CNPJ` `cpf_cnpj` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
				
					
					$s = 0;
					$e = 0;
					for($i = 1; $i < 15; $i++){
					$query_re_campos = mysqli_query($con,$sql_re_campos[$i]);
						if($query_re_campos){
							$s++;			
						}else{
							$e++;	
						}
					
					}
	
					$mensagem .= "$s campos renomeados e $e com erros ao nomear <br />";	
		
		}
	
		/*
		`DATA EMPENHO`, `EMPENHO`, `ANO_`, `PROCESSO`, `DESCRIÇÃO`, `VALOR`, `CANCELAMENTO`, `LIQUIDADO`, `PAGO`, `VALOR A LIQUIDAR`, `TOTAL A PAGAR`, `RAZÃO SOCIAL`, `CPF/CNPJ`, `DOTAÇÃO`
		`id`, `data_empenho`, `empenho`, `ano`, `processo`, `descricao`, `valor`, `cancelamento`, `liquidado`, `pago`, `valor_a_liquidar`, `total`, `razao_social`, `cpf_cnpj`, `dotacao`
	
	
	
	
	
	
	
	
	
	
		*/
		
		
		// procura na tabela igsis_pedido_contratacao todos os processos que possume Numero de Processo
/*		$sql_pedido = "SELECT * FROM igsis_pedido_contratacao WHERE (NumeroProcesso <> '' OR NumeroProcesso IS NOT NULL) AND publicado = '1' ORDER BY idPedidoContratacao DESC";
		$query_pedido = mysqli_query($con,$sql_pedido);
		$n_pedido = mysqli_num_rows($query_pedido);
		$tabela = "
			<div class='container'>
				 <h5>Foram encontrados $n_pedido pedidos de contratação com Número de Processos SEI válidos.</h5>
				<div class='table-responsive list_info'>
		<table class='table table-condensed'>
						<thead>
							<tr class='list_menu'>
								<td>Processo</td>
								<td>Valor IGSIS</td>
								<td>Valor SOF</td>
								<td>Cancelado</td>
								<td>Liquido</td>
								<td>Pago</td>
								<td>A Liquidar</td>
								<td>Total SOF</td>
								<td>Dotação</td>
							</tr>
						</thead>
		
		
		
		"; //Começa a montar o texto
	
		while($pedido = mysqli_fetch_array($query_pedido)){
			$n_processo = trim(soNumero($pedido['NumeroProcesso'])); // transforma em numero e evita espaços na frente e no final
	
			// executa uma query verificando se o processo já está na contabilidade
			$sql_conta = "SELECT * FROM igsis_6354 WHERE processo LIKE '$n_processo'";
			$query_conta = mysqli_query($con,$sql_conta);
			$n_conta = mysqli_num_rows($query_conta);
			if($n_conta > 0){ 
				while($conta = mysqli_fetch_array($query_conta)){
				$numero_processo = $pedido['NumeroProcesso'];
				$tabela .= "
				<tr>
				<td>$n_processo </td>
				<td>".($pedido['valor'])."</td >
				<td>".($conta['valor'])."</td>
				<td>".($conta['cancelamento'])."</td>
				<td>".($conta['liquidado'])."</td>
				<td>".($conta['pago'])."</td>
				<td>".($conta['valor_a_liquidar'])."</td>
				<td>".($conta['total'])."</td>
				<td>".($conta['dotacao'])."</td>
				</tr>
				";
				
				
				}
			}
		}
		echo "</table>
		</div>
		</div>
	";*/
	}else{
		$mensagem .= "É preciso fazer upload da tabela em formato ODS pelo PHPMYADMIN.<br />
		Não se esqueça de escolhar a opção <i>'A primeira linha contém o nome dos campos'</i> ao importar.<br />
		Aproveite para fazer um backup geral do banco.<br />";	
	}

}

?>
<section id="contact" class="home-section bg-white">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-2 col-md-8">
                <div class="text-hide">
                <h2>Administrador do Sistema</h2>
	                <h5>Scripts - use com moderação</h5>
                    <h5><?php if(isset($mensagem)){ echo $mensagem; } ?></h5>
                    
                </div>
            </div>
        <div class="form-group">
            <div class="col-md-offset-2 col-md-8">
                <a href="?perfil=admin&p=scripts&atualizar=agenda" class="btn btn-theme btn-lg btn-block">Atualizar agenda</a>
                <a href="?perfil=admin&p=scripts&status=1" class="btn btn-theme btn-lg btn-block">Atualizar status</a>
              <a href="?perfil=admin&p=scripts&inst_agenda=1" class="btn btn-theme btn-lg btn-block">Atualizar Instituições/Agenda</a>
               <a href="?perfil=admin&p=scripts&limpar_base=1" class="btn btn-theme btn-lg btn-block">Limpar base de eventos</a>
              <a href="?perfil=admin&p=scripts&empenho=1" class="btn btn-theme btn-lg btn-block">Atualizar status N.E.</a>
              <a href="?perfil=admin&p=scripts&formacao=1" class="btn btn-theme btn-lg btn-block">Importar Base PF Formação</a>
              <a href="?perfil=admin&p=scripts&contabilidade=1" class="btn btn-theme btn-lg btn-block">Verificar tabela contabilidade</a>

	            <!--<a href="?perfil=busca&p=pedidos" class="btn btn-theme btn-lg btn-block">Pedidos de contratação</a>-->

            </div>
          </div>
          <?php if(isset($texto)){ ?>
        <div class="form-group">
            <div class="col-md-offset-2 col-md-8">
            <br /><br />
				<p><?php echo $texto;  ?></p>
            </div>
          </div>
  		<?php } ?>	

        </div>

    </div>
<?php //if(isset($_GET['contabilidade'])){
	//echo $tabela;	
//} ?>  
  
</section>
  

<?php } ?>
