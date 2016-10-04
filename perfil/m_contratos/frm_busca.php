<?php include 'includes/menu.php';?>

<?php
if(isset($_GET['b'])){
	$b = $_GET['b'];	
}else{
	$b = 'inicial';
}

switch($b){
case 'inicial':
if(isset($_POST['pesquisar'])){
$id = trim($_POST['id']);
$evento = trim($_POST['evento']);
$fiscal = $_POST['fiscal'];
$tipo = $_POST['tipo'];
$instituicao = $_POST['instituicao'];
$estado = $_POST['estado'];
$operador = $_POST['operador'];
$juridico = $_POST['juridico'];
$projeto = $_POST['projeto'];
if($id == "" AND $evento == "" AND $fiscal == 0 AND $tipo == 0 AND $instituicao == 0 AND $estado == 0 AND $operador == 0 AND $juridico == 0 AND $projeto == 0){ ?>
	 <section id="services" class="home-section bg-white">
		<div class="container">
			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="section-heading">
					 <h2>Busca por pedido</h2>
                    <p>É preciso ao menos um critério de busca ou você pesquisou por um pedido inexistente. Tente novamente.</p>

					</div>
				  </div>
			  </div>
			  
	        <div class="row">
            <div class="form-group">
            	<div class="col-md-offset-2 col-md-8">
            <h5><?php if(isset($mensagem)){ echo $mensagem; } ?>
                      <form method="POST" action="?perfil=contratos&p=frm_busca" class="form-horizontal" role="form">
            		<label>Código do Pedido</label>
            		<input type="text" name="id" class="form-control" id="palavras" placeholder="Insira o Código do Pedido" ><br />
            		<label>Objeto/Evento</label>
            		<input type="text" name="evento" class="form-control" id="palavras" placeholder="Insira o objeto" ><br />

     
            			          
    	        <label>Fiscal, suplente ou usuário que cadastrou o evento</label>
					<select class="form-control" name="fiscal" id="inputSubject" >
					<option value="0"></option>	
					<?php echo opcaoUsuario($_SESSION['idInstituicao'],"") ?>
                    </select>
                    <br />
                     <label>Tipo de evento</label>
                    <select class="form-control" name="tipo" id="inputSubject" >
					<option value="0"></option>		                
						<?php echo geraOpcao("ig_tipo_evento","","") ?>
                    </select>	
                    <br />
                    <label>Instituição</label>
                    <select class="form-control" name="instituicao" id="inputSubject" >
                   <option value="0"></option>
						<?php echo geraOpcao("ig_instituicao","","") ?>
                    </select>		
                    <br />
                    <label>Status do pedido</label>
                    <select class="form-control" name="estado" id="inputSubject" >
                   <option value='0'></option>


		<?php echo geraOpcao("sis_estado","","") ?>
                    </select>	
                    
                    <label>Operador do Contrato</label>
                    <select class="form-control" name="operador" id="inputSubject" >
                   <option value='0'></option>
	<?php  geraOpcaoContrato(""); ?>
                    </select>	
     <label>Tipo de Relação Jurídica</label>
                    <select class="form-control" name="juridico" id="inputSubject" >
                   <option value='0'></option>
	<?php  geraOpcao("ig_modalidade","",""); ?>
                    </select>	
					
					<label>Tipo de Projeto</label>
                    <select class="form-control" name="projeto" id="inputSubject" >
                   <option value='0'></option>
	<?php  geraOpcao("ig_projeto_especial","",""); ?>
                    </select>


            	</div>
             </div>
				<br />             
	            <div class="form-group">
		            <div class="col-md-offset-2 col-md-8">
                	<input type="hidden" name="pesquisar" value="1" />
    		        <input type="submit" class="btn btn-theme btn-lg btn-block" value="Pesquisar">
                    </form>
        	    	</div>
        	    </div>

	</section>
<?php
}else{
$con = bancoMysqli();
	$sql_existe = "SELECT idPedidoContratacao,idEvento,estado FROM igsis_pedido_contratacao WHERE idEvento = '$evento' AND publicado = '1' AND estado IS NOT NULL ORDER BY idPedidoContratacao DESC";
	$query_existe = mysqli_query($con, $sql_existe);
	$num_registro = mysqli_num_rows($query_existe);
if($id != "" AND $num_registro > 0){ // Foi inserido o número do pedido
		$pedido = recuperaDados("igsis_pedido_contratacao",$id,"idPedidoContratacao");
		if($pedido['estado'] > 1){
			
		$evento = recuperaDados("ig_evento",$pedido['idEvento'],"idEvento"); //$tabela,$idEvento,$campo
		$projeto = recuperaDados("ig_projeto_especial",$pedido['idEvento'],"projetoEspecial");
		$usuario = recuperaDados("ig_usuario",$evento['idUsuario'],"idUsuario");
		$instituicao = recuperaDados("ig_instituicao",$evento['idInstituicao'],"idInstituicao");
		$local = listaLocais($pedido['idEvento']);
		$periodo = retornaPeriodo($pedido['idEvento']);
		$duracao = retornaDuracao($pedido['idEvento']);
		$pessoa = recuperaPessoa($pedido['idPessoa'],$pedido['tipoPessoa']);
		$fiscal = recuperaUsuario($evento['idResponsavel']);
		$suplente = recuperaUsuario($evento['suplente']);
		$protocolo = ""; //recuperaDados("sis_protocolo",$pedido['idEvento'],"idEvento");
					$operador = recuperaUsuario($pedido['idContratos']);
		if($pedido['parcelas'] > 1){
			$valorTotal = somaParcela($pedido['idPedidoContratacao'],$pedido['parcelas']);
			$formaPagamento = txtParcelas($pedido['idPedidoContratacao'],$pedido['parcelas']);	
		}else{
			$valorTotal = $pedido['valor'];
			$formaPagamento = $pedido['formaPagamento'];
		}
	
	$x[0]['id']= $pedido['idPedidoContratacao'];
	$x[0]['objeto'] = retornaTipo($evento['ig_tipo_evento_idTipoEvento'])." - ".$evento['nomeEvento'];
	switch($pedido['tipoPessoa']){
		case 1:
		$pessoa = recuperaDados("sis_pessoa_fisica",$pedido['idPessoa'],"Id_PessoaFisica");
		$x[0]['proponente'] = $pessoa['Nome'];
		$x[0]['tipo'] = "Física";
		$x[0]['pessoa'] = 1;
		break;
		case 2:
		$pessoa = recuperaDados("sis_pessoa_juridica",$pedido['idPessoa'],"Id_PessoaJuridica");
		$x[0]['proponente'] = $pessoa['RazaoSocial'];
		$x[0]['tipo'] = "Jurídica";
		$x[0]['pessoa'] = 2;

		break;
		case 4:
		$pessoa = recuperaDados("sis_pessoa_fisica",$pedido['idPessoa'],"Id_PessoaFisica");
		$x[0]['proponente'] = $pessoa['Nome'];
		$x[0]['tipo'] = "Formação";
		$x[0]['pessoa'] = 4;

		break;
	}
	$x[0]['local'] = substr($local,1);
	$x[0]['instituicao'] = $instituicao['sigla'];
	$x[0]['periodo'] = $periodo;
	$x[0]['status'] = $pedido['estado'];
	$x[0]['operador'] = $operador['nomeCompleto'];	
	$x['num'] = 1;
	}else{
			$x['num'] = 0;
		
	}
}else{ //Não foi inserido o número do pedido
		if($evento != ''){
			$filtro_evento = " AND nomeEvento LIKE '%$evento%' OR autor LIKE '%$evento%' ";
		}else{
			$filtro_evento = "";
			
		}
		if($fiscal != 0){
			$filtro_fiscal = " AND (idResponsavel = '$fiscal' OR suplente = '$fiscal' OR idUsuario = '$fiscal' )";	
		}else{
			$filtro_fiscal = "";	
		}
		
		if($tipo != 0){
			$filtro_tipo = " AND ig_tipo_evento_idTipoEvento = '$tipo' ";	
		}else{
			$filtro_tipo = "";	
		}
		
		if($instituicao != 0){
			$filtro_instituicao = " AND idInstituicao = '$instituicao' ";	
		}else{
			$filtro_instituicao = "";	
		}
		
		if($estado == "" OR $estado == 0){
			$filtro_status = " AND estado IS NOT NULL ";	
	
		}else{
			$filtro_status = " AND igsis_pedido_contratacao.estado = '$estado'  ";	

		}
		
		if($operador == 0){
			$filtro_operador = "";	
	
		}else{
			$filtro_operador = " AND idContratos = '$operador'  ";	

		}
		
		if($juridico == 0){
			$filtro_juridico = "";	
	
		}else{
			$filtro_juridico = " AND ig_modalidade_IdModalidade = '$juridico'  ";	

		}
		if($projeto == 0){
			$filtro_projeto = " ";	
	
		}else{
			$filtro_projeto = " AND ig_evento.projetoEspecial = '$projeto'  ";	

		}
				
		$sql_evento = "SELECT * FROM ig_evento, igsis_pedido_contratacao WHERE ig_evento.publicado = '1' AND igsis_pedido_contratacao.publicado = '1' AND igsis_pedido_contratacao.idEvento = ig_evento.idEvento $filtro_evento $filtro_fiscal $filtro_tipo $filtro_instituicao $filtro_juridico $filtro_projeto ORDER BY ig_evento.idEvento DESC ";
		$query_evento = mysqli_query($con,$sql_evento);
		$i = 0;
		while($evento = mysqli_fetch_array($query_evento)){
			$idEvento = $evento['idEvento'];	
			$sql_existe = "SELECT * FROM igsis_pedido_contratacao WHERE idEvento = '$idEvento' AND publicado = '1' $filtro_status ORDER BY idPedidoContratacao DESC" ;
			$query_existe = mysqli_query($con, $sql_existe);
			if(mysqli_num_rows($query_existe) > 0)
			{
			while($ped = mysqli_fetch_array($query_existe)){	
			$pedido = recuperaDados("igsis_pedido_contratacao",$ped['idPedidoContratacao'],"idPedidoContratacao");
			$evento = recuperaDados("ig_evento",$pedido['idEvento'],"idEvento"); //$tabela,$idEvento,$campo
			$projeto = recuperaDados("ig_projeto_especial",$pedido['idEvento'],"projetoEspecial");
			$usuario = recuperaDados("ig_usuario",$evento['idUsuario'],"idUsuario");
			$instituicao = recuperaDados("ig_instituicao",$evento['idInstituicao'],"idInstituicao");
			$local = listaLocais($pedido['idEvento']);
			$periodo = retornaPeriodo($pedido['idEvento']);
			$duracao = retornaDuracao($pedido['idEvento']);
			$pessoa = recuperaPessoa($pedido['idPessoa'],$pedido['tipoPessoa']);
			$fiscal = recuperaUsuario($evento['idResponsavel']);
			$suplente = recuperaUsuario($evento['suplente']);
			$protocolo = ""; //recuperaDados("sis_protocolo",$pedido['idEvento'],"idEvento");
			$operador = recuperaUsuario($pedido['idContratos']);
			if($pedido['parcelas'] > 1){
				$valorTotal = somaParcela($pedido['idPedidoContratacao'],$pedido['parcelas']);
				$formaPagamento = txtParcelas($pedido['idPedidoContratacao'],$pedido['parcelas']);	
			}else{
				$valorTotal = $pedido['valor'];
				$formaPagamento = $pedido['formaPagamento'];
			}
			if($pedido['publicado'] == 1){		
			$x[$i]['id']= $pedido['idPedidoContratacao'];
			$x[$i]['objeto'] = retornaTipo($evento['ig_tipo_evento_idTipoEvento'])." - ".$evento['nomeEvento'];
			switch($pedido['tipoPessoa']){
			case 1:
				$pessoa = recuperaDados("sis_pessoa_fisica",$pedido['idPessoa'],"Id_PessoaFisica");
				$x[$i]['proponente'] = $pessoa['Nome'];
				$x[$i]['tipo'] = "Física";
				$x[$i]['pessoa'] = 1;
				break;
			case 2:
				$pessoa = recuperaDados("sis_pessoa_juridica",$pedido['idPessoa'],"Id_PessoaJuridica");
				$x[$i]['proponente'] = $pessoa['RazaoSocial'];
				$x[$i]['tipo'] = "Jurídica";
				$x[$i]['pessoa'] = 2;
				break;
			case 4:
				$pessoa = recuperaDados("sis_pessoa_fisica",$pedido['idPessoa'],"Id_PessoaFisica");
				$x[$i]['proponente'] = $pessoa['Nome'];
				$x[$i]['tipo'] = "Formação";
				$x[$i]['pessoa'] = 4;
				break;
			}
			$x[$i]['local'] = substr($local,1);
			$x[$i]['instituicao'] = $instituicao['sigla'];
			$x[$i]['periodo'] = $periodo;
			$x[$i]['status'] = $pedido['estado'];	
			$x[$i]['operador'] = $operador['nomeCompleto'];		
			$i++;
			}
			}
			}
		}
		$x['num'] = $i;
		
}
} 



$mensagem = "Foram encontradas ".$x['num']." pedido(s) de contratação.";
?>
<br />
<br />
	<section id="list_items">
		<div class="container">
			 <h3>Resultado da busca</3>
             <h5>Foram encontrados <?php echo $x['num']; ?> pedidos de contratação.</h5>
               <h5><a href="?perfil=contratos&p=frm_busca">Fazer outra busca</a></h5>
			<div class="table-responsive list_info">
			<?php if($x['num'] == 0){ ?>
			
			<?php }else{ ?>
				<table class="table table-condensed">
					<thead>
						<tr class="list_menu">
							<td>Codigo do Pedido</td>
							<td>Proponente</td>
							<td>Tipo</td>
							<td>Objeto</td>
							<td width="20%">Local</td>
                            <td>Instituição</td>
							<td>Periodo</td>
							<td>Status</td>
   							<td>Operador</td>
						</tr>
					</thead>
					<tbody>
<?php

$data=date('Y');
for($h = 0; $h < $x['num']; $h++)
 {
	 $status = recuperaDados("sis_estado",$x[$h]['status'],"idEstado");
	switch($x[$h]['pessoa'])
	{
	case 1:
		echo "<tr><td class='lista'> <a href='?perfil=contratos&p=frm_edita_propostapf&id_ped=".$x[$h]['id']."'>".$x[$h]['id']."</a></td>";
	break;
	case 2:
		echo "<tr><td class='lista'> <a href='?perfil=contratos&p=frm_edita_propostapj&id_ped=".$x[$h]['id']."'>".$x[$h]['id']."</a></td>";
	break;
	case 4:
		echo "<tr><td class='lista'> <a href='?perfil=contratos&p=frm_edita_proposta_formacao&id_ped=".$x[$h]['id']."'>".$x[$h]['id']."</a></td>";
	
	break;	
	}
	echo '<td class="list_description">'.$x[$h]['proponente'].					'</td> ';
	echo '<td class="list_description">'.$x[$h]['tipo'].					'</td> ';
	echo '<td class="list_description">'.$x[$h]['objeto'].						'</td> ';
	echo '<td class="list_description">'.$x[$h]['local'].				'</td> ';
	echo '<td class="list_description">'.$x[$h]['instituicao'].				'</td> ';
	echo '<td class="list_description">'.$x[$h]['periodo'].						'</td> ';
	echo '<td class="list_description">'.$status['estado'].						'</td> ';
	echo '<td class="list_description">'.$x[$h]['operador'].						'</td> </tr>';

	}
?>
	
					
					</tbody>
				</table>
			<?php } ?>		
            <?php 
			if($_SESSION['perfil'] == 1){
				//var_dump($x);	
			}
			?>
		</div>
			
		</div>
	</section>


<?php
}else{
?>
	 <section id="services" class="home-section bg-white">
		<div class="container">
			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="section-heading">
					 <h2>Busca por pedido</h2>
                    

					</div>
				  </div>
			  </div>
			  
	        <div class="row">
            <div class="form-group">
            	<div class="col-md-offset-2 col-md-8">
            <h5><?php if(isset($mensagem)){ echo $mensagem; } ?>
                        <form method="POST" action="?perfil=contratos&p=frm_busca" class="form-horizontal" role="form">
            		<label>Código do Pedido</label>
            		<input type="text" name="id" class="form-control" id="palavras" placeholder="Insira o Código do Pedido" ><br />
            		<label>Objeto/Evento</label>
            		<input type="text" name="evento" class="form-control" id="palavras" placeholder="Insira o objeto" ><br />

     
            			          
    	        <label>Fiscal, suplente ou usuário que cadastrou o evento</label>
					<select class="form-control" name="fiscal" id="inputSubject" >
					<option value="0"></option>	
					<?php echo opcaoUsuario($_SESSION['idInstituicao'],"") ?>
                    </select>
                    <br />
                     <label>Tipo de evento</label>
                    <select class="form-control" name="tipo" id="inputSubject" >
					<option value="0"></option>		                
						<?php echo geraOpcao("ig_tipo_evento","","") ?>
                    </select>	
                    <br />
                    <label>Instituição</label>
                    <select class="form-control" name="instituicao" id="inputSubject" >
                   <option value="0"></option>
						<?php echo geraOpcao("ig_instituicao","","") ?>
                    </select>	
                    <br />
                                        <label>Status do pedido</label>
                    <select class="form-control" name="estado" id="inputSubject" >
                   <option value=""></option>
		<?php echo geraOpcao("sis_estado","","") ?>
                    </select>	<br />
                    
	                    <label>Operador do Contrato</label>
                    <select class="form-control" name="operador" id="inputSubject" >
                   <option value='0'></option>
	<?php  geraOpcaoContrato(""); ?>
                    </select>
                    <br />	
     <label>Tipo de Relação Jurídica</label>
                    <select class="form-control" name="juridico" id="inputSubject" >
                   <option value='0'></option>
	<?php  geraOpcao("ig_modalidade","",""); ?>
                    </select>
					
					<label>Tipo de Projeto</label>
                    <select class="form-control" name="projeto" id="inputSubject" >
                   <option value='0'></option>
	<?php  geraOpcao("ig_projeto_especial","",""); ?>
                    </select>

            	</div>
             </div>
             

				<br />             
	            <div class="form-group">
		            <div class="col-md-offset-2 col-md-8">
                	<input type="hidden" name="pesquisar" value="1" />
    		        <input type="submit" class="btn btn-theme btn-lg btn-block" value="Pesquisar">
                    </form>
        	    	</div>
        	    </div>
             </div>
	</section>               


<?php } ?>

<?php
break;
case 'periodo': //
?>
<?php


	if($_POST['inicio'] == "" OR $_POST['final'] == ""){ ?>
	 <section id="services" class="home-section bg-white">
		<div class="container">
			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="section-heading">
					 <h2>Busca por pedido</h2>
                    <p>É preciso ao menos um critério de busca ou você pesquisou por um pedido inexistente. Tente novamente.</p>

					</div>
				  </div>
			  </div>
			  
	        <div class="row">
            <div class="form-group">
            	<div class="col-md-offset-2 col-md-8">
            <h5><?php if(isset($mensagem)){ echo $mensagem; } ?>
                      <form method="POST" action="?perfil=contratos&p=frm_busca" class="form-horizontal" role="form">
            		<label>Código do Pedido</label>
            		<input type="text" name="id" class="form-control" id="palavras" placeholder="Insira o Código do Pedido" ><br />
            		<label>Objeto/Evento</label>
            		<input type="text" name="evento" class="form-control" id="palavras" placeholder="Insira o objeto" ><br />

     
            			          
    	        <label>Fiscal, suplente ou usuário que cadastrou o evento</label>
					<select class="form-control" name="fiscal" id="inputSubject" >
					<option value="0"></option>	
					<?php echo opcaoUsuario($_SESSION['idInstituicao'],"") ?>
                    </select>
                    <br />
                     <label>Tipo de evento</label>
                    <select class="form-control" name="tipo" id="inputSubject" >
					<option value="0"></option>		                
						<?php echo geraOpcao("ig_tipo_evento","","") ?>
                    </select>	
                    <br />
                    <label>Instituição</label>
                    <select class="form-control" name="instituicao" id="inputSubject" >
                   <option value="0"></option>
						<?php echo geraOpcao("ig_instituicao","","") ?>
                    </select>		
                    <br />
                    <label>Status do pedido</label>
                    <select class="form-control" name="estado" id="inputSubject" >
                   <option value='0'></option>


		<?php echo geraOpcao("sis_estado","","") ?>
                    </select>	
                    
                    <label>Operador do Contrato</label>
                    <select class="form-control" name="operador" id="inputSubject" >
                   <option value='0'></option>
	<?php  geraOpcaoContrato(""); ?>
                    </select>	
     <label>Tipo de Relação Jurídica</label>
                    <select class="form-control" name="juridico" id="inputSubject" >
                   <option value='0'></option>
	<?php  geraOpcao("ig_modalidade","",""); ?>
                    </select>	


            	</div>
             </div>
				<br />             
	            <div class="form-group">
		            <div class="col-md-offset-2 col-md-8">
                	<input type="hidden" name="pesquisar" value="1" />
    		        <input type="submit" class="btn btn-theme btn-lg btn-block" value="Pesquisar">
                    </form>
        	    	</div>
        	    </div>
                
			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="section-heading">
					 <h2>Busca por periodo</h2>
                                        <p>O sistema varre os pedidos de contratação e informa quais terão início no período da busca.<br />
                    É preciso definir o início e o fim do período a se fazer a busca. <br /> Não funciona combinado com a busca acima.</p>


					</div>
				  </div>
			  </div>
			  
	        <div class="row">
            <div class="form-group">
            	<div class="col-md-offset-2 col-md-8">
            <h5><?php if(isset($mensagem)){ echo $mensagem; } ?>
            <form method="POST" action="?perfil=contratos&p=frm_busca&b=periodo" class="form-horizontal" role="form">
                <div class="form-group">
                	<div class="col-md-offset-2 col-md-6">
               			 <label>Data início *</label>
                		<input type="text" name="inicio" class="form-control" id="datepicker01" placeholder="">
               		 </div>
                	<div class=" col-md-6">
                		<label>Data encerramento *</label>
                		<input type="text" name="final" class="form-control" id="datepicker02"  placeholder="">
               		</div>
                </div>
                            

            	</div>
             </div>
				<br />             
	            <div class="form-group">
		            <div class="col-md-offset-2 col-md-8">
                	<input type="hidden" name="periodo" value="1" />
    		        <input type="submit" class="btn btn-theme btn-lg btn-block" value="Pesquisar">
                    </form>
        	    	</div>
        	    </div>


            </div>
	</section>
<?php
		
	}else{ // Caso as datas seja válidas
		$inicio = exibirDataMysql($_POST['inicio']);
		$final = exibirDataMysql($_POST['final']);	
		$con = bancoMysqli();
		$sql_evento = "SELECT DISTINCT idEvento FROM ig_ocorrencia WHERE dataInicio >= '$inicio' AND dataInicio <= '$final' AND publicado = '1' ORDER BY dataInicio ASC ";
		$query_evento = mysqli_query($con,$sql_evento);
		$i = 0;
		while($evento = mysqli_fetch_array($query_evento)){
			
			$idEvento = $evento['idEvento'];
			$evento = recuperaDados("ig_evento",$evento['idEvento'],"idEvento");
			if($evento['dataEnvio'] != NULL){	
			$sql_existe = "SELECT idPedidoContratacao,idEvento,estado FROM igsis_pedido_contratacao WHERE idEvento = '$idEvento' AND publicado = '1'";
			$query_existe = mysqli_query($con, $sql_existe);
			if(mysqli_num_rows($query_existe) > 0)
			{
//$tabela,$idEvento,$campo
			$usuario = recuperaDados("ig_usuario",$evento['idUsuario'],"idUsuario");
			$instituicao = recuperaDados("ig_instituicao",$evento['idInstituicao'],"idInstituicao");
			$fiscal = recuperaUsuario($evento['idResponsavel']);
			$suplente = recuperaUsuario($evento['suplente']);
			$protocolo = ""; //recuperaDados("sis_protocolo",$pedido['idEvento'],"idEvento");

			$sql_pedido = "SELECT * FROM igsis_pedido_contratacao WHERE publicado = '1' AND idEvento = '$idEvento'";
			$query_pedido = mysqli_query($con,$sql_pedido);
			while($ped = mysqli_fetch_array($query_pedido)){
				
			$pedido = recuperaDados("igsis_pedido_contratacao",$ped['idPedidoContratacao'],"idEvento");
			if($pedido['estado'] != NULL){
			$local = listaLocais($pedido['idEvento']);
			$periodo = retornaPeriodo($pedido['idEvento']);
			$duracao = retornaDuracao($pedido['idEvento']);
			$pessoa = recuperaPessoa($pedido['idPessoa'],$pedido['tipoPessoa']);
			$operador = recuperaUsuario($pedido['idContratos']);
			if($pedido['parcelas'] > 1){
				$valorTotal = somaParcela($pedido['idPedidoContratacao'],$pedido['parcelas']);
				$formaPagamento = txtParcelas($pedido['idPedidoContratacao'],$pedido['parcelas']);	
			}else{
				$valorTotal = $pedido['valor'];
				$formaPagamento = $pedido['formaPagamento'];
			}

			$x[$i]['id']= $pedido['idPedidoContratacao'];
			$x[$i]['objeto'] = retornaTipo($evento['ig_tipo_evento_idTipoEvento'])." - ".$evento['nomeEvento'];
			if($pedido['tipoPessoa'] == 1){
				$pessoa = recuperaDados("sis_pessoa_fisica",$pedido['idPessoa'],"Id_PessoaFisica");
				$x[$i]['proponente'] = $pessoa['Nome'];
				$x[$i]['tipo'] = "Física";
			}else{
				$pessoa = recuperaDados("sis_pessoa_juridica",$pedido['idPessoa'],"Id_PessoaJuridica");
				$x[$i]['proponente'] = $pessoa['RazaoSocial'];
				$x[$i]['tipo'] = "Jurídica";

			}
			$x[$i]['local'] = substr($local,1);
			$x[$i]['instituicao'] = $instituicao['sigla'];
			$x[$i]['periodo'] = $periodo;
			$x[$i]['status'] = $pedido['estado'];	
			$x[$i]['operador'] = $operador['nomeCompleto'];		
			$i++;
			}
			}
			}
		}
		}
		$x['num'] = $i;
		



$mensagem = "Foram encontradas ".$x['num']." pedido(s) de contratação.";
?>
<br />
<br />
	<section id="list_items">
		<div class="container">
			 <h3>Resultado da busca</3>
             <h5>Foram encontrados <?php echo $x['num']; ?> pedidos de contratação.</h5>
             <h5><a href="?perfil=contratos&p=frm_busca">Fazer outra busca</a></h5>
			<div class="table-responsive list_info">
				<table class="table table-condensed">
					<thead>
						<tr class="list_menu">
							<td>Codigo do Pedido</td>
							<td>Proponente</td>
							<td>Tipo</td>
							<td>Objeto</td>
							<td width="20%">Local</td>
                            <td>Instituição</td>
							<td>Periodo</td>
							<td>Status</td>
   							<td>Operador</td>
						</tr>
					</thead>
					<tbody>
<?php

$data=date('Y');
for($h = 0; $h < $x['num']; $h++)
 {
	 $status = recuperaDados("sis_estado",$x[$h]['status'],"idEstado");
	if($x[$h]['tipo'] == 'Física'){
		echo "<tr><td class='lista'> <a href='?perfil=contratos&p=frm_edita_propostapf&id_ped=".$x[$h]['id']."'>".$x[$h]['id']."</a></td>";
	}else{
		echo "<tr><td class='lista'> <a href='?perfil=contratos&p=frm_edita_propostapj&id_ped=".$x[$h]['id']."'>".$x[$h]['id']."</a></td>";
		
	}
	echo '<td class="list_description">'.$x[$h]['proponente'].					'</td> ';
	echo '<td class="list_description">'.$x[$h]['tipo'].					'</td> ';
	echo '<td class="list_description">'.$x[$h]['objeto'].						'</td> ';
	echo '<td class="list_description">'.$x[$h]['local'].				'</td> ';
	echo '<td class="list_description">'.$x[$h]['instituicao'].				'</td> ';
	echo '<td class="list_description">'.$x[$h]['periodo'].						'</td> ';
	echo '<td class="list_description">'.$status['estado'].						'</td> ';
	echo '<td class="list_description">'.$x[$h]['operador'].						'</td> </tr>';

	}
?>
	
					
					</tbody>
				</table>
			</div>
		</div>
	</section>


	<?php
}


break;

} // fim da switch

 ?>





