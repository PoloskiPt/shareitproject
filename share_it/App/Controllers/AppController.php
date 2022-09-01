<?php

namespace App\Controllers;

//os recursos do MF
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {


	public function timeline(){
	
			$this->validaAuth();

			//recuperação dos posts

			$post = Container::getModel('Post');

			$post->__set('id_utilizador', $_SESSION['id']);


			//Variáveis de paginação

			$total_registos_pagina = 10;
			$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
			$deslocamento = ($pagina - 1) * $total_registos_pagina;


			$posts = $post->getPorPagina($total_registos_pagina, $deslocamento);
			$total_posts = $post->getTotalRegistos();

			$renderLike = '';

			$this->view->total_de_paginas = ceil(($total_posts['total'] / $total_registos_pagina));		
			$this->view->pagina_ativa = $pagina;

			$this->view->posts = $posts;

			//settar valores para utilizar
			$utilizador = Container::getModel('Utilizador');
			$utilizador->__set('id', $_SESSION['id']);

			$this->view->info_utilizador = $utilizador->getUserInfo();
			$this->view->numLikesPerPost = $post->getLikes();
			$this->view->total_posts = $utilizador->getTotalPosts();
			$this->view->total_a_seguir = $utilizador->getTotalSeguir();
			$this->view->total_seguidores = $utilizador->getTotalSeguidores();
			$this->view->renderLike = $post->renderLike();

			$this->render('timeline');
	
	}

	public function settings(){
		$this->validaAuth();
		$this->render('settings');

	}

	public function postView(){
		$this->render('postView');
	}

	public function like(){
		
		$this->validaAuth();
		//echo 'chegamos aqui';
		$post = Container::getModel('Post');
		
		$post->__set('id_utilizador', $_SESSION['id']);
		
		$post_id = isset($_GET['post_id']) ? $_GET['post_id'] : '' ;

		if(empty($post_id)){
			header('Location: /timeline');
		}

		$post = Container::getModel('Post');
		$post->__set('id', $post_id);
		$post->__set('id_utilizador', $_SESSION['id']);

		$post->gerirLike();

		header('Location: /timeline');
	}


		

	public function listarComentarios(){



		$this->validaAuth();
		$aux = $_POST['post'];
				
 		$comentario = Container::getModel('Comentario');
		$comentario->__set('id_post', $aux);
		$result = $comentario->getComentariosPorPost();	
		$response = ($result);	
		echo json_encode($result);
		
			
	}

	public function guardarComentario(){

		$this->validaAuth();

		if(!empty($_POST['content'])){

			$comentario = Container::getModel('Comentario');
			$comentario->__set('content', $_POST['content']);
			$comentario->__set('id_post',$_GET['id_post']);
			$comentario->__set('id_utilizador',$_SESSION['id']);

			$comentario->guardarComentario();
			$this->view->msg = "Sucesso";
		}
		 if(empty($_POST['content'])) {
		 	$this->view->msg = "Insira um comentário válido.";
		 }
			header('Location: /timeline');

		
	}

	public function alterarPost(){
		
		$this->validaAuth();

		if(!empty($_POST['postTexto']) && !empty($_GET['id'])){

			$post = Container::getModel('Post');
			$post->__set('id', $_GET['id']);
			$post->__set('post',$_POST['postTexto']);
			$post->alterarPost();
			
		}
			header('Location: /timeline');

	}

	public function post(){
		
			$this->validaAuth();
			$post = Container::getModel('Post');

			if(!empty($_POST['post-content'])){
				$post->__set('post',$_POST['post-content']);
				$post->__set('id_utilizador', $_SESSION['id']);
				$post->guardar();
			}
				header('Location: /timeline');

	}

	public function removePost(){

		//verificar sessao do utilizador e eliminar o id se o mesmo corresponder ao id do utilizador autenticado

		$this->validaAuth();
		
		$post_id = isset($_GET['post_id']) ? $_GET['post_id'] : '' ;
	
		if(empty($post_id)){
			header('Location: /timeline');
		}
		
		$post = Container::getModel('Post');
		$post->__set('id', $post_id);
		$post->__set('id_utilizador', $_SESSION['id']);
		$post->removerPost();
		header('Location: /timeline');
	}

	public function validaAuth(){

		session_start();

		if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == ''){
			header('Location: /?login=erro');
		}
				
	}

	public function gerarListagemSeguidores(){

		$this->validaAuth();

		
		$tipo = $_GET['tipo'];

		if(empty($tipo)){
			header('Location: /timeline');
		}

		$this->validaAuth();

		$pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

		//echo 'A procura de: ' .$pesquisarPor;

		//instanciar a variavel para não dar erro
		//não pode ser gerada apenas dentro da condiçao pois gera warning por estar a atribuir 'empty'.
		$utilizadores = array();

		if($pesquisarPor != ''){

			$utilizador = Container::getModel('Utilizador');
			$utilizador->__set('nome', $pesquisarPor);
			$utilizador->__set('id', $_SESSION['id']);
			$utilizadores = $utilizador->getAll();
		}

		$this->view->utilizadores = $utilizadores;
		
		//settar valores para utilizar
			$utilizadorLogado = Container::getModel('Utilizador');
			$utilizadorLogado->__set('id', $_SESSION['id']);

			$this->view->info_utilizador = $utilizadorLogado->getUserInfo();

			$this->view->total_posts = $utilizadorLogado->getTotalPosts();

			$this->view->total_a_seguir = $utilizadorLogado->getTotalSeguir();

			$this->view->total_seguidores = $utilizadorLogado->getTotalSeguidores();
		$this->render('listagemSeguidores');

	}

	public function gerarListagemPosts(){

		$this->validaAuth();

		$post = Container::getModel('post');
		$post->__set('id_utilizador', $_SESSION['id']);
		$posts = $post->listarPosts();
		
		$this->view->posts = $posts;

		$utilizador = Container::getModel('Utilizador');
			$utilizador->__set('id', $_SESSION['id']);

			$this->view->info_utilizador = $utilizador->getUserInfo();

			$this->view->total_posts = $utilizador->getTotalPosts();

			$this->view->total_a_seguir = $utilizador->getTotalSeguir();

			$this->view->total_seguidores = $utilizador->getTotalSeguidores();

		$this->render('listagemPosts');
		
	
	}

	public function quemSeguir(){

		$this->validaAuth();

		$pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

		//echo 'A procura de: ' .$pesquisarPor;

		//instanciar a variavel para não dar erro
		//não pode ser gerada apenas dentro da condiçao pois gera warning por estar a atribuir 'empty'.
		$utilizadores = array();

		if($pesquisarPor != ''){

			$utilizador = Container::getModel('Utilizador');
			$utilizador->__set('nome', $pesquisarPor);
			$utilizador->__set('id', $_SESSION['id']);
			$utilizadores = $utilizador->getAll();
		}

		$this->view->utilizadores = $utilizadores;
		
		//settar valores para utilizar
			$utilizadorLogado = Container::getModel('Utilizador');
			$utilizadorLogado->__set('id', $_SESSION['id']);

			$this->view->info_utilizador = $utilizadorLogado->getUserInfo();

			$this->view->total_posts = $utilizadorLogado->getTotalPosts();

			$this->view->total_a_seguir = $utilizadorLogado->getTotalSeguir();

			$this->view->total_seguidores = $utilizadorLogado->getTotalSeguidores();


		$this->render('quemSeguir');
	}

	public function acao(){

		$this->validaAuth();

		$acao = isset($_GET['acao']) ? $_GET['acao'] : '' ;
		$id_utilizador_follow = isset($_GET['id_utilizador']) ? $_GET['id_utilizador'] : '';	
		//criar um novo modelo? - ver se tiver tempo
		$utilizador = Container::getModel('Utilizador');
		$utilizador->__set('id', $_SESSION['id']);

		if($acao == 'follow'){
			$utilizador->followUtilizador($id_utilizador_follow);
		}else if($acao == 'unfollow'){
			$utilizador->unfollowUtilizador($id_utilizador_follow);
		}

		header('Location: /quem_seguir');

	}

}

?>