	<div class="menu-area">
					<div id="dl-menu" class="dl-menuwrapper">
						<button class="dl-trigger">Open Menu</button>
						<ul class="dl-menu">
							<li><a href="?secao=inicio">Início</a></li>
							<li><a href="?secao=perfil">Perfil de acesso</a></li>
<?//Menu Administrativo!?> <li><a href="#lista"> Perfil Administrativo Local </a>
							<ul class="dl-submenu">
							<li><a href="?perfil=administrador&p=users"> Listar Usuários</a></li>
							<li><a href="?perfil=administrador&p=espacos"> Listar Espaço</a></li>
							<li><a href="?perfil=administrador&p=listaprojetoespecial"> Listar Projeto Especial</a></li>
							<li><a href="?perfil=administrador&p=eventos"> Listar Eventos</a></li>
						<!--	<li><a href="?perfil=administrador&p=logsLocais"> Logs Locais</a></li> !-->
							<li><a href="?perfil=administrador&p=alteracoes"> Alterações</a></li>
   							<li><a href="?perfil=administrador&p=reabertura"> Reabrir eventos enviados</a></li>
							</li> </ul> <!-- Fim Menu administrativo!--> 
							<li><a href="?perfil=usuario">Gerenciar conta</a></li>
							<li><a href="?secao=ajuda">Ajuda</a></li>
                            <li><a href="../include/logoff.php">Sair</a></li>
							
						</ul>
					</div><!-- /dl-menuwrapper !-->
		</div>
<!-- fim Menu Área !-->
<?php

//include para painel administração
require "../funcoes/funcoesAdministrador.php"; //chamar funcoes do administrador
require "../funcoes/funcoesSiscontrat.php"; //chamar funcoes do administrador

?>
<?php
@ini_set('display_errors', '1');
error_reporting(E_ALL);
$con = bancoMysqli();
if(isset($_GET['p'])){
	$p = $_GET['p'];	
}else{
	$p = "inicio";
}

if(isset($_GET['atualizar'])){
	if($_GET['atualizar'] == 'agenda'){
		if(reloadAgenda()){
			$mensagem = "Agenda atualizada.";	
		}	
	}
}


