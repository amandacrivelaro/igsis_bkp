<?php 

/*
igSmc v0.1 - 2015
ccsplab.org - centro cultural são paulo
*/

// Esta é a página de login do usuário ou de contato com administrador do sistema.

//Imprime erros com o banco


	include "funcoes/funcoesGerais.php";
	require "funcoes/funcoesConecta.php";

if(isset($_POST['usuario'])){

	$usuario = $_POST['usuario'];
	$senha = $_POST['senha'];
	autenticaUsuario($usuario,$senha);	

}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>IGSMC - v0.1 - 2015</title>
    <link href="visual/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="visual/css/style.css" rel="stylesheet" media="screen">
	<link href="visual/color/default.css" rel="stylesheet" media="screen">
	<script src="visual/js/modernizr.custom.js"></script>
</head>


<body>
	  <section id="contact" class="home-section bg-white">
	  	<div class="container">
			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="text-hide">
					 <h2>IGSIS - SMC</h2>
					 <p>É preciso ter um login válido. Dúvidas? Envie um email para sistema.igsis@gmail.com </p>
					</div>
				  </div>
			  </div>

	  		<div class="row">
	  			<div class="col-md-offset-1 col-md-10">

				<form method="POST" action="index.php"class="form-horizontal" role="form">
				  <div class="form-group">
					<div class="col-md-offset-2 col-md-6">
					  <input type="text" name="usuario" class="form-control" id="inputName" placeholder="Usuário">
					</div>
				  
					<div class=" col-md-6">
					  <input type="password" name="senha" class="form-control" id="inputEmail" placeholder="Senha">
					</div>
				  </div>

				  <div class="form-group">
					<div class="col-md-offset-2 col-md-8">
					 <button type="submit" class="btn btn-theme btn-lg btn-block">Entrar</button>
					</div>
				  </div>
				</form>
				<br />
				
                    <div class="form-group">
					<div class="col-md-offset-2 col-md-8">
					<h4><p>Não possui cadastro?<strong><a href="https://goo.gl/lHLdnI" target="_blank"> CLIQUE AQUI!</strong></a></p></h4>
					<p>Dúvidas? Envie e-mail para: <strong>sistema.igsis@gmail.com</strong></p>
                    <p>Saiba mais sobre <u>"Fluxo SEI na SMC"</u> :  <strong><a href="http://sei.smc.adm.br/" target="_blank"> sei.smc.adm.br </strong></a></p>
                    <br />
                                
					</div>
				  </div>

				  <div class="form-group">
					<div class="col-md-offset-2 col-md-8">
					<h5>Últimos eventos inseridos</h5>
                    <div class="left">
                    <ul>

					<?php 

					$con = bancoMysqli();
					$sql_ultimo = "SELECT * FROM ig_evento WHERE dataEnvio IS NOT NULL ORDER BY dataEnvio DESC LIMIT 0,20";
					$query_ultimo = mysqli_query($con,$sql_ultimo);
					while($evento = mysqli_fetch_array($query_ultimo)){
						$usuario = recuperaUsuario($evento['idUsuario']);
						$instituicao = recuperaDados("ig_instituicao",$usuario['idInstituicao'],"idInstituicao");				
					?>
					<li><p><strong><?php echo $evento['nomeEvento'] ?> </strong>(<?php echo retornaTipo($evento['ig_tipo_evento_idTipoEvento']) ?>) </p>
                    <p><i><?php echo $evento['autor'] ?></i> - enviado por: <?php echo $usuario['nomeCompleto'] ?> (<?php echo $instituicao['instituicao'] ?>) em: <?php echo exibirDataBr($evento['dataEnvio']) ?></p>
                    <p><?php echo resumoOcorrencias($evento['idEvento']); ?></p>
					<br />				
                    </li>
					<?php } ?>
                    </ul>                   

                    </div>

					</div>
					

				  </div>


	
	  			</div>
			
				
	  		</div>
			

	  	</div>
	  </section>  


	 
<?php include "visual/rodape.php" ?>
    


</body>
</html>
