<?php
//geram o insert pro framework da igsis
$pasta = "?perfil=contratos&p=";
$usuario = recuperaDados("ig_usuario",$_SESSION['idUsuario'],"idUsuario");
 ?>


<div class="menu-area">
  <div id="dl-menu" class="dl-menuwrapper">
	<button class="dl-trigger">Open Menu</button>
	<ul class="dl-menu">
		<li><a href="#">Cadastro de Pessoas</a>
			<ul class="dl-submenu">
				<li><a href="<?php echo $pasta ?>frm_lista_pf">Pessoa Física</a></li>
				<li><a href="<?php echo $pasta ?>frm_lista_pj">Pessoa Jurídica</a></li>
			</ul>
        </li>
        <!--
		<li><a href="#">Contratos Pessoa Física</a>
			<ul class="dl-submenu">
				<li><a href="<?php echo $pasta ?>frm_lista_pedidocontratacaopf">Pedido de Contratação</a></li>
				<li><a href="<?php echo $pasta ?>frm_listapedidocontratacaopf_cadastraprocesso">Processo</a></li>
				<li><a href="<?php echo $pasta ?>frm_lista_propostapf">Listar Todos</a></li>
			</ul>
		</li>
		<li><a href="#">Contratos Pessoa Jurídica</a>
			<ul class="dl-submenu">
				<li><a href="<?php echo $pasta ?>frm_lista_pedidocontratacaopj">Pedido de Contratação</a></li>
				<li><a href="<?php echo $pasta ?>frm_listapedidocontratacaopj_cadastraprocesso">Processo</a></li>
				<li><a href="<?php echo $pasta ?>frm_lista_propostapj">Listar Todos</a></li>
			</ul>
		</li>
        -->
        <li><a href="<?php echo $pasta ?>frm_sem_operador">Sem Operador</a></li>
		<li><a href="<?php echo $pasta ?>frm_busca">Contratos</a></li>
  		<li><a href="<?php echo $pasta ?>frm_busca_periodo">Contratos por período</a></li>
		<li><a href="<?php echo $pasta ?>frm_busca_dataenvio">Contratos por data de envio</a></li>
        <li><a href="<?php echo $pasta ?>frm_lista_pedidocontratacao_pf&enviados=1">Formação</a></li>
        <!--
        <?php if($usuario['contratos'] == 2){ ?>
					<li><a href="<?php echo $pasta ?>frm_reabertura">Reabertura de Eventos/Pedidos</a></li>
        <?php } ?>
		-->
        <li><a href="<?php echo $pasta ?>frm_chamados">Chamados</a></li>
  		<li style="color:white;">-------------------------</li>
        <li><a href="index.php?secao=perfil">Carregar módulos</a></li>
		<li><a href="<?php echo $pasta ?>ajuda">Ajuda</a></li>
		<li><a href="../index.php">Sair</a></li>
			</ul>
  </div>
</div>	
