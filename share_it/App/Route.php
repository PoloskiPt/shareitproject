<?php

namespace App;

use MF\Init\Bootstrap;

class Route extends Bootstrap {

	protected function initRoutes() {

		$routes['home'] = array(
			'route' => '/',
			'controller' => 'indexController',
			'action' => 'index'
		);

		$routes['inscreverse'] = array(
			'route' => '/inscreverse',
			'controller' => 'indexController',
			'action' => 'inscreverse'
		);

		$routes['registar'] = array(
			'route' => '/registar',
			'controller' => 'indexController',
			'action' => 'registar'
		);

		$routes['autenticar'] = array(
			'route' => '/autenticar',
			'controller' => 'AuthController',
			'action' => 'autenticar'
		);

		$routes['timeline'] = array(
			'route' => '/timeline',
			'controller' => 'AppController',
			'action' => 'timeline'
		);

		$routes['sair'] = array(
			'route' => '/sair',
			'controller' => 'AuthController',
			'action' => 'sair'
		);

		$routes['post'] = array(
			'route' => '/post',
			'controller' => 'AppController',
			'action' => 'post'
		);

		$routes['quem_seguir'] = array(
			'route' => '/quem_seguir',
			'controller' => 'AppController',
			'action' => 'quemSeguir'
		);

		$routes['acao'] = array(
			'route' => '/acao',
			'controller' => 'AppController',
			'action' => 'acao'
		);

		$routes['remove_post'] = array(
			'route' => '/remove_post',
			'controller' => 'AppController',
			'action' => 'removePost'
		);

		$routes['listagem_posts'] = array(
			'route' => '/listagem_posts',
			'controller' => 'AppController',
			'action' => 'gerarListagemPosts'
		);

		$routes['listagem_seguidores'] = array(
			'route' => '/listagem_seguidores',
			'controller' => 'AppController',
			'action' => 'gerarListagemSeguidores'
		);

		$routes['like'] = array(
			'route' => '/like',
			'controller' => 'AppController',
			'action' => 'like'
		);


		$routes['comentar'] = array(
			'route' => '/comentar',
			'controller' => 'AppController',
			'action' => 'guardarComentario'
		);

		$routes['post_view'] = array(
			'route' => '/post_view',
			'controller' => 'AppController',
			'action' => 'postView'
		);

		$routes['settings'] = array(
			'route' => '/settings',
			'controller' => 'AppController',
			'action' => 'settings'
		);

		$routes['alterar_dados'] = array(
			'route' => '/alterar_dados',
			'controller' => 'AuthController',
			'action' => 'alterarDados'
		);

		$routes['eliminar_conta'] = array(
			'route' => '/eliminar_conta',
			'controller' => 'AuthController',
			'action' => 'eliminarConta'
		);

		$routes['alterar_post'] = array(
			'route' => '/alterar_post',
			'controller' => 'AppController',
			'action' => 'alterarPost'
		);

		$routes['listar_comentarios'] = array(
			'route' => '/listar_comentarios',
			'controller' => 'AppController',
			'action' => 'listarComentarios'
		);

			$routes['ativar_conta'] = array(
			'route' => '/ativar_conta',
			'controller' => 'indexController',
			'action' => 'ativarConta'
		);




		$this->setRoutes($routes);
	}

}

?>