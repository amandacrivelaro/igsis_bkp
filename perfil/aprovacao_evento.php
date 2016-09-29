<?php
if(isset($_POST['aprovacao_evento'])){

	$con = bancoMysqli();
	$datetime = date("Y-m-d H:i:s");
	$instituicao = $_SESSION['idInstituicao'];
	$idEvento = $_SESSION['idEvento'];
	$sql_atualiza_evento = "UPDATE ig_evento SET statusEvento = 'Aguardando' WHERE idEvento = '$idEvento'";
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
			$mensagem = "<h3>Solicitação enviada!</h3>
						<p>Aguarde a liberação pelo responsável.</p>";	
				
		}else{
				$mensagem = "Erro ao enviar formulário";	
		}


$_SESSION['idEvento'] = NULL;	

}


?>

	 <section id="services" class="home-section bg-white">
		<div class="container">
			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="section-heading">
					 <h3></h3>
                     
<p><?php //var_dump($_SESSION); ?></p>
<?php if(isset($mensagem)){echo $mensagem;} ?>
<p>&nbsp;</p>

<p><a href="?perfil=evento">Voltar ao módulo eventos?</a></p>

					</div>
				  </div>
			  </div>
			  
		</div>
	</section>