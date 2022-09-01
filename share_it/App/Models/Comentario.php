<?php
namespace App\Models;

use MF\Model\Model;

class Comentario extends Model {

	private $id;
	private $id_utilizador;
	private $id_post;
	private $content;
	private $data;


	public function __get($atributo){
		return $this->$atributo;
	}

	public function __set($atributo, $valor){
		 $this->$atributo = $valor;
	}
	public function guardarComentario(){

		//guardar comentario
	$query = "insert into comentarios(id_post, id_utilizador, comentario) values (?, ?, ?)";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id_post'));
		$stmt->bindValue(2, $this->__get('id_utilizador'));
		$stmt->bindValue(3, $this->__get('content'));
		$stmt->execute();

		return $this;

	}



	public function getComentariosPorPost(){

		$query = "select c.id,u.nome ,c.comentario, DATE_FORMAT(c.data_criacao, '%d/%m/%Y %H:%i') as data 
		from comentarios as c left join posts as p on (c.id_post = p.id) 
		join utilizadores as u on (c.id_utilizador = u.id)
		where p.id = ? order by p.data desc;";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id_post'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	
}

?>