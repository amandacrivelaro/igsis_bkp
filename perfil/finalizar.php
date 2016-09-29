<?php
if(isset($_POST['finalizar'])){

	$con = bancoMysqli();
	$datetime = date("Y-m-d H:i:s");
	$instituicao = $_SESSION['idInstituicao'];
	$idEvento = $_SESSION['idEvento'];
	$sql_atualiza_evento = "UPDATE ig_evento SET dataEnvio = '$datetime', statusEvento = 'Enviado' WHERE idEvento = '$idEvento'";
	$query_atualiza_evento = mysqli_query($con,$sql_atualiza_evento);
	if($query_atualiza_evento){
		gravarLog($sql_atualiza_evento);
		atualizarAgenda($idEvento);	
	}
	$sql_atualiza_pedido = "UPDATE `igsis`.`igsis_pedido_contratacao` SET 
	`estado` = '1'
	WHERE `igsis_pedido_contratacao`.`idEvento` = '$idEvento' AND `igsis_pedido_contratacao`.`publicado` = '1' ";
	$query_atualiza_pedido = mysqli_query($con,$sql_atualiza_pedido);
		if($query_atualiza_evento){
			gravarLog($sql_atualiza_pedido);
			$sql_protocolo = "INSERT INTO `ig_protocolo` (`idProtocolo`, `ig_evento_idEvento`, `publicado`, `dataInsercao`) VALUES (NULL, '$idEvento', '1', '$datetime')";
			$query_protocolo = mysqli_query($con,$sql_protocolo);
			if($query_protocolo){
				gravarLog($sql_protocolo);
				$protocolo = recuperaUltimo("ig_protocolo");
				$mensagem = "O formulário de evento foi enviado com sucesso!<br/><h5>O número IG é ".$idEvento."</h5>
				Refira-se a este número ao entrar em contato com as áreas de Comunicação e Produção.<br /><br /><br />
				";
				$sql_recupera_pedidos = "SELECT * FROM igsis_pedido_contratacao WHERE idEvento = '$idEvento' AND publicado = '1'";
				$query_recupera_pedidos = mysqli_query($con,$sql_recupera_pedidos);
				$num_pedidos = mysqli_num_rows($query_recupera_pedidos);
				
				//Envia Email
				$evento = recuperaDados("ig_evento",$idEvento,"idEvento"); //$tabela,$idEvento,$campo
				$usuario = recuperaDados("ig_usuario",$evento['idUsuario'],"idUsuario");
				$instituicao = recuperaDados("ig_instituicao",$usuario['idInstituicao'],"idInstituicao");
				$local = listaLocais($idEvento);
				$periodo = retornaPeriodo($idEvento);
				$fiscal = recuperaUsuario($evento['idResponsavel']);
				$suplente = recuperaUsuario($evento['suplente']);

				$tipoEvento = recuperaDados('ig_tipo_evento',$evento['ig_tipo_evento_idTipoEvento'],'idTipoEvento');
				$programa = recuperaDados('ig_programa',$evento['ig_programa_idPrograma'],'idPrograma');
				$projetoEspecial = recuperaDados('ig_projeto_especial',$evento['projetoEspecial'],'idProjetoEspecial');

				$conteudo_email = "Olá, <br /><br />
				
				O evento <b>".$evento['nomeEvento']."</b> foi cadastrado no sistema por ".$usuario['nomeCompleto']." em ".exibirDataHoraBr($datetime).".<br /><br />
					Número da IG: <b>".$idEvento."</b><br />
					<b>Tipo de evento:</b> ".$tipoEvento['tipoEvento']."<br />";
	
	if($evento['ig_programa_idPrograma'] != 0){ $conteudo_email .= "<b>Programa especial:</b> ".$programa['programa']."<br />";}
	if($evento['projetoEspecial'] != 0){ $conteudo_email .= "<b>Projeto especial:</b> ".$projetoEspecial['projetoEspecial']."<br />";}
	if($evento['projeto'] != ""){ $conteudo_email .= "<b>Projeto:</b> ".$evento['projeto']."<br />";}
	$conteudo_email .= "
	<br />
	<b>Responsável pelo evento:</b> ".$fiscal['nomeCompleto']."<br />
	<b>Suplente:</b> ".$suplente['nomeCompleto']."<br />
	<br />
	<b>Sinopse:</b><br />".nl2br($evento['sinopse'])."<br /><br />
	<b>Local / Período: </br >".substr($local,1)." / ".$periodo."<br />
	<br />
	
	
	Saiba mais acessando: <a href='http://www.centrocultural.cc/igsis/'> centrocultural.cc/igsis </a>
	
	<br />
	<br />
	<p>Atenciosamente,<br />
	Equipe IGSIS</p>
				";

				$subject = "O evento ".$evento['nomeEvento']." foi cadastrado no sistema.";
				enviarEmail($conteudo_email, $_SESSION['idInstituicao'], $subject, $idEvento, $num_pedidos );

				
				if($num_pedidos > 0){
					while($pedido = mysqli_fetch_array($query_recupera_pedidos)){
						$idPedido = $pedido['idPedidoContratacao'];
						$idUsuario = $_SESSION['idUsuario'];
						$sql_fecha_pedido = "INSERT INTO `sis_protocolo` (`idProtocolo`, `idPedido`, `data`, `userId`) VALUES (NULL, '$idPedido', '$datetime', '$idUsuario')";
						$query_fecha_pedido = mysqli_query($con,$sql_fecha_pedido);
						$i = 0;
						if($sql_fecha_pedido){
								gravarLog($sql_fecha_pedido);
								$protoPedido = recuperaUltimo("sis_protocolo");
								$pedidos[$i] = $idPedido;
								$mensagem = $mensagem."Foi gerado um <strong>pedido de contratação</strong> com número <h5>".$pedidos[$i]."</h5>
								Este número é a referência para as áreas de Contratos, Jurídico, Finanças, Contabilidade entre outros.<br />
								<strong><a target='_blank' href='?perfil=detalhes_contrato&id_ped=".$pedidos[$i]."'>Clique aqui caso queira visualizar os detalhes desta contratação.</a></strong>
								<br /><br />
								<a href='http://www.centrocultural.cc/igsis/manual/index.php/2016/01/24/numero-ig-e-numero-pedido-de-contratacao/' target='_blank'>Saiba mais sobre os números gerados no nosso <i>Manual do Sistema</i></a>.<br /><br /><br />
								";
								$i++;
							
						} 
					}
					
				}
				
					
			}else{
				$mensagem = "Erro ao gerar protocolo";
			}		
		}else{
				$mensagem = "Erro ao enviar formulário";	
		}


// Gera um registro em ig_comunicacao
$sql_pesquisar = "SELECT * FROM ig_evento WHERE idEvento = '$idEvento'";
$query = mysqli_query($con,$sql_pesquisar);
while($importa = mysqli_fetch_array($query)){
	$sql_importar = "INSERT INTO `igsis`.`ig_comunicacao` (`sinopse`, `fichaTecnica`, `autor`, `projeto`, `releaseCom`, `ig_evento_idEvento`, `nomeEvento`, `ig_tipo_evento_idTipoEvento`, `ig_programa_idPrograma`, `idInstituicao`) 
	SELECT `sinopse`, `fichaTecnica`, `autor`, `projeto`,`releaseCom`, `idEvento`, `nomeEvento`, `ig_tipo_evento_idTipoEvento`, `ig_programa_idPrograma`, `idInstituicao` FROM `ig_evento` WHERE `idEvento` = '$idEvento'";
	$query_importar = mysqli_query($con,$sql_importar);
	if($query_importar){
		$mensagem_com = "Registro na Divisão de Comunicação e Informação efetuado com sucesso.";
	}else{
		$mensagem_com = "Erro ao registrar evento na Divisão de Comunicação e Informação.";
	}
}		


// Criar data para fechamento
// Criar Protocolo da IG
// Criar Protocolo dos Pedidos de Contratação
// Enviar e-mail para as áreas interessadas
/*
$evento = recuperaDados("ig_evento",$idEvento,"idEvento");
$tipo = recuperaDados("ig_tipo_evento",$evento['ig_tipo_evento_idTipoEvento'],"idTipoEvento");
if($num_pedidos > 0){
	$ped = "Há pedidos de contratação artística";	
}else{
	$ped = "Não há pedidos de contratação artística";
}
$conteudo_email = "Olá,<br /><br />
Uma nova IGSIS foi enviada por ".$_SESSION['nomeCompleto']." em ".exibirDataHoraBr($evento['dataEnvio']).". <br /><br />

Nome do evento: <strong>".$evento['nomeEvento']." (".$tipo['tipoEvento'].")</strong><br />
Período/Locais: ".resumoOcorrencias($idEvento)."<br /><br />

$ped <br /><br />

Para saber mais, acesse: http://www.centrocultural.cc/igsis
<br />
<br />
Equipe IGSIS





";

$instituicao = $_SESSION['idInstituicao']; 
$subject = "Uma nova IGSIS foi enviada por ".$_SESSION['nomeCompleto']." em ".exibirDataHoraBr($evento['dataEnvio']); 
$user = recuperaDados("ig_usuario",$_SESSION['idUsuario'],"idUsuario");
$email = $user['email']; 
$usuario = $user['nomeCompleto'];

$mensagem_email = enviarEmail($conteudo_email, $instituicao, $subject, $email, $usuario );
*/
// Fecha sessão
// Verificar datas

$_SESSION['idEvento'] = NULL;	

}


?>

	 <section id="services" class="home-section bg-white">
		<div class="container">
			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="section-heading">
					 <h3>Envio confirmado!</h3>
                     
<p><?php //var_dump($_SESSION); ?></p>
<p><?php if(isset($mensagem)){echo $mensagem;} ?></p>
<p><?php if(isset($mensagem_com)){echo $mensagem_com;} ?></p>
<p><?php if(isset($mensagem_email)){echo $mensagem_email;} ?></p>

<p><a href="?p=inicio">Voltar ao início?</a></p>

					</div>
				  </div>
			  </div>
			  
		</div>
	</section>