switch($p){

case "inicio":
?>
	<section id="contact" class="home-section bg-white">
	<div class="container">
        <div class="row">
            <div class="col-md-offset-2 col-md-8">
                <div class="text-hide">
	                <h4>Escolha uma opção</h4>
					<p><?php if(isset($mensagem)){echo $mensagem;} ?></p>
                </div>
            </div>
        <div class="form-group">
            <div class="col-md-offset-2 col-md-8">
	            <a href="?perfil=administrador&p=users" class="btn btn-theme btn-lg btn-block">Listar usuários</a>
				<a href="?perfil=administrador&p=espacos" class="btn btn-theme btn-lg btn-block">Listar Espaços</a>
				<a href="?perfil=administrador&p=eventos" class="btn btn-theme btn-lg btn-block">Listar Eventos</a>
				<a href="?perfil=administrador&p=listaprojetoespecial" class="btn btn-theme btn-lg btn-block">Listar Projeto especial</a>
			<!--	<a href="?perfil=administrador&p=logsLocais" class="btn btn-theme btn-lg btn-block">Logs Locais</a> !-->
	<a href="?perfil=administrador&p=reabertura" class="btn btn-theme btn-lg btn-block">Reabrir pedidos enviados</a>
				<a href="?perfil=administrador&p=alteracoes" class="btn btn-theme btn-lg btn-block">Alterações</a>

  	        </div>
          </div>
        </div>
    </div>
	</section>  
<?php
// LISTA DE USUARIOS 
break;
 case "users":
 
	
	if(isset($_POST['apagar'])){
	$idApagar = $_POST['apagar'];
	$sql_apagar_registro = "UPDATE ig_usuario 
						SET publicado = 0 
						WHERE idUsuario = $idApagar";

	if(mysqli_query($con,$sql_apagar_registro)){	
		$mensagem = "Usuário apagado com sucesso!";
		gravarLog($sql_apagar_registro);
	}else{
		$mensagem = "Erro ao apagar o usuário...";	
	}		
}	

?> 

	<section id="list_items" class="home-section bg-white">
	 <div class="form-group">
            <div class="col-md-offset-2 col-md-8">		
		 <h2>Usuários Cadastrados</h2>
				<a href="?perfil=administrador&p=novoUser" class="btn btn-theme btn-lg btn-block">Inserir novo usuário</a>
  	        </div>
				</div> 
		<div class="container">
      			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="section-heading">					
					<h4>Selecione o usuário para editar.</h4>
                    <h5><?php if(isset($mensagem)){echo $mensagem;} ?></h5>
					</div>
				  </div>
			  </div>  

			<div class="table-responsive list_info">
                         <?php listuserAdministrador(""); ?>
			</div>
		</div>
	</section> 
<?php	
break; // FIM LISTA USUARIOS
case "novoUser": // INSERIR NOVO USUARIO 


	if(isset($_POST['carregar'])){
	$_SESSION['idUsuario'] = $_POST['carregar'];
}



	if(isset($_POST['atualizar'])){
		$nomeCompleto = $_POST['nomeCompleto'];
		$usuario = $_POST['usuario'];
		$existe = verificaExiste("ig_usuario","nomeUsuario",$usuario,"0");
		//$senha = MD5($_POST['senha']);
		$senha = MD5 ('igsis2015');
		$instituicao = $_POST['instituicao'];
		$telefone = $_POST['telefone'];
		$perfil = $_POST['papelusuario'];
		$email = $_POST['email'];
		$existe = verificaExiste("ig_usuario","email",$usuario,"0");
		$publicado = "1";
		if(isset($_POST['receberEmail'])){
			$receberEmail =	1;
		}else{
			$receberEmail =	0;
		}
			
	
		if($existe['numero'] == 0){
			$sql_inserir = "INSERT INTO `ig_usuario` (`idUsuario`, `ig_papelusuario_idPapelUsuario`, `senha`, `receberNotificacao`, `nomeUsuario`, `email`, `nomeCompleto`, `idInstituicao`, `telefone`, `publicado`) VALUES (NULL, '$perfil', '$senha', '$receberEmail', '$usuario', '$email', '$nomeCompleto', '$instituicao', '$telefone', '$publicado')";
			$query_inserir = mysqli_query($con,$sql_inserir);
			if($query_inserir){
				$mensagem = "Usuário inserido com sucesso";
			}else{
				$mensagem = "Erro ao inserir. Tente novamente.";
			}
		}
		else{
			$mensagem = "Usuário ou email já existente. Tente novamente.";
		}
	}
?>
<section id="inserirUser" class="home-section bg-white">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-md-10">
                <div class="text-hide">
                    <h3>Inserir Usuário</h3>
					<h3><?php if(isset($mensagem)){echo $mensagem;} ?></h3>
                </div>
            </div>
    	</div>
  <div class="row">
        <div class="col-md-offset-2 col-md-8">
	<form method="POST" action="?perfil=administrador&p=novoUser" class="form-horizontal" role="form">
               
					<!-- // Usuario !-->
			<div class="col-md-offset-1 col-md-10">  
			    <div class="form-group">
				<div class="col-md-offset-2 col-md-8">
                		<label>Nome Completo:</label>
                		<input type="text" name="nomeCompleto" class="form-control"id="nomeCompleto" value="" />  </div> 
                	<div class="col-md-offset-2 col-md-8">
                		<label>Usuario:</label>
                		<input type="text" name="usuario" class="form-control"id="usuario" />
                	</div>  <!-- // SENHA !-->
					<div class="col-md-offset-2 col-md-8">
                		<label>Senha:</label>
						<label>igsis2015</label>
               		</div> 	<!-- // Departamento !-->
					<div class="col-md-offset-2 col-md-8">	
                		<label>telefone:</label>
                		<input type="text" name="telefone" class="form-control"id="departamento" />
                	</div>  <!-- // ig_instituicao Puxada pela SESSASION do "CRIADOR" - Admin Local !-->
				<!-- // Perfil de Usuario !-->
					 <div class="col-md-offset-2 col-md-8">
					 <label>Instituição:</label>
						<select name="instituicao" class="form-control"  >
						<?php acessoInstituicao("ig_instituicao","",""); ?>
						</select>
					 </div>
					 <div class="col-md-offset-2 col-md-8">
                		<label>Acesso aos Perfil's:</label>
						<select name="papelusuario" class="form-control"  >
						<?php acessoPerfilUser("ig_papelusuario","3",""); ?>
						</select>
					</div>  <!--  // Email !-->
					<div class="col-md-offset-2 col-md-8">  
					<label>Email para cadastro:</label>
					<input type="text" name="email" class="form-control" id="email" value=""/>
					</div>
		            <div class="col-md-offset-2 col-md-8"> <!-- // Confirmação de Recebimento de Email !-->
            		  <label style="padding:0 10px 0 5px;">Receber Email de atualizações: </label><input type="checkbox" name="receberEmail" id="diasemana01"/>
            		</div> <!-- Fim de Preenchemento !-->  
					<!-- Botão de Confirmar cadastro !-->
					<div class="col-md-offset-2 col-md-8">
                    	<input type="hidden" name="atualizar" value="1"  />
                		<input type="submit" class="btn btn-theme btn-lg btn-block" value="Inserir Usuário"  />
					</div>
						</div>
				</div>
		</div>
	</form>
	<form method="POST" action="?perfil=administrador&p=users" class="form-horizontal"  role="form">
				<div class="col-md-offset-2 col-md-8">
					<input type="submit" class="btn btn-theme btn-lg btn-blcok" value="Lista de Usuário" />
					</div>
					</form>
					
			  
					  
					</div>
          
  </div>
</section>   

<?php	
break; // FIM INSERIR USUARIO
case "editarUser": // ATUALIZAR /EDITAR USUARIO 


if (isset ($_POST ['resetSenha'])) {
		$senha = MD5 ('igsis2015');
		$usuario = $_POST ['editarUser'];
		
	$sql_atualizar = "UPDATE `ig_usuario` SET
	`senha` = '$senha'
	WHERE `idUsuario` = '$usuario'";
			$con = bancoMysqli();
	if(mysqli_query ($con,$sql_atualizar)){
		$mensagem = "Senha reiniciado com sucesso";
			}else{
				$mensagem = "Erro ao reiniciar. Tente novamente.";
			}
	}
	
	// Atualiza o banco com as informações do post
	if(isset($_POST['atualizar'])){
		$usuario= $_POST ['idUsuario'];
		$nomeCompleto = $_POST['nomeCompleto'];
		$nomeUsuario = $_POST['nomeUsuario'];
		$existe = verificaExiste("ig_usuario","nomeUsuario",$usuario,"0");
		$telefone = $_POST['telefone'];
		$instituicao = $_SESSION['id_usuario = 1'] = $_POST['ig_instituicao_idInstituicao'];
		$perfil = $_POST['papelusuario'];
		$rf	=	$_POST['rf'];
		$email = $_POST['email'];	
		if(isset($_POST['receberEmail'])){
			$receberEmail =	1;
		}else{
			$receberEmail =	0;
		}	if($existe['numero'] == 0)
			{
				$sql_atualizar = "UPDATE `ig_usuario`SET
			`nomeCompleto`= '$nomeCompleto',
			`nomeUsuario`= '$nomeUsuario', 
				`telefone`= '$telefone',
				`idInstituicao` = '$instituicao',
			`ig_papelusuario_idPapelUsuario`= '$perfil',
			`rf`= '$rf',	
			`email`= '$email', 
			`receberNotificacao`= '$receberEmail'			
			WHERE `idUsuario` = '$usuario' ";
				$con = bancoMysqli();
			if(mysqli_query($con,$sql_atualizar)){ 
				$mensagem = "Usuário atualizado com sucesso";
			}else{
				$mensagem = "Erro ao editar. Tente novamente.";
			}
		}
		else{
			$mensagem = "Tente novamente.";
		}
	} 
	$recuperaUsuario = recuperaDados("ig_usuario",$_POST['editarUser'],"idUsuario"); 
?>
<section id="inserirUser" class="home-section bg-white">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-md-10">
                <div class="text-hide">
                    <h3>Editar Usuário</h3>
					<h3><?php if(isset($mensagem)){echo $mensagem;} ?></h3>
                </div>
            </div>
    	</div>
  <div class="row">
        <div class="col-md-offset-2 col-md-8">
	<form method="POST" action="?perfil=administrador&p=editarUser" class="form-horizontal" role="form">
          <input type="hidden" name="idUsuario"  value=<?php  echo $recuperaUsuario['idUsuario'] ?> />
					<!-- // Usuario !-->
			<div class="col-md-offset-1 col-md-10">  
			    <div class="form-group">
				<div class="col-md-offset-2 col-md-8">
                		<label>Nome Completo:</label>
                		<input type="text" name="nomeCompleto" class="form-control"id="nomeCompleto" value="<?php echo $recuperaUsuario['nomeCompleto'] ?>" />  </div> 
                	<div class="col-md-offset-2 col-md-8">
                		<label>Usuario:</label>
                		<input type="text" name="nomeUsuario" class="form-control"id="nomeUsuario" value="<?php echo $recuperaUsuario['nomeUsuario'] ?>" />
                	</div>  <!-- // SENHA !-->
						<!-- // Departamento !-->
					<div class="col-md-offset-2 col-md-8">	
                		<label>telefone:</label>
                		<input type="text" name="telefone" class="form-control"id="departamento" value="<?php echo $recuperaUsuario['telefone'] ?>" />
                	</div>  <!-- // Perfil de Usuario !-->
					<div class="col-md-offset-2 col-md-8">
                		<label>Instituição:</label>
                		<select name="ig_instituicao_idInstituicao" class="form-control"  >
						<?php instituicaoLocal("ig_instituicao",$recuperaUsuario['idInstituicao'],""); ?>
						</select>
                	</div>  <!-- // Perfil de Usuario !-->
					 <div class="col-md-offset-2 col-md-8">
					 <div class="col-md-offset-2 col-md-8">
                		<label>Acesso aos Perfil's :</label> </div>
						<select name="papelusuario" class="form-control"  >
						<?php acessoPerfilUser("ig_papelusuario",$recuperaUsuario['ig_papelusuario_idPapelUsuario'],""); ?>
						</select>
					</div>  <!--  // Regristro Funcional 'RF' !-->
					<div class="col-md-offset-2 col-md-8">  
					<label>RF:</label>
					<input type="text" name="rf" class="form-control" value="<?php echo $recuperaUsuario ['rf']?>"/>
					</div> <!--  // Email !-->
					<div class="col-md-offset-2 col-md-8">  
					<label>Email para cadastro:</label>
					<input type="text" name="email" class="form-control" id="email" value="<?php echo $recuperaUsuario ['email']?>"/>
					</div>
		            <div class="col-md-offset-2 col-md-8"> <!-- // Confirmação de Recebimento de Email !-->
            		  <label style="padding:0 10px 0 5px;">Receber Email de atualizações: </label><input type="checkbox" name="receberEmail" id="diasemana01"/>
            		</div> <!-- Fim de Preenchemento !-->  
					<!-- Botão de Confirmar cadastro !-->
					<div class="col-md-offset-2 col-md-8">
					<input type="hidden" name="editarUser" value="<?php echo $_POST['editarUser'] ?>"  />
                    	<input type="hidden" name="atualizar" value="1"  />
                		<input type="submit" class="btn btn-theme btn-lg btn-block" value="Atualizar Usuário"  />
					</div>
						        	
	</form>			
	</div>
	<form method="POST" action="?perfil=administrador&p=editarUser" class="form-horizontal" role="form">
<div class="col-md-offset-1 col-md-10">
                		<input type="hidden" name="editarUser" value="<?php echo $_POST['editarUser'] ?>"  />
						<input type="hidden" name="resetSenha" value="1"  />
						<input type="submit" class="btn btn-theme btn-lg btn-blcok" name="resetar_senha" value="Resetar Senha do usuario" /> <p> </p>
               		</div> 
</form>	
	<form method="POST" action="?perfil=administrador&p=users" class="form-horizontal" >
				<div class="col-md-offset-2 col-md-8">
					<input type="submit" class="btn btn-theme btn-lg btn-blcok" value="Lista de Usuário" />
				</div>
		</div>
	</div>	
	</form>	

	</div>    
</div>
</section>   
<?php
break; // FIM LISTA USUARIOS / INSERIR / ATUALIZAR
case "novoEspaco": // INSERIR NOVO ESPACO 
if(isset($_POST['cadastrar'])){
	
	
		$espaco = $_POST['espaco'];	
		$instituicao = $_POST['instituicao'];
			if($espaco == ''){  
			$mensagem = "<p>O campo espaco, está em branco e é obrigatório. Tente novamente.</a></p>"; 
							}
			else{
				$sqlverificar = "SELECT sala FROM ig_local WHERE idInstituicao = $instituicao AND espaco LIKE '$espaco'";
				$queryverificar = mysqli_query($con,$sqlverificar);
				$existe = mysqli_num_rows ($queryverificar);
				
				if ($existe == 0) // caso não esteja vazio
				{ //inserir no banco
					$sqlinserir= "INSERT INTO `ig_local` (`idLocal`,`sala`,`idInstituicao`,`publicado`) VALUES (NULL, '$espaco','$instituicao', 1)";
					$queryinserir = mysqli_query($con,$sqlinserir);
					if($queryinserir){
						$mensagem = "Inserido com sucesso.";
					}
					else { // erro ao inserir
						$mensagem= "Erro ao inserir.";
					}
				}
				else {  // espaço já existe retirado do comando $sqlverificar 
					$mensagem = "Espaço já existente.";
				}
		}					 
}
?>    
 <section id="inserirUser" class="home-section bg-white">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-md-10">
                <div class="text-hide">
                    <h3>Administrativo </h3> <h2> Inserir Novo Espaço</h3>
                    <h5><?php if(isset($mensagem)){echo $mensagem;} ?></h5>
                </div>
            </div>
    	</div>
  <div class="row">
        <div class="col-md-offset-2 col-md-8">
		     <form method="POST" action="?perfil=administrador&p=novoEspaco" class="form-horizontal" role="form">
					<!-- // Espaço existente !-->
			<div class="col-md-offset-1 col-md-10">  
			    <div class="form-group">
                	<div class="col-md-offset-2 col-md-8">
                		<label>Adicionar novo Espaço:</label>
                		<input type="text" name="espaco" class="form-control"id="espaco" value="" />
                	</div>  
					<div class="col-md-offset-2 col-md-8">
					 <label>Instituição:</label>
						<select name="instituicao" class="form-control"  >
						<?php acessoInstituicao("ig_instituicao","",""); ?>
						</select>
					 </div>
				 <div class="col-md-offset-2 col-md-8"> 
				 <label></label> <!-- Adicionar novo espaço !-->
            		</div>
						<!-- Botão de gravar !-->
					<div class="col-md-offset-2 col-md-8">
                    	<input type="hidden" name="cadastrar" value="1"  />
                		<input type="submit" class="btn btn-theme btn-lg btn-block" value="Inserir"  />
				    </div>
				</div>
		</div>
		</form>
				<form method="POST" action="?perfil=administrador&p=espacos" class="form-horizontal"  role="form">
				<div class="col-md-offset-2 col-md-8">
				<input type="submit" class="btn btn-theme btn-lg btn-block" value="lista de espaço"/>
				</div></form>
</div>
  </div>
    </div>  <!-- // FIM DE INSERIR ESPACOS !-->
	</section>
	<?php
break; // FIM ADICIONAR NOVO ESPACO
case "espacos": 
if(isset($_POST['apagar'])){
	$con = bancoMysqli();
	$idApagar = $_POST['apagar'];
	$sql_apagar_registro = "UPDATE ig_local SET publicado = 2 WHERE idLocal = $idApagar";

	if(mysqli_query($con,$sql_apagar_registro)){	
		$mensagem = "Espaço apagado com sucesso!";
		gravarLog($sql_apagar_registro);
	}else{
		$mensagem = "Erro ao apagar o evento...";	
	}
}// EDITAR / APAGAR ESPACOS
?>
<section id="list_items" class="home-section bg-white">
		 <div class="form-group">
            <div class="col-md-offset-2 col-md-8">		
			<h2>Lista de Espaços</h2>
				<a href="?perfil=administrador&p=novoEspaco" class="btn btn-theme btn-lg btn-block">Inserir novo espaço</a>
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
                         <?php espacoExistente ($_SESSION['idInstituicao']); ?>
			</div>
		</div>
		
	</section> <!--/#list_items-->
<?php
break; // FIM LISTA ESPACOS / INSERIR / ATUALIZAR
case "novoProjetoEspecial": // INSERIR NOVO PROJETO ESPECIAL 
if(isset($_POST['cadastrar'])){
	//$con = bancoMysqli();
	
		$projetoEspecial = $_POST['projetoEspecial'];	
			if($projetoEspecial == ''){  
			$mensagem = "<p>O campo projeto especial, está em branco e é obrigatório. Tente novamente.</a></p>"; 
							}
			else{
			$sqlverificar= "SELECT * FROM ig_projeto_especial WHERE projetoEspecial LIKE '$projetoEspecial'";
				$queryverificar= mysqli_query($con,$sqlverificar);
				$existe = mysqli_num_rows ($queryverificar);
				
				if ($existe == 0) // caso não esteja vazio
				{ //inserir no banco
					$sqlinserir= "INSERT INTO `ig_projeto_especial` (`idProjetoEspecial`,`projetoEspecial`,`publicado`) VALUES (NULL, '$projetoEspecial', 1)";
					$queryinserir= mysqli_query($con,$sqlinserir);
					if($queryinserir){
						$mensagem= "Inserido com sucesso.";
					}
					else { // erro ao inserir
						$mensagem= "Erro ao inserir.";
					}
				}
				else {  // espaço já existe retirado do comando $sqlverificar 
					$mensagem = "Projeto especial já existente.";
				}
		}					 
}
?>
<section id="inserirUser" class="home-section bg-white">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-md-10">
                <div class="text-hide">
                    <h3>Administrativo </h3> <h2> Inserir novo projeto especial</h3>
                    <h5><?php if(isset($mensagem)){echo $mensagem;} ?></h5>
                </div>
            </div>
    	</div>
  <div class="row">
        <div class="col-md-offset-2 col-md-8">
		     <form method="POST" action="?perfil=administrador&p=novoProjetoEspecial" class="form-horizontal" role="form">
					<!-- // Espaço existente !-->
			<div class="col-md-offset-1 col-md-10">  
			    <div class="form-group">
					<div class="col-md-offset-2 col-md-8">
                		<label>Projeto especial existentes:</label>
					<select name="listaespaco" class="form-control"  >
						<?php geraProjetoEspecial("ig_projeto_especial",""); ?>
						</select>	
					</div>
                	<div class="col-md-offset-2 col-md-8">
                		<label>Adicionar novo projeto especial:</label>
                		<input type="text" name="projetoEspecial" class="form-control"id="espaco" value="" />
                	</div>  
				 <div class="col-md-offset-2 col-md-8"> 
				 <label></label> <!-- Adicionar novo espaço !-->
            		</div>
						<!-- Botão de gravar !-->
					<div class="col-md-offset-2 col-md-8">
                    	<input type="hidden" name="cadastrar" value="1"  />
                		<input type="submit" class="btn btn-theme btn-lg btn-block" value="Adcionar"  />
						         		</div>
			</div>
		</div>
		</form>
				<form method="POST" action="?perfil=administrador&p=listaprojetoespecial" class="form-horizontal"  role="form">
				<div class="col-md-offset-2 col-md-8">
				<input type="submit" class="btn btn-theme btn-lg btn-block" value="lista de projeto especial"/>
				</div></form>
</div>
  </div>
    </div>
       <!-- // FIM DE INSERIR !-->
	</section>
	
<?php
break; // FIM ADICIONAR NOVO PROJETO ESPECIAL
case "listaprojetoespecial": 

if(isset($_POST['apagar'])){
	$con = bancoMysqli();
	$idApagar = $_POST['apagar'];
	$sql_apagar_registro = "UPDATE `ig_projeto_especial` SET `publicado` = '0' WHERE idProjetoEspecial = $idApagar";
		if(mysqli_query($con,$sql_apagar_registro)){	
		$mensagem = "projeto especial apagado com sucesso!";
		gravarLog($sql_apagar_registro);
	}else{
		$mensagem = "Erro ao apagar o projeto especial...";	
	}
							}	// EDITAR / APAGAR PROJETO ESPECIAL
?>
<section id="list_items" class="home-section bg-white">
		 <div class="form-group">
            <div class="col-md-offset-2 col-md-8">		
			<h2>Lista de projeto especial</h2>
				<a href="?perfil=administrador&p=novoProjetoEspecial" class="btn btn-theme btn-lg btn-block">Inserir novo projeto especial</a>
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
                         <?php projetoEspecialExistente ($_SESSION['perfil']); ?>
			</div>
		</div>
</section> <!--/#list_items-->
<?php	
break; // FIM PROJETO ESPECIAL
case "eventos": // LISTAR NOVOS EVENTOS
if(isset($_POST['apagar'])){
	$idApagar = $_POST['apagar'];
	$sql_apagar_registro = "UPDATE ig_evento SET publicado = 3 WHERE idEvento = $idApagar";

	if(mysqli_query($con,$sql_apagar_registro)){	
		$mensagem = "Evento apagado com sucesso!";
		gravarLog($sql_apagar_registro);
	}else{
		$mensagem = "Erro ao apagar o evento...";	
	}
}
?>
<section id="list_items" class="home-section bg-white">
		<div class="container">
      			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="section-heading">
					 <h2>Eventos excluidos</h2>
					<h4>Selecione o evento para recuperar ou editar.</h4>
                    <h5><?php if(isset($mensagem)){echo $mensagem;} ?></h5>
					</div>
				  </div>
			  </div>  

			<div class="table-responsive list_info">
                         <?php listaEventosAdministrador($_SESSION['idInstituicao']); ?>
			</div>
		</div>
	</section> <!--/#list_items-->



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
	$sql_reabrir = "UPDATE ig_evento SET dataEnvio = NULL WHERE idEvento = '$idEvento'";
	$query_reabrir = mysqli_query($con,$sql_reabrir);
	if($query_reabrir){
		$sql_pedido = "UPDATE igsis_pedido_contratacao SET estado = NULL WHERE idEvento = '$idEvento'";
		$query_pedido = mysqli_query($con,$sql_pedido);
		if($query_pedido){
			$evento = recuperaDados("ig_evento",$idEvento,"idEvento");
			$mensagem = "Evento ".$evento['nomeEvento']."($idEvento) reaberto com sucesso";	
		}
	} 
	
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
						$sql_lista = "SELECT * FROM ig_evento WHERE publicado = '1' AND dataEnvio IS NOT NULL ORDER BY idEvento DESC";
						$query_lista = mysqli_query($con,$sql_lista);
						$num = mysqli_num_rows($query_lista);
?>
			<h5><?php echo $num ?> eventos enviados.</h5>
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
			<form method='POST' action='?perfil=administrador&p=reabertura'>
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
break; // FIM EVENTOS
case "logsLocais": // VISUALIZAR LOGS DE USUARIO
?>
		<section id="list_items" class="home-section bg-white">
		<div class="container">
      			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="section-heading">
					 <h2>Logs de Usuários</h2>
					<h4>Selecione o Log recuperar ou editar.</h4>
                    <h5><?php if(isset($mensagem)){echo $mensagem;} ?></h5>
					</div>
				  </div>
			  </div>  

			<div class="table-responsive list_info">
                         <?php listaLogAdministrador($_SESSION['perfil']); ?>
			</div>
		</div>
	</section> <!--/#list_items-->	
	
<?php
break; // FIM LOGS
case "formularioalteracoes": // inicio dos formularios de alterações 
	?> <?php
	
if(isset($_POST['carregar'])){
	$_SESSION['idChamado'] = $_POST['carregar'];
}
// Atualiza o banco com as informações do post
if(isset ($_POST ['atualizar'])) {
		$idChamado = $_POST['idChamado'];
		$titulo = $_POST ['listaTitulo'];
		$status = $_POST ['estado'];
		$nota = $_POST ['nota'];
		//$nome = $_POST ['nomeCompleto'];
		
		$sql_atualizar = "UPDATE `igsis_chamado` SET
		`titulo`= '$titulo',
		`estado`= '$status',
		`nota`= '$nota'
		
		WHERE `idChamado` ='$idChamado'";
		
		$con = bancoMysqli();
		if(mysqli_query($con,$sql_atualizar)){
			$mensagem = "Atualizado com Sucesso.";
			gravarLog($sql_atualizar);
		} else {
			$mensagem = "Erro ao gravar atualização... Tente novamente.";
		}
}
	$recuperaChamado = recuperaDados("igsis_chamado", $_POST['carregaChamado'],"idChamado");
	?>
<section id="chamado" class="home-section bg-white">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-md-10">
                <div class="text-hide">
                    <h3>CHAMADOS</h3>
					<h3><?php if(isset($mensagem)){echo $mensagem;} ?></h3>
                </div>
            </div>
    	</div>
  <div class="row">
        <div class="col-md-offset-2 col-md-8">
	<form method="POST" action="?perfil=administrador&p=formularioalteracoes" class="form-horizontal" role="form">
      
					<!-- // numero do chamado !-->
			<div class="col-md-offset-1 col-md-10">  
			    <div class="form-group">
				<div class="col-md-offset-2 col-md-8">
                		<label>ID Chamado:</label>				
                		<input type="text" readonly name="idChamado" class="form-control"id="idChamado" value="<?php echo $recuperaChamado['idChamado'] ?>" />  </div> 
                	<div  class="col-md-offset-2 col-md-8">
                		<label>Titulo chamado:</label>
                		<select name="listaTitulo" class="form-control">
						<?php geraTituloChamado("igsis_tipo_chamado",$recuperaChamado ['titulo'],""); ?>	</select>	
                	</div>  
					<div class="col-md-offset-2 col-md-8">	
                		<label>Criado por:</label>
                		<select disabled name="nomeCompleto" class="form-control" <?php geraUsuario("ig_usuario",$recuperaChamado['idUsuario'],"");?> </select>
                </div> <!-- Usuário que preencheou o chamado !--> 
					<div class="col-md-offset-2 col-md-8">	
                		<label>Data do chamado:</label>
                		<input type="text" readonly name="data" onblur="validate()" class="form-control"id="data" value="<?php echo $recuperaChamado['data'] ?>" />
						</div><!--  // data !-->
					 <div class="form-group">
            	<div class="col-md-offset-2 col-md-8">
            		<label>Descrição:</label>
            		<textarea name="descricao" readonly class="form-control" rows="10"> <?php echo $recuperaChamado['descricao'] ?></textarea>
            	</div>  	<div class="col-md-offset-2 col-md-8">
            		<label>Justificativa:</label>
            		<textarea name="justificativa" readonly class="form-control" rows="10"> <?php echo $recuperaChamado['justificativa'] ?></textarea>
            	</div> <!-- Preenchemento feito pelo usuário !-->  
            </div>
					<div class="col-md-offset-2 col-md-8">	
                		<label>Status:</label>
                		<select name="estado" class="form-control"  >
						<?php geraStatusChamado("igsis_tipo_chamado",$recuperaChamado['estado'],""); ?> </select>                </div> 
				 <div class="form-group">
            	<div class="col-md-offset-2 col-md-8">
            		<label>Notas adicionais:</label>
            		<textarea name="nota" class="form-control" rows="10"> <?php echo $recuperaChamado['nota'] ?></textarea>
            	</div> <!-- Fim de Preenchemento !-->  
            </div>
			</div>	
				<div class="col-md-offset-2 col-md-8">
                    	<input type="hidden" name="carregaChamado" value="<?php echo $_POST['carregaChamado'] ?>"  />
						<input type="hidden" name="atualizar" value="1" />
                		<input type="submit" class="btn btn-theme btn-lg btn-block" value="	concluir"  />
					</div>
		</form>				
			</div>
			<form method="POST" action="?perfil=administrador&p=alteracoes" class="form-horizontal"  role="form">
				<div class="col-md-offset-2 col-md-8">
					<input type="submit" class="btn btn-theme btn-lg btn-blcok" value="Lista de chamados" />
					</div>
				</form>
			</div> 
			</div>
		</div>
	
</section>   
	
		<?php	
break; // FIM FORM ALTERÇÕES
case "alteracoes": // INICIO DE ALTERAÇÕES

?>
 		  <section id="list_items" class="home-section bg-white">
		<div class="container">
      			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="section-heading">
					 <h2>Chamados</h2>
					<h4>Selecione o chamado para visualizar.</h4>
                    <h5><?php if(isset($mensagem)){echo $mensagem;} ?></h5>
					</div>
				  </div>
			  </div>  

			<div class="table-responsive list_info">
                         <?php listaAlteracoesAdmin($_SESSION['idInstituicao']); ?>
			</div>
		</div>
	</section> <!--/#list_items-->
<?php 
break;
} //fim da switch ?>
<?php // var_dump ($_POST)
?>
